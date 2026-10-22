<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignPublisher;
use App\Models\MetaIntegration;
use App\Models\User;
use Illuminate\Http\Request;

class MetaIntegrationController extends Controller
{
    public function index()
    {
        $integrations = MetaIntegration::with([
            'campaign',
            'publisher',
            'campaignPublisher'
        ])->orderBy('id', 'desc')->paginate(15);

        return view('admin.meta-integrations.index', compact('integrations'));
    }

    public function create()
    {
        $campaigns = Campaign::orderBy('name')->get();

        $publishers = User::where('role', 'publisher')
            ->orderBy('name')
            ->get();

        $assignments = CampaignPublisher::with(['campaign', 'publisher'])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.meta-integrations.create', compact(
            'campaigns',
            'publishers',
            'assignments'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'page_id' => 'required|string|max:100',
            'form_id' => 'nullable|string|max:100',
            'campaign_id' => 'required|exists:campaigns,id',
            'publisher_id' => 'required|exists:users,id',
            'campaign_publisher_id' => 'nullable|exists:campaign_publishers,id',
            'app_secret' => 'required|string|max:2000',
            'page_access_token' => 'required|string|max:5000',
            'verify_token' => 'required|string|max:255',
        ]);

        $publisher = User::where('id', $request->publisher_id)
            ->where('role', 'publisher')
            ->first();

        if (!$publisher) {
            return back()->withInput()->withErrors([
                'publisher_id' => 'Please select a valid publisher account.'
            ]);
        }

        if ($request->filled('campaign_publisher_id')) {
            $assignment = CampaignPublisher::where(
                'id',
                $request->campaign_publisher_id
            )
                ->where('campaign_id', $request->campaign_id)
                ->where('publisher_id', $request->publisher_id)
                ->first();

            if (!$assignment) {
                return back()->withInput()->withErrors([
                    'campaign_publisher_id' =>
                        'Selected assignment does not match the campaign and publisher.'
                ]);
            }
        }

        $integration = new MetaIntegration();
        $integration->name = $request->name;
        $integration->page_id = $request->page_id;
        $integration->form_id = $request->form_id;
        $integration->campaign_id = $request->campaign_id;
        $integration->publisher_id = $request->publisher_id;
        $integration->campaign_publisher_id =
            $request->campaign_publisher_id ?: null;

        // Meta credentials are encrypted by MetaIntegration model accessors.
        $integration->app_secret = $request->app_secret;
        $integration->page_access_token = $request->page_access_token;
        $integration->verify_token = $request->verify_token;
        $integration->status = false;
        $integration->save();

        return redirect()
            ->route('admin.meta-integrations.index')
            ->with('success', 'Meta integration saved. It is disabled until webhook setup is complete.');
    }

    public function toggle($id)
    {
        $integration = MetaIntegration::findOrFail($id);
        $integration->status = !$integration->status;
        $integration->save();

        return redirect()
            ->route('admin.meta-integrations.index')
            ->with('success', 'Integration status updated.');
    }
}