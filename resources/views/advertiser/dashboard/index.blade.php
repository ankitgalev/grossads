@extends('layouts.advertiser')

@section('title', 'Advertiser Dashboard')

@section('page_title', 'Advertiser Dashboard')

@section('content')

<div class="mb-4">

    <h3>
        Welcome, {{ auth()->user()->name }}
    </h3>

    <p class="text-muted">
        Manage campaigns and incoming leads.
    </p>

</div>


<div class="row g-4">

    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <small class="text-muted">
                    Campaigns
                </small>

                <h2 class="mt-2">
                    0
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <small class="text-muted">
                    Total Leads
                </small>

                <h2 class="mt-2">
                    0
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <small class="text-muted">
                    Accepted
                </small>

                <h2 class="mt-2">
                    0
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <small class="text-muted">
                    Converted
                </small>

                <h2 class="mt-2">
                    0
                </h2>

            </div>

        </div>

    </div>

</div>


<div class="card border-0 shadow-sm mt-4">

    <div class="card-body">

        <h5>
            Lead Overview
        </h5>

        <p class="text-muted mb-0">

            Campaign-wise leads and lead status
            will appear here.

        </p>

    </div>

</div>

@endsection
