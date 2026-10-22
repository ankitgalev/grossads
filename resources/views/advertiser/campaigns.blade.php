
@extends('layouts.advertiser')

@section('content')
<div class="container-fluid py-3">

    <h3>My Campaigns</h3>
    <p class="text-muted">Campaigns associated with your company.</p>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Campaign Name</th>
                        <th>Type</th>
                        <th>Advertiser Price</th>
                        <th>Status</th>
                        <th>Total Leads</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $campaign)
                        <tr>
                            <td>{{ $campaign->id }}</td>
                            <td>{{ $campaign->name }}</td>
                            <td>{{ ucfirst($campaign->campaign_type ?? 'lead') }}</td>
                            <td>{{ number_format((float) $campaign->advertiser_price, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $campaign->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td>{{ $campaign->leads_count }}</td>
                            <td>{{ optional($campaign->created_at)->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No campaigns have been assigned to your account yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $campaigns->links() }}
        </div>
    </div>

</div>
@endsection
