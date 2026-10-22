<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Http\Request;
use App\Models\CampaignPublisher;
use App\Models\Lead;

class PublisherController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Publisher::with('user')
            ->latest();

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('company_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_person', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {

                        $userQuery->where(
                            'email',
                            'like',
                            '%' . $search . '%'
                        );

                    });

            });
        }

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        $publishers = $query
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.publishers.index',
            compact('publishers')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.publishers.create');
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],

            'company_name' => [
                'required',
                'string',
                'max:255'
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255'
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30'
            ],

            'website' => [
                'nullable',
                'url',
                'max:500'
            ],

            'callback_url' => [
                'nullable',
                'url',
                'max:1000'
            ],

            'notes' => [
                'nullable',
                'string'
            ],

        ]);


        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Create Login User
            |--------------------------------------------------------------------------
            */

            $user = User::create([

                'name' => $validated['name'],

                'email' => $validated['email'],

                'password' => Hash::make(
                    $validated['password']
                ),

                'role' => 'publisher',

                'company_name' => $validated['company_name'],

                'phone' => $validated['phone'] ?? null,

                'status' => 1,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Create Publisher Profile
            |--------------------------------------------------------------------------
            */

            Publisher::create([

                'user_id' => $user->id,

                'company_name' =>
                    $validated['company_name'],

                'contact_person' =>
                    $validated['contact_person'] ?? null,

                'phone' =>
                    $validated['phone'] ?? null,

                'website' =>
                    $validated['website'] ?? null,

                'callback_url' =>
                    $validated['callback_url'] ?? null,

                'notes' =>
                    $validated['notes'] ?? null,

                'status' => 1,

            ]);


            DB::commit();

            return redirect()
                ->route('admin.publishers.index')
                ->with(
                    'success',
                    'Publisher created successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create publisher: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(Publisher $publisher)
    {
        $publisher->load('user');

        return view(
            'admin.publishers.show',
            compact('publisher')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(Publisher $publisher)
    {
        $publisher->load('user');

        return view(
            'admin.publishers.edit',
            compact('publisher')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Publisher $publisher
    ) {

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' .
                    $publisher->user_id
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed'
            ],

            'company_name' => [
                'required',
                'string',
                'max:255'
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255'
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30'
            ],

            'website' => [
                'nullable',
                'url',
                'max:500'
            ],

            'callback_url' => [
                'nullable',
                'url',
                'max:1000'
            ],

            'notes' => [
                'nullable',
                'string'
            ],

        ]);


        DB::beginTransaction();

        try {

            $user = $publisher->user;

            $user->name =
                $validated['name'];

            $user->email =
                $validated['email'];

            $user->company_name =
                $validated['company_name'];

            $user->phone =
                $validated['phone'] ?? null;


            if (!empty($validated['password'])) {

                $user->password =
                    Hash::make(
                        $validated['password']
                    );
            }

            $user->save();


            $publisher->update([

                'company_name' =>
                    $validated['company_name'],

                'contact_person' =>
                    $validated['contact_person'] ?? null,

                'phone' =>
                    $validated['phone'] ?? null,

                'website' =>
                    $validated['website'] ?? null,

                'callback_url' =>
                    $validated['callback_url'] ?? null,

                'notes' =>
                    $validated['notes'] ?? null,

            ]);


            DB::commit();

            return redirect()
                ->route('admin.publishers.index')
                ->with(
                    'success',
                    'Publisher updated successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update publisher: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(Publisher $publisher)
    {
        DB::beginTransaction();

        try {

            $user = $publisher->user;

            $publisher->delete();

            if ($user) {
                $user->delete();
            }

            DB::commit();

            return redirect()
                ->route('admin.publishers.index')
                ->with(
                    'success',
                    'Publisher deleted successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Unable to delete publisher.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(Publisher $publisher)
    {
        DB::beginTransaction();

        try {

            $newStatus =
                $publisher->status == 1
                    ? 0
                    : 1;

            $publisher->status =
                $newStatus;

            $publisher->save();


            if ($publisher->user) {

                $publisher->user->status =
                    $newStatus;

                $publisher->user->save();
            }


            DB::commit();

            return back()->with(
                'success',
                'Publisher status updated.'
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Unable to update status.'
            );
        }
    }

    
    public function dashboard()
    {
        $publisherId = Auth::id();

        $assignments = CampaignPublisher::with([
            'campaign.advertiser'
        ])
        ->where('publisher_id', $publisherId)
        ->get();

        $totalLeads = Lead::where('publisher_id', $publisherId)->count();

        $todayLeads = Lead::where('publisher_id', $publisherId)
            ->whereDate('created_at', date('Y-m-d'))
            ->count();

        $newLeads = Lead::where('publisher_id', $publisherId)
            ->where('status', 'new')
            ->count();

        $approvedLeads = Lead::where('publisher_id', $publisherId)
            ->where('status', 'approved')
            ->count();

        $rejectedLeads = Lead::where('publisher_id', $publisherId)
            ->where('status', 'rejected')
            ->count();

        $recentLeads = Lead::with([
            'campaign',
            'advertiser'
        ])
        ->where('publisher_id', $publisherId)
        ->orderBy('id', 'desc')
        ->take(10)
        ->get();

        return view('publisher.dashboard', compact(
            'assignments',
            'totalLeads',
            'todayLeads',
            'newLeads',
            'approvedLeads',
            'rejectedLeads',
            'recentLeads'
        ));
    }

    public function campaigns()
    {
        $assignments = CampaignPublisher::with([
            'campaign.advertiser',
            'campaign.fields'
        ])
        ->where('publisher_id', Auth::id())
        ->orderBy('id', 'desc')
        ->get();

        return view('publisher.campaigns', compact('assignments'));
    }

    public function leads(Request $request)
    {
        $query = Lead::with([
            'campaign',
            'advertiser'
        ])->where('publisher_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        return view('publisher.leads', compact('leads'));
    }
    
    public function apiDocs()
    {
        $assignments = \App\Models\CampaignPublisher::with([
            'campaign.fields',
            'campaign.advertiser'
        ])
        ->where('publisher_id', \Illuminate\Support\Facades\Auth::id())
        ->where('status', 1)
        ->orderBy('id', 'desc')
        ->get();

        foreach ($assignments as $assignment) {
            $sampleData = array();

            if ($assignment->campaign) {
                foreach ($assignment->campaign->fields as $field) {
                    switch ($field->field_type) {
                        case 'email':
                            $sampleData[$field->field_key] = 'lead@example.com';
                            break;

                        case 'phone':
                            $sampleData[$field->field_key] = '9876543210';
                            break;

                        case 'number':
                            $sampleData[$field->field_key] = 100;
                            break;

                        case 'date':
                            $sampleData[$field->field_key] = '2026-10-10';
                            break;

                        case 'select':
                            $options = $field->options;

                            if (is_string($options)) {
                                $decoded = json_decode($options, true);

                                if (json_last_error() === JSON_ERROR_NONE) {
                                    $options = $decoded;
                                } else {
                                    $options = explode(',', $options);
                                }
                            }

                            $sampleData[$field->field_key] =
                                (is_array($options) && count($options))
                                    ? reset($options)
                                    : 'Example option';
                            break;

                        case 'textarea':
                            $sampleData[$field->field_key] = 'Example message';
                            break;

                        default:
                            $sampleData[$field->field_key] = 'Example text';
                            break;
                    }
                }
            }

            $payload = array('data' => $sampleData);

            $assignment->docs_endpoint = url('/api/v1/leads');

            $assignment->docs_json = json_encode(
                $payload,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            );

            $assignment->docs_curl =
                "curl --location '" . $assignment->docs_endpoint . "' \\\n" .
                "--header 'Accept: application/json' \\\n" .
                "--header 'Content-Type: application/json' \\\n" .
                "--header 'X-API-KEY: " . $assignment->api_key . "' \\\n" .
                "--data '" .
                str_replace("'", "'\\''", json_encode($payload)) .
                "'";

            $assignment->docs_php =
                "<?php\n\n" .
                "\$url = '" . $assignment->docs_endpoint . "';\n" .
                "\$apiKey = '" . $assignment->api_key . "';\n\n" .
                "\$payload = " . var_export($payload, true) . ";\n\n" .
                "\$ch = curl_init(\$url);\n" .
                "curl_setopt(\$ch, CURLOPT_POST, true);\n" .
                "curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);\n" .
                "curl_setopt(\$ch, CURLOPT_HTTPHEADER, array(\n" .
                "    'Accept: application/json',\n" .
                "    'Content-Type: application/json',\n" .
                "    'X-API-KEY: ' . \$apiKey\n" .
                "));\n" .
                "curl_setopt(\$ch, CURLOPT_POSTFIELDS, json_encode(\$payload));\n\n" .
                "\$response = curl_exec(\$ch);\n" .
                "\$httpCode = curl_getinfo(\$ch, CURLINFO_HTTP_CODE);\n\n" .
                "if (\$response === false) {\n" .
                "    echo curl_error(\$ch);\n" .
                "} else {\n" .
                "    echo \$response;\n" .
                "}\n\n" .
                "curl_close(\$ch);\n";
        }

        return view('publisher.api-docs', compact('assignments'));
    }


}