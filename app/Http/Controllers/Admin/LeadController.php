<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\LeadsExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->input('tab', 'all');

        if (!in_array($activeTab, ['all', 'duplicate', 'junk'])) {
            $activeTab = 'all';
        }

        $leads = $this->filteredLeadsQuery($request)
            ->paginate(20)
            ->appends($request->query());

        $fieldNames = $leads->getCollection()
            ->flatMap(function ($lead) {
                return $lead->values->map(function ($value) {
                    return optional($value->field)->field_name;
                });
            })
            ->filter()
            ->unique()
            ->values();

        $campaigns = Campaign::orderBy('name')->get();

        $publishers = User::where('role', 'publisher')
            ->orderBy('name')
            ->get();

        // Overall lead statistics
        $totalLeads = Lead::count();
        $newLeads = Lead::where('status', 'new')->count();
        $approvedLeads = Lead::where('status', 'approved')->count();
        $rejectedLeads = Lead::where('status', 'rejected')->count();

        // Duplicate and junk counts
        $duplicateLeads = Lead::where('is_duplicate', 1)->count();
        $junkLeads = Lead::where('is_junk', 1)->count();

        return view('admin.leads.index', compact(
            'leads',
            'campaigns',
            'publishers',
            'totalLeads',
            'newLeads',
            'approvedLeads',
            'rejectedLeads',
            'fieldNames',
            'activeTab',
            'duplicateLeads',
            'junkLeads'
        ));
    }

    public function show($id)
    {
        $lead = Lead::with([
            'campaign.fields',
            'publisher',
            'advertiser',
            'values.field',
            'duplicateOf'
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

    // Excel and CSV export
    public function export(Request $request, $format)
    {
        if (!in_array($format, ['excel', 'csv'])) {
            abort(404);
        }

        $leads = $this->filteredLeadsQuery($request)->get();

        $fieldNames = $leads->flatMap(function ($lead) {
            return $lead->values->map(function ($value) {
                return optional($value->field)->field_name;
            });
        })->filter()->unique()->values();

        $export = new LeadsExport($leads, $fieldNames);

        $fileName = 'grossads-leads-' . date('Y-m-d-His');

        if ($format === 'csv') {
            return Excel::download(
                $export,
                $fileName . '.csv',
                ExcelFormat::CSV
            );
        }

        return Excel::download(
            $export,
            $fileName . '.xlsx',
            ExcelFormat::XLSX
        );
    }

    // Common query for listing and export filters.
    private function filteredLeadsQuery(Request $request)
    {
        $query = Lead::with([
            'campaign',
            'publisher',
            'advertiser',
            'values.field'
        ]);

        // All / Duplicate / Junk tab filter
        $tab = $request->input('tab', 'all');

        if ($tab === 'duplicate') {
            $query->where('is_duplicate', 1);
        } elseif ($tab === 'junk') {
            $query->where('is_junk', 1);
        }

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

        return $query->orderBy('id', 'desc');
    }
}

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Models\Lead;
// use App\Models\Campaign;
// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Validation\Rule;
// use App\Exports\LeadsExport;
// use Maatwebsite\Excel\Facades\Excel;
// use Maatwebsite\Excel\Excel as ExcelFormat;

// class LeadController extends Controller
// {
//     // public function index(Request $request)
//     // {
//     //     $query = Lead::with([
//     //         'campaign',
//     //         'publisher',
//     //         'advertiser',
//     //         'values.field'
//     //     ]);

//     //     // Search by lead reference, campaign or publisher.
//     //     if ($request->filled('search')) {
//     //         $search = trim($request->input('search'));

//     //         $query->where(function ($q) use ($search) {
//     //             $q->where('lead_reference', 'like', '%' . $search . '%')
//     //               ->orWhereHas('campaign', function ($campaignQuery) use ($search) {
//     //                   $campaignQuery->where('name', 'like', '%' . $search . '%');
//     //               })
//     //               ->orWhereHas('publisher', function ($publisherQuery) use ($search) {
//     //                   $publisherQuery->where('name', 'like', '%' . $search . '%')
//     //                       ->orWhere('email', 'like', '%' . $search . '%');
//     //               });
//     //         });
//     //     }

//     //     if ($request->filled('campaign_id')) {
//     //         $query->where('campaign_id', $request->campaign_id);
//     //     }

//     //     if ($request->filled('publisher_id')) {
//     //         $query->where('publisher_id', $request->publisher_id);
//     //     }

//     //     if ($request->filled('status')) {
//     //         $query->where('status', $request->status);
//     //     }

//     //     if ($request->filled('date_from')) {
//     //         $query->whereDate('created_at', '>=', $request->date_from);
//     //     }

//     //     if ($request->filled('date_to')) {
//     //         $query->whereDate('created_at', '<=', $request->date_to);
//     //     }

//     //     $leads = $query->orderBy('id', 'desc')
//     //         ->paginate(20)
//     //         ->appends($request->query());

//     //     $campaigns = Campaign::orderBy('name')->get();

//     //     $publishers = User::where('role', 'publisher')
//     //         ->orderBy('name')
//     //         ->get();

//     //     $totalLeads = Lead::count();
//     //     $newLeads = Lead::where('status', 'new')->count();
//     //     $approvedLeads = Lead::where('status', 'approved')->count();
//     //     $rejectedLeads = Lead::where('status', 'rejected')->count();

//     //     return view('admin.leads.index', compact(
//     //         'leads',
//     //         'campaigns',
//     //         'publishers',
//     //         'totalLeads',
//     //         'newLeads',
//     //         'approvedLeads',
//     //         'rejectedLeads'
//     //     ));
//     // }
//     public function index(Request $request)
//     {
//         $leads = $this->filteredLeadsQuery($request)
//             ->paginate(20)
//             ->appends($request->query());

//         // Current page par available dynamic field names.
//         $fieldNames = $leads->getCollection()
//             ->flatMap(function ($lead) {
//                 return $lead->values->map(function ($value) {
//                     return optional($value->field)->field_name;
//                 });
//             })
//             ->filter()
//             ->unique()
//             ->values();

//         $campaigns = Campaign::orderBy('name')->get();

//         $publishers = User::where('role', 'publisher')
//             ->orderBy('name')
//             ->get();

//         $totalLeads = Lead::count();
//         $newLeads = Lead::where('status', 'new')->count();
//         $approvedLeads = Lead::where('status', 'approved')->count();
//         $rejectedLeads = Lead::where('status', 'rejected')->count();

//         return view('admin.leads.index', compact(
//             'leads',
//             'campaigns',
//             'publishers',
//             'totalLeads',
//             'newLeads',
//             'approvedLeads',
//             'rejectedLeads',
//             'fieldNames'
//         ));
//     }


//     public function show($id)
//     {
//         $lead = Lead::with([
//             'campaign.fields',
//             'publisher',
//             'advertiser',
//             'values.field'
//         ])->findOrFail($id);

//         return view('admin.leads.show', compact('lead'));
//     }

//     public function updateStatus(Request $request, $id)
//     {
//         $request->validate([
//             'status' => [
//                 'required',
//                 Rule::in(['new', 'approved', 'rejected'])
//             ]
//         ]);

//         $lead = Lead::findOrFail($id);
//         $lead->status = $request->status;
//         $lead->save();

//         return redirect()
//             ->route('admin.leads.index')
//             ->with('success', 'Lead status updated successfully.');
//     }
//     // Excel and CSV export
//     public function export(Request $request, $format)
//     {
//         if (!in_array($format, ['excel', 'csv'])) {
//             abort(404);
//         }

//         // Export all matching records, not just the current page.
//         $leads = $this->filteredLeadsQuery($request)->get();

//         $fieldNames = $leads->flatMap(function ($lead) {
//             return $lead->values->map(function ($value) {
//                 return optional($value->field)->field_name;
//             });
//         })->filter()->unique()->values();

//         $export = new LeadsExport($leads, $fieldNames);

//         $fileName = 'grossads-leads-' . date('Y-m-d-His');

//         if ($format === 'csv') {
//             return Excel::download(
//                 $export,
//                 $fileName . '.csv',
//                 ExcelFormat::CSV
//             );
//         }

//         return Excel::download(
//             $export,
//             $fileName . '.xlsx',
//             ExcelFormat::XLSX
//         );
//     }

//     // Common query for listing and export filters.
//     private function filteredLeadsQuery(Request $request)
//     {
//         $query = Lead::with([
//             'campaign',
//             'publisher',
//             'advertiser',
//             'values.field'
//         ]);

//         if ($request->filled('search')) {
//             $search = trim($request->input('search'));

//             $query->where(function ($q) use ($search) {
//                 $q->where('lead_reference', 'like', '%' . $search . '%')
//                     ->orWhereHas('campaign', function ($campaignQuery) use ($search) {
//                         $campaignQuery->where('name', 'like', '%' . $search . '%');
//                     })
//                     ->orWhereHas('publisher', function ($publisherQuery) use ($search) {
//                         $publisherQuery->where('name', 'like', '%' . $search . '%')
//                             ->orWhere('email', 'like', '%' . $search . '%');
//                     });
//             });
//         }

//         if ($request->filled('campaign_id')) {
//             $query->where('campaign_id', $request->campaign_id);
//         }

//         if ($request->filled('publisher_id')) {
//             $query->where('publisher_id', $request->publisher_id);
//         }

//         if ($request->filled('status')) {
//             $query->where('status', $request->status);
//         }

//         if ($request->filled('date_from')) {
//             $query->whereDate('created_at', '>=', $request->date_from);
//         }

//         if ($request->filled('date_to')) {
//             $query->whereDate('created_at', '<=', $request->date_to);
//         }

//         return $query->orderBy('id', 'desc');
//     }
// } 
