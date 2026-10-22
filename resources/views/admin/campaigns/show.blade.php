@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3>
                Campaign Details
            </h3>

            <p class="text-muted">
                {{ $campaign->name }}
            </p>

        </div>


        <div>

            <a
                href="{{ route(
                    'admin.campaigns.edit',
                    $campaign->id
                ) }}"
                class="btn btn-warning">

                <i class="fas fa-edit"></i>
                Edit

            </a>


            <a
                href="{{ route(
                    'admin.campaigns.index'
                ) }}"
                class="btn btn-secondary">

                Back

            </a>

        </div>

    </div>


    <div class="row">


        {{-- Campaign Details --}}

        <div class="col-md-4">

            <div class="card mb-4">

                <div class="card-header">

                    <strong>
                        Campaign Information
                    </strong>

                </div>


                <div class="card-body">

                    <p>

                        <strong>
                            ID:
                        </strong>

                        {{ $campaign->id }}

                    </p>


                    <p>

                        <strong>
                            Name:
                        </strong>

                        {{ $campaign->name }}

                    </p>


                    <p>

                        <strong>
                            Advertiser:
                        </strong>

                        {{ $campaign->advertiser->name ?? '-' }}

                    </p>


                    <p>

                        <strong>
                            Status:
                        </strong>

                        <span class="badge bg-success">

                            {{ ucfirst(
                                $campaign->status
                            ) }}

                        </span>

                    </p>


                    <p>

                        <strong>
                            Publishers:
                        </strong>

                        {{ $campaign->publishers->count() }}

                    </p>


                    <p>

                        <strong>
                            Leads:
                        </strong>

                        {{ $campaign->leads->count() }}

                    </p>


                    @if($campaign->description)

                        <hr>

                        <strong>
                            Description
                        </strong>

                        <p class="mt-2">

                            {{ $campaign->description }}

                        </p>

                    @endif

                </div>

            </div>

        </div>


        {{-- Assigned Publishers --}}

        <div class="col-md-8">

            <div class="card">

                <div class="card-header
                            d-flex
                            justify-content-between
                            align-items-center">

                    <strong>
                        Assigned Publishers
                    </strong>


                    <a
                        href="{{ route(
                            'admin.campaigns.assign',
                            $campaign->id
                        ) }}"
                        class="btn btn-sm btn-primary">

                        <i class="fas fa-user-plus"></i>

                        Assign Publisher

                    </a>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered mb-0">

                            <thead>

                                <tr>

                                    <th>
                                        Publisher
                                    </th>

                                    <th>
                                        Payout
                                    </th>

                                    <th>
                                        Daily Limit
                                    </th>

                                    <th>
                                        Total Limit
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                            @forelse(
                                $campaign->publishers
                                as $publisher
                            )

                                <tr>

                                    <td>

                                        <strong>
                                            {{ $publisher->name }}
                                        </strong>

                                        <br>

                                        <small
                                            class="text-muted">

                                            {{ $publisher->email }}

                                        </small>

                                    </td>


                                    <td>

                                        ₹{{ number_format(
                                            $publisher
                                                ->pivot
                                                ->publisher_payout
                                                ?? 0,
                                            2
                                        ) }}

                                    </td>


                                    <td>

                                        {{ $publisher
                                            ->pivot
                                            ->daily_limit
                                            ?? '-' }}

                                    </td>


                                    <td>

                                        {{ $publisher
                                            ->pivot
                                            ->total_limit
                                            ?? '-' }}

                                    </td>


                                    <td>

                                        @if(
                                            $publisher
                                                ->pivot
                                                ->status
                                            == 1
                                        )

                                            <span
                                                class="badge bg-success">

                                                Active

                                            </span>

                                        @else

                                            <span
                                                class="badge bg-secondary">

                                                Inactive

                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="5"
                                        class="text-center py-4">

                                        No publishers assigned.

                                    </td>

                                </tr>

                            @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection