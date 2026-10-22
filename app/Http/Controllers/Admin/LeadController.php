<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with([
            'campaign',
            'publisher',
            'advertiser'
        ]);

        // Search by lead reference, campaign or publisher.
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('lead_reference', 'like', '%' . $search . '%')
                  ->orWhereHas('campaign', function ($campaignQuery) use ($search) {
                      $campaignQuery->where('name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('publisher', function ($publisherQuery) use ($search) {
                      $publisherQuery->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->filled('publisher_id')) {
            $query->where('publisher_id', $request->publisher_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $leads = $query->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $campaigns = Campaign::orderBy('name')->get();

        $publishers = User::where('role', 'publisher')
            ->orderBy('name')
            ->get();

        $totalLeads = Lead::count();
        $newLeads = Lead::where('status', 'new')->count();
        $approvedLeads = Lead::where('status', 'approved')->count();
        $rejectedLeads = Lead::where('status', 'rejected')->count();

        return view('admin.leads.index', compact(
            'leads',
            'campaigns',
            'publishers',
            'totalLeads',
            'newLeads',
            'approvedLeads',
            'rejectedLeads'
        ));
    }

    public function show($id)
    {
        $lead = Lead::with([
            'campaign.fields',
            'publisher',
            'advertiser',
            'values.field'
        ])->findOrFail($id);

        return view('admin.leads.show', compact('lead'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => [
                'required',
                Rule::in(['new', 'approved', 'rejected'])
            ]
        ]);

        $lead = Lead::findOrFail($id);
        $lead->status = $request->status;
        $lead->save();

        return redirect()
            ->route('admin.leads.index')
            ->with('success', 'Lead status updated successfully.');
    }
}

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\Campaign;
// use App\Models\Lead;
// use Illuminate\Http\Request;

// class LeadController extends Controller
// {
//     public function index(Request $request)
//     {
//         $query = Lead::with([
//             'campaign',
//             'values.field'
//         ]);


//         if ($request->filled('campaign_id')) {

//             $query->where(
//                 'campaign_id',
//                 $request->campaign_id
//             );
//         }


//         if ($request->filled('status')) {

//             $query->where(
//                 'status',
//                 $request->status
//             );
//         }


//         if ($request->filled('lead_reference')) {

//             $query->where(
//                 'lead_reference',
//                 'like',
//                 '%' .
//                 $request->lead_reference .
//                 '%'
//             );
//         }


//         $leads = $query
//             ->latest()
//             ->paginate(25)
//             ->withQueryString();


//         $campaigns =
//             Campaign::orderBy('name')
//                 ->get();


//         return view(
//             'admin.leads.index',
//             compact(
//                 'leads',
//                 'campaigns'
//             )
//         );
//     }


//     public function show($id)
//     {
//         $lead = Lead::with([
//             'campaign',
//             'values.field'
//         ])->findOrFail($id);


//         return view(
//             'admin.leads.show',
//             compact('lead')
//         );
//     }


//     public function updateStatus(
//         Request $request,
//         $id
//     ) {

//         $request->validate([

//             'status' => [
//                 'required',
//                 'in:new,accepted,rejected,duplicate,processing,converted,closed'
//             ]

//         ]);


//         $lead =
//             Lead::findOrFail($id);


//         $lead->update([

//             'status' =>
//                 $request->status

//         ]);


//         return back()->with(
//             'success',
//             'Lead status updated successfully.'
//         );
//     }


//     public function apiDocs()
//     {
//         $campaigns =
//             Campaign::with('fields')
//                 ->where('status', 'active')
//                 ->orderBy('name')
//                 ->get();


//         return view(
//             'admin.leads.api-docs',
//             compact('campaigns')
//         );
//     }
// }