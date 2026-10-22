
@extends('layouts.advertiser')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>Lead Details</h3>
            <p class="text-muted mb-0">
                Complete information for your lead
            </p>
        </div>

        <a href="{{ route('advertiser.leads') }}"
           class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Leads
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <strong>Lead Information</strong>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <strong>Lead ID</strong>
                    <div>{{ $lead->id }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Reference</strong>
                    <div>{{ $lead->lead_reference }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Status</strong>
                    <div>
                        <span class="badge badge-secondary">
                            {{ ucfirst($lead->status) }}
                        </span>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Campaign</strong>
                    <div>{{ optional($lead->campaign)->name ?? 'N/A' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Publisher</strong>
                    <div>{{ optional($lead->publisher)->name ?? 'N/A' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Submitted At</strong>
                    <div>
                        {{ optional($lead->created_at)->format('d M Y, h:i A') }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>IP Address</strong>
                    <div>{{ $lead->ip_address ?: 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Submitted Lead Fields</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Field Name</th>
                        <th>Submitted Value</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($lead->values as $value)
                        <tr>
                            <td>
                                {{ optional($value->field)->field_name
                                    ?? optional($value->field)->field_key
                                    ?? 'Field' }}
                            </td>
                            <td>
                                {{ $value->field_value !== null
                                    && $value->field_value !== ''
                                    ? $value->field_value
                                    : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">
                                No submitted field values found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
