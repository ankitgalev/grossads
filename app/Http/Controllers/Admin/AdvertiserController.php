<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Advertiser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdvertiserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Advertiser::with('user')
            ->latest();

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'company_name',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'contact_person',
                    'like',
                    '%' . $search . '%'
                )

                ->orWhere(
                    'phone',
                    'like',
                    '%' . $search . '%'
                )

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

        $advertisers = $query
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.advertisers.index',
            compact('advertisers')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.advertisers.create');
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

            'industry' => [
                'nullable',
                'string',
                'max:255'
            ],

            'address' => [
                'nullable',
                'string'
            ],

            'city' => [
                'nullable',
                'string',
                'max:100'
            ],

            'state' => [
                'nullable',
                'string',
                'max:100'
            ],

            'country' => [
                'required',
                'string',
                'max:100'
            ],

            'pincode' => [
                'nullable',
                'string',
                'max:20'
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
            | User
            |--------------------------------------------------------------------------
            */

            $user = User::create([

                'name' =>
                    $validated['name'],

                'email' =>
                    $validated['email'],

                'password' =>
                    Hash::make(
                        $validated['password']
                    ),

                'role' =>
                    'advertiser',

                'company_name' =>
                    $validated['company_name'],

                'phone' =>
                    $validated['phone'] ?? null,

                'status' =>
                    1,

            ]);


            /*
            |--------------------------------------------------------------------------
            | Advertiser Profile
            |--------------------------------------------------------------------------
            */

            Advertiser::create([

                'user_id' =>
                    $user->id,

                'company_name' =>
                    $validated['company_name'],

                'contact_person' =>
                    $validated['contact_person'] ?? null,

                'phone' =>
                    $validated['phone'] ?? null,

                'website' =>
                    $validated['website'] ?? null,

                'industry' =>
                    $validated['industry'] ?? null,

                'address' =>
                    $validated['address'] ?? null,

                'city' =>
                    $validated['city'] ?? null,

                'state' =>
                    $validated['state'] ?? null,

                'country' =>
                    $validated['country'],

                'pincode' =>
                    $validated['pincode'] ?? null,

                'notes' =>
                    $validated['notes'] ?? null,

                'status' =>
                    1,

            ]);


            DB::commit();

            return redirect()
                ->route('admin.advertisers.index')
                ->with(
                    'success',
                    'Advertiser created successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create advertiser: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(Advertiser $advertiser)
    {
        $advertiser->load('user');

        return view(
            'admin.advertisers.show',
            compact('advertiser')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(Advertiser $advertiser)
    {
        $advertiser->load('user');

        return view(
            'admin.advertisers.edit',
            compact('advertiser')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Advertiser $advertiser
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
                    $advertiser->user_id
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

            'industry' => [
                'nullable',
                'string',
                'max:255'
            ],

            'address' => [
                'nullable',
                'string'
            ],

            'city' => [
                'nullable',
                'string',
                'max:100'
            ],

            'state' => [
                'nullable',
                'string',
                'max:100'
            ],

            'country' => [
                'required',
                'string',
                'max:100'
            ],

            'pincode' => [
                'nullable',
                'string',
                'max:20'
            ],

            'notes' => [
                'nullable',
                'string'
            ],

        ]);


        DB::beginTransaction();

        try {

            $user = $advertiser->user;

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


            $advertiser->update([

                'company_name' =>
                    $validated['company_name'],

                'contact_person' =>
                    $validated['contact_person'] ?? null,

                'phone' =>
                    $validated['phone'] ?? null,

                'website' =>
                    $validated['website'] ?? null,

                'industry' =>
                    $validated['industry'] ?? null,

                'address' =>
                    $validated['address'] ?? null,

                'city' =>
                    $validated['city'] ?? null,

                'state' =>
                    $validated['state'] ?? null,

                'country' =>
                    $validated['country'],

                'pincode' =>
                    $validated['pincode'] ?? null,

                'notes' =>
                    $validated['notes'] ?? null,

            ]);


            DB::commit();

            return redirect()
                ->route('admin.advertisers.index')
                ->with(
                    'success',
                    'Advertiser updated successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update advertiser: ' .
                    $e->getMessage()
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(Advertiser $advertiser)
    {
        DB::beginTransaction();

        try {

            $user = $advertiser->user;

            $advertiser->delete();

            if ($user) {
                $user->delete();
            }

            DB::commit();

            return redirect()
                ->route('admin.advertisers.index')
                ->with(
                    'success',
                    'Advertiser deleted successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Unable to delete advertiser.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(Advertiser $advertiser)
    {
        DB::beginTransaction();

        try {

            $newStatus =
                $advertiser->status == 1
                    ? 0
                    : 1;

            $advertiser->status =
                $newStatus;

            $advertiser->save();


            if ($advertiser->user) {

                $advertiser->user->status =
                    $newStatus;

                $advertiser->user->save();
            }


            DB::commit();

            return back()->with(
                'success',
                'Advertiser status updated.'
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Unable to update status.'
            );
        }
    }
}