
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Lead Details</h3>
            <p class="text-muted mb-0">
                Reference: {{ $lead->lead_reference }}
            </p>
        </div>
        <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
            Back to Leads
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">

            <div class="card mb-4">
                <div class="card-header"><strong>Lead Information</strong></div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 35%">Lead ID</th>
                            <td>{{ $lead->id }}</td>
                        </tr>
                        <tr>
                            <th>Reference</th>
                            <td>{{ $lead->lead_reference }}</td>
                        </tr>
                        <tr>
                            <th>Campaign</th>
                            <td>{{ optional($lead->campaign)->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Publisher</th>
                            <td>
                                {{ optional($lead->publisher)->name
                                    ?? optional($lead->publisher)->email
                                    ?? 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Advertiser</th>
                            <td>{{ optional($lead->advertiser)->company_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Source</th>
                            <td>{{ $lead->source ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Submitted At</th>
                            <td>{{ $lead->submitted_at
                                ? \Carbon\Carbon::parse($lead->submitted_at)->format('d-m-Y H:i:s')
                                : ($lead->created_at ? $lead->created_at->format('d-m-Y H:i:s') : 'N/A') }}</td>
                        </tr>
                        <tr>
                            <th>IP Address</th>
                            <td>{{ $lead->ip_address ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><strong>Submitted Field Values</strong></div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lead->values as $leadValue)
                                <tr>
                                    <td>{{ optional($leadValue->field)->field_name ?? 'Field' }}</td>
                                    <td>{{ $leadValue->field_value }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">No field values found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><strong>Update Lead Status</strong></div>
                <div class="card-body">

                    <p>
                        Current status:
                        <strong>{{ ucfirst($lead->status) }}</strong>
                    </p>

                    <form method="POST"
                          action="{{ route('admin.leads.status', $lead->id) }}">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>
                                    New
                                </option>
                                <option value="approved" {{ $lead->status === 'approved' ? 'selected' : '' }}>
                                    Approved
                                </option>
                                <option value="rejected" {{ $lead->status === 'rejected' ? 'selected' : '' }}>
                                    Rejected
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            Update Status
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
