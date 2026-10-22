
@extends('layouts.advertiser')

@section('content')
<div class="container-fluid py-3">

    <h3 class="mb-1">Advertiser Dashboard</h3>
    <p class="text-muted">
        Welcome, {{ Auth::user()->name ?? 'Advertiser' }}
    </p>

    <div class="row">
        @php
            $cards = [
                ['title' => 'Total Campaigns', 'value' => $totalCampaigns, 'color' => 'primary'],
                ['title' => 'Active Campaigns', 'value' => $activeCampaigns, 'color' => 'success'],
                ['title' => 'Total Leads', 'value' => $totalLeads, 'color' => 'info'],
                ['title' => "Today's Leads", 'value' => $todayLeads, 'color' => 'warning'],
                ['title' => 'New Leads', 'value' => $newLeads, 'color' => 'secondary'],
                ['title' => 'Approved Leads', 'value' => $approvedLeads, 'color' => 'success'],
                ['title' => 'Rejected Leads', 'value' => $rejectedLeads, 'color' => 'danger'],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-{{ $card['color'] }} shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">
                            {{ $card['title'] }}
                        </div>
                        <h3 class="mb-0 mt-2">
                            {{ number_format($card['value']) }}
                        </h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>My Campaigns</strong>
            <a href="{{ route('advertiser.campaigns') }}"
               class="btn btn-sm btn-primary">View All</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Advertiser Price</th>
                        <th>Total Leads</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                        <tr>
                            <td>{{ $campaign->name }}</td>
                            <td>{{ ucfirst($campaign->campaign_type ?? 'lead') }}</td>
                            <td>
                                <span class="badge badge-{{ $campaign->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td>
                                {{ number_format((float) $campaign->advertiser_price, 2) }}
                            </td>
                            <td>{{ $campaign->leads_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No campaigns found for your account.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Recent Leads</strong>
            <a href="{{ route('advertiser.leads') }}"
               class="btn btn-sm btn-primary">View All</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Campaign</th>
                        <th>Publisher</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLeads as $lead)
                        <tr>
                            <td>{{ $lead->lead_reference }}</td>
                            <td>{{ optional($lead->campaign)->name ?? 'N/A' }}</td>
                            <td>{{ optional($lead->publisher)->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>
                            <td>{{ optional($lead->created_at)->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No leads received yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
