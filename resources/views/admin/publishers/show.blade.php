@extends('layouts.admin')

@section('title', 'Publisher Details')

@section('page_title', 'Publisher Details')

@section('content')

<div class="d-flex justify-content-between mb-4">

    <h3>
        Publisher Details
    </h3>

    <a
        href="{{ route('admin.publishers.edit', $publisher) }}"
        class="btn btn-primary">

        <i class="bi bi-pencil"></i>

        Edit Publisher

    </a>

</div>


<div class="row">

    <div class="col-md-8">

        <div class="table-card">

            <h5 class="mb-4">
                Basic Information
            </h5>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Name
                    </small>

                    <div class="fw-bold">
                        {{ $publisher->user->name }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Email
                    </small>

                    <div class="fw-bold">
                        {{ $publisher->user->email }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Company
                    </small>

                    <div class="fw-bold">
                        {{ $publisher->company_name }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Contact Person
                    </small>

                    <div>
                        {{ $publisher->contact_person ?? '-' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Phone
                    </small>

                    <div>
                        {{ $publisher->phone ?? '-' }}
                    </div>

                </div>


                <div class="col-md-6 mb-3">

                    <small class="text-muted">
                        Website
                    </small>

                    <div>
                        {{ $publisher->website ?? '-' }}
                    </div>

                </div>

            </div>

        </div>


        <div class="table-card mt-4">

            <h5 class="mb-3">
                Callback Configuration
            </h5>

            @if($publisher->callback_url)

                <div class="alert alert-success">

                    <strong>
                        Callback URL:
                    </strong>

                    <br>

                    {{ $publisher->callback_url }}

                </div>

            @else

                <div class="alert alert-warning">

                    Callback URL is not configured.

                </div>

            @endif

        </div>

    </div>


    <div class="col-md-4">

        <div class="table-card">

            <h5>
                Account Status
            </h5>

            <hr>

            @if($publisher->status == 1)

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