@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page_title', 'Dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">
            Welcome, {{ auth()->user()->name }}
        </h3>

        <p class="text-muted mb-0">
            GrossAds Lead Management Dashboard
        </p>

    </div>

</div>


<div class="row g-4">

    <div class="col-md-3">

        <div class="stat-card">

            <div class="d-flex justify-content-between">

                <div>

                    <small class="text-muted">
                        Publishers
                    </small>

                    <h3 class="mt-2 mb-0">
                        0
                    </h3>

                </div>

                <div class="stat-icon">

                    <i class="bi bi-people"></i>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="stat-card">

            <div class="d-flex justify-content-between">

                <div>

                    <small class="text-muted">
                        Advertisers
                    </small>

                    <h3 class="mt-2 mb-0">
                        0
                    </h3>

                </div>

                <div class="stat-icon">

                    <i class="bi bi-building"></i>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="stat-card">

            <div class="d-flex justify-content-between">

                <div>

                    <small class="text-muted">
                        Campaigns
                    </small>

                    <h3 class="mt-2 mb-0">
                        0
                    </h3>

                </div>

                <div class="stat-icon">

                    <i class="bi bi-megaphone"></i>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-3">

        <div class="stat-card">

            <div class="d-flex justify-content-between">

                <div>

                    <small class="text-muted">
                        Total Leads
                    </small>

                    <h3 class="mt-2 mb-0">
                        0
                    </h3>

                </div>

                <div class="stat-icon">

                    <i class="bi bi-person-lines-fill"></i>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="row mt-4">

    <div class="col-md-8">

        <div class="table-card">

            <div class="d-flex justify-content-between mb-3">

                <h5 class="mb-0">
                    Recent Leads
                </h5>

                <button class="btn btn-sm btn-outline-primary">
                    View All
                </button>

            </div>

            <div class="text-center text-muted py-5">

                No leads available yet.

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="table-card">

            <h5>
                API Status
            </h5>

            <hr>

            <div class="d-flex justify-content-between mb-3">

                <span>
                    Lead API
                </span>

                <span class="badge bg-success">
                    Active
                </span>

            </div>

            <div class="d-flex justify-content-between">

                <span>
                    API Logs
                </span>

                <span class="badge bg-success">
                    Enabled
                </span>

            </div>

        </div>

    </div>

</div>

@endsection