@extends('layouts.admin')

@section('title', 'Advertiser Details')

@section('page_title', 'Advertiser Details')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h3>
        Advertiser Details
    </h3>

    <a
        href="{{ route('admin.advertisers.edit', $advertiser) }}"
        class="btn btn-primary">

        <i class="bi bi-pencil"></i>

        Edit Advertiser

    </a>

</div>


<div class="row">

    <div class="col-md-8">

        <div class="table-card">

            <h5 class="mb-4">
                Company Information
            </h5>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Contact Name
                    </small>

                    <div class="fw-bold">
                        {{ $advertiser->user->name }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Email
                    </small>

                    <div class="fw-bold">
                        {{ $advertiser->user->email }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Company
                    </small>

                    <div class="fw-bold">
                        {{ $advertiser->company_name }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Contact Person
                    </small>

                    <div>
                        {{ $advertiser->contact_person ?? '-' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Phone
                    </small>

                    <div>
                        {{ $advertiser->phone ?? '-' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Industry
                    </small>

                    <div>
                        {{ $advertiser->industry ?? '-' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Website
                    </small>

                    <div>
                        {{ $advertiser->website ?? '-' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        City
                    </small>

                    <div>
                        {{ $advertiser->city ?? '-' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        State
                    </small>

                    <div>
                        {{ $advertiser->state ?? '-' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Pincode
                    </small>

                    <div>
                        {{ $advertiser->pincode ?? '-' }}
                    </div>

                </div>


                <div class="col-md-12 mb-3">

                    <small class="text-muted">
                        Address
                    </small>

                    <div>
                        {{ $advertiser->address ?? '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <div class="table-card">

            <h5>
                Account Status
            </h5>

            <hr>

            @if($advertiser->status == 1)

                <span class="badge bg-success fs-6">
                    Active
                </span>

            @else

                <span class="badge bg-secondary fs-6">
                    Inactive
                </span>

            @endif

        </div>

    </div>

</div>

@endsection