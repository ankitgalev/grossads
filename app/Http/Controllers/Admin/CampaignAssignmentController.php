<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignPublisher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CampaignAssignmentController extends Controller
{
    /**
     * Show publishers assigned to a campaign.
     */
    public function index($campaignId)
    {
        $campaign = Campaign::with([
            'advertiser',
            'publishers'
        ])->findOrFail($campaignId);

        return view(
            'admin.campaigns.assign',
            compact('campaign')
        );
    }

    /**
     * Assign one or more publishers.
     */
    public function store(Request $request, $campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);

        $validated = $request->validate([
            'publishers' => 'required|array|min:1',
            'publishers.*.publisher_id' => 'required|integer|distinct|exists:users,id',
            'publishers.*.publisher_payout' => 'required|numeric|min:0',
            'publishers.*.daily_limit' => 'required|integer|min:0',
            'publishers.*.total_limit' => 'required|integer|min:0',
        ]);

        // Validate that the selected users are actually publishers.
        // This assumes users.role contains "publisher".
        $publisherIds = collect($validated['publishers'])
            ->pluck('publisher_id')
            ->unique()
            ->values();

        $validPublisherIds = User::whereIn('id', $publisherIds)
            ->where('role', 'publisher')
            ->pluck('id')
            ->map(function ($id) {
                return (int) $id;
            });

        if ($validPublisherIds->count() !== $publisherIds->count()) {
            return back()
                ->withInput()
                ->withErrors([
                    'publishers' => 'One or more selected users are not publishers.'
                ]);
        }

        DB::transaction(function () use (
            $validated,
            $campaign,
            $validPublisherIds
        ) {
            foreach ($validated['publishers'] as $row) {
                $publisherId = (int) $row['publisher_id'];

                if (!$validPublisherIds->contains($publisherId)) {
                    continue;
                }

                $assignment = CampaignPublisher::where(
                    'campaign_id',
                    $campaign->id
                )->where(
                    'publisher_id',
                    $publisherId
                )->first();

                $data = [
                    'publisher_payout' => $row['publisher_payout'],
                    'daily_limit' => $row['daily_limit'],
                    'total_limit' => $row['total_limit'],
                    'status' => 1,
                ];

                if (!$assignment) {
                    $data['api_key'] = 'GA_' . Str::random(48);
                    $data['assigned_at'] = now();

                    CampaignPublisher::create(array_merge(
                        $data,
                        [
                            'campaign_id' => $campaign->id,
                            'publisher_id' => $publisherId,
                        ]
                    ));
                } else {
                    // Preserve the existing API key on reassignment.
                    $assignment->update($data);
                }
            }
        });

        return redirect()
            ->route('admin.campaigns.assign', $campaign->id)
            ->with('success', 'Publisher assignments saved successfully.');
    }

    /**
     * Enable/disable an assignment without deleting its API key.
     */
    public function toggleStatus($campaignId, $assignmentId)
    {
        $assignment = CampaignPublisher::where(
            'campaign_id',
            $campaignId
        )->findOrFail($assignmentId);

        $assignment->status = $assignment->status ? 0 : 1;
        $assignment->save();

        return back()->with(
            'success',
            'Publisher assignment status updated.'
        );
    }
}
