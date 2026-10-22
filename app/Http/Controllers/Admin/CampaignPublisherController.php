<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;

class CampaignPublisherController extends Controller
{
    public function create($campaignId)
    {
        $campaign = Campaign::with(
            'publishers'
        )->findOrFail($campaignId);


        $publishers = User::where(
            'role',
            'publisher'
        )
        ->where('status', 1)
        ->orderBy('name')
        ->get();


        return view(
            'admin.campaigns.assign',
            compact(
                'campaign',
                'publishers'
            )
        );
    }


    public function store(
        Request $request,
        $campaignId
    ) {

        $campaign =
            Campaign::findOrFail(
                $campaignId
            );


        $request->validate([

            'publishers' =>
                'required|array|min:1',

            'publishers.*' =>
                'integer|exists:users,id',

            'publisher_payout' =>
                'nullable|numeric|min:0',

            'daily_limit' =>
                'nullable|integer|min:0',

            'total_limit' =>
                'nullable|integer|min:0',

        ]);


        foreach (
            $request->publishers
            as $publisherId
        ) {

            $campaign->publishers()->syncWithoutDetaching([

                $publisherId => [

                    'publisher_payout' =>
                        $request->publisher_payout ?? 0,

                    'daily_limit' =>
                        $request->daily_limit ?? 0,

                    'total_limit' =>
                        $request->total_limit ?? 0,

                    'status' =>
                        1,

                ]

            ]);

        }


        return redirect()
            ->route(
                'admin.campaigns.show',
                $campaign->id
            )
            ->with(
                'success',
                'Publisher assigned successfully.'
            );
    }


    public function destroy(
        $campaignId,
        $publisherId
    ) {

        $campaign =
            Campaign::findOrFail(
                $campaignId
            );


        $campaign->publishers()
            ->detach($publisherId);


        return back()->with(
            'success',
            'Publisher removed from campaign.'
        );
    }
}