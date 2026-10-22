@extends('layouts.publisher')

@section('title', 'Publisher Dashboard')

@section('page_title', 'Publisher Dashboard')

@section('content')

<div class="mb-4">

    <h3>
        Welcome, {{ auth()->user()->name }}
    </h3>

    <p class="text-muted">
        Manage your campaigns, API and leads.
    </p>

</div>


<div class="row g-4">

    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <small class="text-muted">
                    Assigned Campaigns
                </small>

                <h2 class="mt-2">
                    0
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <small class="text-muted">
                    Submitted Leads
                </small>

                <h2 class="mt-2">
                    0
                </h2>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <small class="text-muted">
                    Accepted Leads
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
            API Integration
        </h5>

        <p class="text-muted">

            Your API credentials and campaign-wise
            API documentation will appear here.

        </p>

        <button class="btn btn-primary">

            View API Documentation

        </button>

    </div>

</div>

@endsection