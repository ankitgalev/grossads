
@extends('layouts.publisher')

@section('content')
<div class="container-fluid">

    <h3 class="mb-2">API Documentation</h3>
    <p class="text-muted">
        Use your assigned campaign credentials to submit leads.
    </p>

    <div class="alert alert-info">
        <strong>Method:</strong> POST<br>
        <strong>Endpoint:</strong> {{ url('/api/v1/leads') }}<br>
        <strong>Content-Type:</strong> application/json<br>
        <strong>Authentication:</strong> X-API-KEY header
    </div>

    @forelse($assignments as $assignment)
        <div class="card shadow-sm mb-4">

            <div class="card-header">
                <strong>
                    {{ optional($assignment->campaign)->name ?: 'Campaign' }}
                </strong>
                <span class="badge badge-success float-right">Active</span>
            </div>

            <div class="card-body">

                <p>
                    <strong>Advertiser:</strong>
                    {{ optional(optional($assignment->campaign)->advertiser)->company_name ?: 'N/A' }}
                </p>

                <p>
                    <strong>Your payout per approved lead:</strong>
                    {{ number_format((float) $assignment->publisher_payout, 2) }}
                </p>

                <div class="form-group">
                    <label>API Endpoint</label>
                    <div class="input-group">
                        <input type="text"
                               id="endpoint-{{ $assignment->id }}"
                               class="form-control"
                               value="{{ $assignment->docs_endpoint }}"
                               readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary"
                                    type="button"
                                    onclick="copyField('endpoint-{{ $assignment->id }}')">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>API Key</label>
                    <div class="input-group">
                        <input type="password"
                               id="key-{{ $assignment->id }}"
                               class="form-control"
                               value="{{ $assignment->api_key }}"
                               readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary"
                                    type="button"
                                    onclick="toggleKey({{ $assignment->id }})">
                                Show / Hide
                            </button>
                            <button class="btn btn-outline-primary"
                                    type="button"
                                    onclick="copyField('key-{{ $assignment->id }}')">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>

                <h5 class="mt-4">Required Campaign Fields</h5>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Field Name</th>
                                <th>API Field Key</th>
                                <th>Type</th>
                                <th>Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($assignment->campaign)
                                @forelse($assignment->campaign->fields as $field)
                                    <tr>
                                        <td>{{ $field->field_name }}</td>
                                        <td><code>{{ $field->field_key }}</code></td>
                                        <td>{{ $field->field_type }}</td>
                                        <td>
                                            {{ $field->is_required ? 'Yes' : 'No' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No fields configured.</td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>

                <h5 class="mt-4">Sample JSON</h5>
                <textarea id="json-{{ $assignment->id }}"
                          class="form-control mb-2"
                          rows="8"
                          readonly>{{ $assignment->docs_json }}</textarea>
                <button class="btn btn-outline-primary mb-3"
                        type="button"
                        onclick="copyField('json-{{ $assignment->id }}')">
                    Copy JSON
                </button>

                <h5>PHP cURL Example</h5>
                <textarea id="php-{{ $assignment->id }}"
                          class="form-control mb-2"
                          rows="15"
                          readonly>{{ $assignment->docs_php }}</textarea>
                <button class="btn btn-primary mb-3"
                        type="button"
                        onclick="copyField('php-{{ $assignment->id }}')">
                    Copy PHP Code
                </button>

                <h5>Terminal cURL Example</h5>
                <textarea id="curl-{{ $assignment->id }}"
                          class="form-control mb-2"
                          rows="6"
                          readonly>{{ $assignment->docs_curl }}</textarea>
                <button class="btn btn-outline-primary"
                        type="button"
                        onclick="copyField('curl-{{ $assignment->id }}')">
                    Copy cURL
                </button>

            </div>
        </div>
    @empty
        <div class="alert alert-warning">
            No active campaign assignments found. Please contact the admin.
        </div>
    @endforelse

    <div class="card shadow-sm">
        <div class="card-header"><strong>Integration Notes</strong></div>
        <div class="card-body">
            <ol>
                <li>Send a POST request to the endpoint shown above.</li>
                <li>Send your campaign API key in the X-API-KEY header.</li>
                <li>Submit JSON using the exact API field keys listed for the campaign.</li>
                <li>Use HTTPS on production websites.</li>
                <li>Keep your API key private. Do not place it in public frontend JavaScript.</li>
            </ol>
        </div>
    </div>

</div>

<script>
function toggleKey(id) {
    var field = document.getElementById('key-' + id);

    if (field) {
        field.type = field.type === 'password' ? 'text' : 'password';
    }
}

function copyField(id) {
    var field = document.getElementById(id);

    if (!field) {
        return;
    }

    field.focus();
    field.select();
    field.setSelectionRange(0, 999999);

    try {
        if (document.execCommand('copy')) {
            alert('Copied successfully');
        } else {
            alert('Copy manually if copying did not work.');
        }
    } catch (e) {
        alert('Copy manually if copying did not work.');
    }
}
</script>
@endsection
