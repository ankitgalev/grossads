
@extends('layouts.advertiser')

@section('content')
<div class="container-fluid py-3">

    <h3>My Leads</h3>
    <p class="text-muted">Leads received for your campaigns.</p>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('advertiser.leads') }}">
                <div class="row">

                    <div class="col-md-3 mb-2">
                        <label>Campaign</label>
                        <select name="campaign_id" class="form-control">
                            <option value="">All Campaigns</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->id }}"
                                    {{ request('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                    {{ $campaign->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            @foreach(['new', 'approved', 'rejected'] as $status)
                                <option value="{{ $status }}"
                                    {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <label>From Date</label>
                        <input type="date" name="from" class="form-control"
                               value="{{ request('from') }}">
                    </div>

                    <div class="col-md-2 mb-2">
                        <label>To Date</label>
                        <input type="date" name="to" class="form-control"
                               value="{{ request('to') }}">
                    </div>

                    <div class="col-md-3 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            Filter
                        </button>
                        <a href="{{ route('advertiser.leads') }}"
                           class="btn btn-secondary">Reset</a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Campaign</th>
                        <th>Publisher</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->lead_reference }}</td>
                            <td>{{ optional($lead->campaign)->name ?? 'N/A' }}</td>
                            <td>{{ optional($lead->publisher)->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $badge = 'secondary';
                                    if ($lead->status == 'approved') $badge = 'success';
                                    elseif ($lead->status == 'rejected') $badge = 'danger';
                                    elseif ($lead->status == 'new') $badge = 'info';
                                @endphp
                                <span class="badge badge-{{ $badge }}">
                                    {{ ucfirst($lead->status) }}
                                </span>
                            </td>
                            <td>{{ optional($lead->created_at)->format('d M Y, h:i A') }}</td>
                            
                            <td>
                                <a href="{{ route('advertiser.leads.show', $lead->id) }}"
                                class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View / Details
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No leads found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $leads->links() }}
        </div>
    </div>

</div>
@endsection
