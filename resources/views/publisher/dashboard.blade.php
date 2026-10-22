
@extends('layouts.publisher')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">Publisher Dashboard</h3>

    <div class="row">

        <div class="col-md-4 col-xl-2 mb-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Leads</p>
                    <h3>{{ $totalLeads }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2 mb-3">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <p class="text-muted mb-1">Today's Leads</p>
                    <h3>{{ $todayLeads }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-2 mb-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <p class="text-muted mb-1">New Leads</p>
                    <h3>{{ $newLeads }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-3 mb-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <p class="text-muted mb-1">Approved Leads</p>
                    <h3>{{ $approvedLeads }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-3 mb-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <p class="text-muted mb-1">Rejected Leads</p>
                    <h3>{{ $rejectedLeads }}</h3>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <strong>My Assigned Campaigns</strong>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Advertiser</th>
                        <th>Payout</th>
                        <th>Daily Limit</th>
                        <th>Total Limit</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td>
                                {{ optional($assignment->campaign)->name ?: 'N/A' }}
                            </td>

                            <td>
                                {{ optional(optional($assignment->campaign)->advertiser)->company_name ?: 'N/A' }}
                            </td>

                            <td>
                                {{ number_format((float) $assignment->publisher_payout, 2) }}
                            </td>

                            <td>{{ $assignment->daily_limit ?: 'Unlimited' }}</td>

                            <td>{{ $assignment->total_limit ?: 'Unlimited' }}</td>

                            <td>
                                @if($assignment->status)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No campaigns have been assigned to you yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <a href="{{ route('publisher.campaigns') }}"
               class="btn btn-primary">
                View All Campaigns
            </a>

            <a href="{{ route('publisher.leads') }}"
               class="btn btn-outline-primary">
                View My Leads
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Recent Leads</strong>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reference</th>
                        <th>Campaign</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($recentLeads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->lead_reference }}</td>
                            <td>{{ optional($lead->campaign)->name ?: 'N/A' }}</td>
                            <td>{{ ucfirst($lead->status) }}</td>
                            <td>
                                {{ $lead->created_at
                                    ? $lead->created_at->format('d-m-Y H:i')
                                    : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                No leads submitted yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
