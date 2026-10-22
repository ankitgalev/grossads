
@extends('layouts.publisher')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">My Campaigns</h3>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Advertiser</th>
                        <th>Payout</th>
                        <th>API Key</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td>{{ optional($assignment->campaign)->name ?: 'N/A' }}</td>

                            <td>
                                {{ optional(optional($assignment->campaign)->advertiser)->company_name ?: 'N/A' }}
                            </td>

                            <td>
                                {{ number_format((float) $assignment->publisher_payout, 2) }}
                            </td>

                            <td>
                                @if($assignment->status && $assignment->api_key)
                                    <input type="password"
                                           id="key-{{ $assignment->id }}"
                                           value="{{ $assignment->api_key }}"
                                           class="form-control form-control-sm"
                                           readonly>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary mt-2"
                                            onclick="toggleKey({{ $assignment->id }})">
                                        Show / Hide
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-primary mt-2"
                                            onclick="copyKey({{ $assignment->id }})">
                                        Copy Key
                                    </button>
                                @else
                                    <span class="text-muted">Inactive / unavailable</span>
                                @endif
                            </td>

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
                            <td colspan="5" class="text-center">
                                No campaigns assigned.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>

<script>
function toggleKey(id) {
    var input = document.getElementById('key-' + id);

    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
}

function copyKey(id) {
    var input = document.getElementById('key-' + id);

    if (!input) {
        return;
    }

    input.type = 'text';
    input.select();
    input.setSelectionRange(0, 99999);

    try {
        if (document.execCommand('copy')) {
            alert('API key copied');
        } else {
            alert('Copy failed. Please copy manually.');
        }
    } catch (e) {
        alert('Copy failed. Please copy manually.');
    }
}
</script>
@endsection
