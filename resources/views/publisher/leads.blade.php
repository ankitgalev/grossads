
@extends('layouts.publisher')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">My Leads</h3>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('publisher.leads') }}">
                <div class="row">

                    <div class="col-md-3 mb-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label>From Date</label>
                        <input type="date" name="from"
                               value="{{ request('from') }}"
                               class="form-control">
                    </div>

                    <div class="col-md-3 mb-2">
                        <label>To Date</label>
                        <input type="date" name="to"
                               value="{{ request('to') }}"
                               class="form-control">
                    </div>

                    <div class="col-md-3 mb-2 d-flex align-items-end">
                        <button class="btn btn-primary mr-2" type="submit">
                            Filter
                        </button>

                        <a href="{{ route('publisher.leads') }}"
                           class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reference</th>
                        <th>Campaign</th>
                        <th>Advertiser</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->lead_reference }}</td>
                            <td>{{ optional($lead->campaign)->name ?: 'N/A' }}</td>
                            <td>{{ optional($lead->advertiser)->company_name ?: 'N/A' }}</td>
                            <td>{{ ucfirst($lead->status) }}</td>
                            <td>
                                {{ $lead->created_at
                                    ? $lead->created_at->format('d-m-Y H:i')
                                    : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No leads found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $leads->links() }}

        </div>
    </div>

</div>
@endsection
