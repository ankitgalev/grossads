<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Imports\BulkLeadsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;

class BulkLeadImportController extends Controller
{
    /**
     * Bulk upload page.
     */
    public function index()
    {
        $campaigns = Campaign::orderBy('name')
            ->get(['id', 'name']);

        return view('admin.leads.bulk_upload', compact('campaigns'));
    }

    /**
     * Get publishers assigned to selected campaign.
     */
    public function publishers($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);

        $publishers = $campaign->publishers()
            ->select('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();

        return response()->json($publishers);
    }

    /**
     * Import CSV / Excel leads.
     */
    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'publisher_id' => 'required|integer|exists:users,id',
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $campaign = Campaign::findOrFail($request->campaign_id);

        // Verify publisher is assigned to this campaign.
        $publisherAssigned = $campaign->publishers()
            ->where('users.id', $request->publisher_id)
            ->exists();

        if (!$publisherAssigned) {
            throw ValidationException::withMessages([
                'publisher_id' => 'Selected publisher is not assigned to this campaign.'
            ]);
        }

        $import = new BulkLeadsImport(
            $campaign,
            $request->publisher_id
        );

        Excel::import($import, $request->file('file'));

        return redirect()
            ->route('admin.leads.bulk-upload.index')
            ->with('success', 'Bulk import completed successfully!')
            ->with('imported_count', $import->importedCount)
            ->with('duplicate_count', $import->duplicateCount)
            ->with('skipped_count', $import->skippedCount)
            ->with('import_errors', $import->errors);
    }
}
