<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Advertiser;
use App\Models\Campaign;
use App\Models\Lead;

class AdvertiserDashboardControllers extends Controller
{
    /**
     * Advertiser dashboard
     */
    public function index()
    {
        $userId = Auth::id();

        // Get the profile linked to the logged-in advertiser
        $advertiser = Advertiser::where('user_id', $userId)->firstOrFail();

        // Only this advertiser's campaigns
        $campaignQuery = Campaign::where('advertiser_id', $advertiser->id);

        $totalCampaigns = (clone $campaignQuery)->count();
        $activeCampaigns = (clone $campaignQuery)
            ->where('status', 'active')
            ->count();

        // Only this advertiser's leads
        $leadQuery = Lead::where('advertiser_id', $advertiser->id);

        $totalLeads = (clone $leadQuery)->count();

        $todayLeads = (clone $leadQuery)
            ->whereDate('created_at', date('Y-m-d'))
            ->count();

        $newLeads = (clone $leadQuery)
            ->where('status', 'new')
            ->count();

        $approvedLeads = (clone $leadQuery)
            ->where('status', 'approved')
            ->count();

        $rejectedLeads = (clone $leadQuery)
            ->where('status', 'rejected')
            ->count();

        $campaigns = (clone $campaignQuery)
            ->withCount('leads')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        $recentLeads = (clone $leadQuery)
            ->with(['campaign', 'publisher'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view('advertiser.dashboard', compact(
            'advertiser',
            'totalCampaigns',
            'activeCampaigns',
            'totalLeads',
            'todayLeads',
            'newLeads',
            'approvedLeads',
            'rejectedLeads',
            'campaigns',
            'recentLeads'
        ));
    }

    /**
     * Advertiser's campaigns
     */
    public function campaigns()
    {
        $advertiser = Advertiser::where('user_id', Auth::id())
            ->firstOrFail();

        $campaigns = Campaign::where('advertiser_id', $advertiser->id)
            ->withCount('leads')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('advertiser.campaigns', compact(
            'advertiser',
            'campaigns'
        ));
    }

    /**
     * Advertiser's leads
     */
    public function leads(Request $request)
    {
        $advertiser = Advertiser::where('user_id', Auth::id())
            ->firstOrFail();

        $query = Lead::where('advertiser_id', $advertiser->id)
            ->with(['campaign', 'publisher']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $leads = $query->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $campaigns = Campaign::where('advertiser_id', $advertiser->id)
            ->orderBy('name')
            ->get();

        return view('advertiser.leads', compact(
            'advertiser',
            'leads',
            'campaigns'
        ));
    }
    
    /**
     * View details of advertiser's own lead
     */
    public function leadDetails($id)
    {
        $advertiser = \App\Models\Advertiser::where(
            'user_id',
            \Illuminate\Support\Facades\Auth::id()
        )->firstOrFail();

        $lead = \App\Models\Lead::with([
            'campaign.fields',
            'publisher',
            'advertiser',
            'values.field'
        ])
        ->where('advertiser_id', $advertiser->id)
        ->where('id', $id)
        ->firstOrFail();

        return view('advertiser.lead-details', compact(
            'advertiser',
            'lead'
        ));
    }

}