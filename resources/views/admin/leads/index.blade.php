
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <h3>Lead Management</h3>
            <p class="text-muted mb-0">View and manage all submitted leads.</p>
        </div>
        <div>
            <a href="{{ route('admin.leads.bulk-upload.index') }}" class="btn btn-success">
                <i class="fas fa-file-upload"></i> Bulk Upload
            </a>
            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">
                Back to Campaigns
            </a>
        </div>
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

    {{-- Statistics --}}
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

    {{-- Lead Tabs --}}
    <div class="card mb-4">
        <div class="card-body pb-0">
            <ul class="nav nav-pills mb-3">
                <li class="nav-item mr-2">
                    <a class="nav-link {{ $activeTab === 'all' ? 'active' : '' }}"
                    href="{{ route('admin.leads.index', array_merge(request()->except('page', 'tab'), ['tab' => 'all'])) }}">
                        <i class="fas fa-list"></i> All Leads
                        <span class="badge badge-light ml-1">{{ $totalLeads }}</span>
                    </a>
                </li>

                <li class="nav-item mr-2">
                    <a class="nav-link {{ $activeTab === 'duplicate' ? 'active' : '' }}"
                    href="{{ route('admin.leads.index', array_merge(request()->except('page', 'tab'), ['tab' => 'duplicate'])) }}">
                        <i class="fas fa-copy"></i> Duplicate
                        <span class="badge badge-warning ml-1">{{ $duplicateLeads }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'junk' ? 'active' : '' }}"
                    href="{{ route('admin.leads.index', array_merge(request()->except('page', 'tab'), ['tab' => 'junk'])) }}">
                        <i class="fas fa-exclamation-triangle"></i> Junk
                        <span class="badge badge-danger ml-1">{{ $junkLeads }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Search and Filters --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Search & Filters</strong>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.leads.index') }}">
                <input type="hidden" name="tab" value="{{ $activeTab }}">

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
                        <a href="{{ route('admin.leads.index', ['tab' => $activeTab]) }}"
                           class="btn btn-outline-secondary">
                            Reset Filters
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Leads Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <strong>
                    @if($activeTab === 'duplicate')
                        Duplicate Leads
                    @elseif($activeTab === 'junk')
                        Junk Leads
                    @else
                        All Leads
                    @endif
                </strong>

                <span class="text-muted">
                    ({{ $leads->total() }} matching records)
                </span>
            </div>

            <div class="mt-2 mt-md-0">
                <a href="{{ route('admin.leads.export', array_merge(['format' => 'excel'], request()->query())) }}"
                   class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Download Excel
                </a>

                <a href="{{ route('admin.leads.export', array_merge(['format' => 'csv'], request()->query())) }}"
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-file-csv"></i> Download CSV
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">

                <thead class="thead-light">
                    <tr>
                        <th>ID</th>

                        @foreach($fieldNames as $fieldName)
                            <th>{{ $fieldName }}</th>
                        @endforeach

                        <th>Campaign</th>
                        <th>Publisher</th>
                        <th>Advertiser</th>
                        <th>Status</th>
                        <th>Lead Type</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>

                            @foreach($fieldNames as $fieldName)
                                @php
                                    $matchedValue = $lead->values->first(function ($value) use ($fieldName) {
                                        return optional($value->field)->field_name === $fieldName;
                                    });
                                @endphp

                                <td>{{ $matchedValue->field_value ?? '-' }}</td>
                            @endforeach

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
                                    <span class="badge badge-primary">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if($lead->is_duplicate)
                                    <span class="badge badge-warning">Duplicate</span>
                                    @if($lead->duplicate_of_lead_id)
                                        <br>
                                        <small class="text-muted">
                                            Original: #{{ $lead->duplicate_of_lead_id }}
                                        </small>
                                    @endif
                                    @if($lead->duplicate_remark)
                                        <br>
                                        <small class="text-danger">
                                            {{ $lead->duplicate_remark }}
                                        </small>
                                    @endif
                                @endif

                                @if($lead->is_junk)
                                    <span class="badge badge-danger">Junk</span>
                                    @if($lead->junk_remark)
                                        <br>
                                        <small class="text-danger">
                                            {{ $lead->junk_remark }}
                                        </small>
                                    @endif
                                @endif

                                @if(!$lead->is_duplicate && !$lead->is_junk)
                                    <span class="badge badge-success">Clean</span>
                                @endif
                            </td>

                            <td>
                                {{ $lead->created_at
                                    ? $lead->created_at->format('d-m-Y H:i')
                                    : 'N/A' }}
                            </td>

                            <td>
                                <a href="{{ route('admin.leads.show', $lead->id) }}"
                                   class="btn btn-sm btn-info">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 8 + $fieldNames->count() }}"
                                class="text-center py-4">
                                No leads found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <!-- <div class="card-body">
            {{ $leads->links() }}
        </div> -->
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
            <div class="text-muted small mb-2">
                Showing {{ $leads->firstItem() ?? 0 }} to {{ $leads->lastItem() ?? 0 }}
                of {{ $leads->total() }} leads
            </div>

            <div class="mb-2">
                {{ $leads->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>
@endsection
