
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Lead Management</h3>
            <p class="text-muted mb-0">View and manage all submitted leads.</p>
        </div>
        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">
            Back to Campaigns
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3 mb-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Total Leads</div>
                    <h3>{{ $totalLeads }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">New</div>
                    <h3 class="text-primary">{{ $newLeads }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Approved</div>
                    <h3 class="text-success">{{ $approvedLeads }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Rejected</div>
                    <h3 class="text-danger">{{ $rejectedLeads }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Search & Filters</strong></div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.leads.index') }}">
                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label>Search</label>
                        <input type="text" name="search" class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Lead reference, campaign, publisher">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Campaign</label>
                        <select name="campaign_id" class="form-control">
                            <option value="">All Campaigns</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->id }}"
                                    {{ (string)request('campaign_id') === (string)$campaign->id ? 'selected' : '' }}>
                                    {{ $campaign->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Publisher</label>
                        <select name="publisher_id" class="form-control">
                            <option value="">All Publishers</option>
                            @foreach($publishers as $publisher)
                                <option value="{{ $publisher->id }}"
                                    {{ (string)request('publisher_id') === (string)$publisher->id ? 'selected' : '' }}>
                                    {{ $publisher->name ?? $publisher->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            @foreach(['new', 'approved', 'rejected'] as $status)
                                <option value="{{ $status }}"
                                    {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>From Date</label>
                        <input type="date" name="date_from" class="form-control"
                            value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>To Date</label>
                        <input type="date" name="date_to" class="form-control"
                            value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="{{ route('admin.leads.index') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>All Leads</strong>
            <span class="text-muted">({{ $leads->total() }} matching records)</span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Lead Reference</th>
                        <th>Campaign</th>
                        <th>Publisher</th>
                        <th>Advertiser</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td><code>{{ $lead->lead_reference }}</code></td>
                            <td>{{ optional($lead->campaign)->name ?? 'N/A' }}</td>
                            <td>
                                {{ optional($lead->publisher)->name
                                    ?? optional($lead->publisher)->email
                                    ?? 'N/A' }}
                            </td>
                            <td>
                                {{ optional($lead->advertiser)->company_name ?? 'N/A' }}
                            </td>
                            <td>
                                @if($lead->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($lead->status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-primary">{{ ucfirst($lead->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $lead->created_at ? $lead->created_at->format('d-m-Y H:i') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.leads.show', $lead->id) }}"
                                   class="btn btn-sm btn-info">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                No leads found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body">
            {{ $leads->links() }}
        </div>
    </div>

</div>
@endsection
