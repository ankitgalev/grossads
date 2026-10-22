
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>API Documentation</h3>
            <p class="text-muted mb-0">
                Copy campaign-specific API integration details for publishers.
            </p>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>API Endpoint:</strong>
        <code>{{ url('/api/v1/leads') }}</code>
        <br>
        <strong>Method:</strong> POST
        <br>
        <strong>Authentication:</strong> X-API-KEY request header
        <br>
        <strong>Content-Type:</strong> application/json
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <strong>Publisher Campaign API Keys</strong>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Publisher</th>
                            <th>API Key</th>
                            <th>Assignment Status</th>
                            <th>Integration Code</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td>
                                    {{ optional($assignment->campaign)->name ?: 'N/A' }}
                                </td>

                                <td>
                                    {{ optional($assignment->publisher)->name
                                        ?: optional($assignment->publisher)->email
                                        ?: 'N/A' }}
                                </td>

                                <td style="min-width:220px">
                                    <input type="password"
                                           id="api-key-{{ $assignment->id }}"
                                           class="form-control form-control-sm"
                                           value="{{ $assignment->api_key }}"
                                           readonly>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary mt-2"
                                            onclick="toggleApiKey({{ $assignment->id }})">
                                        Show / Hide
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary mt-2"
                                            onclick="copyText('api-key-{{ $assignment->id }}')">
                                        Copy API Key
                                    </button>
                                </td>

                                <td>
                                    @if($assignment->status)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td style="min-width:180px">
                                    <button type="button"
                                            class="btn btn-sm btn-primary mb-2"
                                            onclick="copyCode('curl-{{ $assignment->id }}')">
                                        Copy cURL Code
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary mb-2"
                                            onclick="copyCode('php-{{ $assignment->id }}')">
                                        Copy PHP Code
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary mb-2"
                                            onclick="copyCode('json-{{ $assignment->id }}')">
                                        Copy Sample JSON
                                    </button>

                                    <details class="mt-2">
                                        <summary>View integration code</summary>

                                        <label class="mt-2">cURL</label>
                                        <textarea id="curl-{{ $assignment->id }}"
                                                  class="form-control mb-2"
                                                  rows="8"
                                                  readonly>{{ $assignment->curl_example }}</textarea>

                                        <label>PHP</label>
                                        <textarea id="php-{{ $assignment->id }}"
                                                  class="form-control mb-2"
                                                  rows="14"
                                                  readonly>{{ $assignment->php_example }}</textarea>

                                        <label>Sample JSON</label>
                                        <textarea id="json-{{ $assignment->id }}"
                                                  class="form-control"
                                                  rows="8"
                                                  readonly>{{ $assignment->sample_json }}</textarea>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    No campaign assignments found.
                                    Assign a campaign to a publisher first.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Expected API Response</strong>
        </div>

        <div class="card-body">
            <p>Successful request: HTTP 201</p>

            <pre class="bg-light p-3"><code>{
  "message": "Lead submitted successfully",
  "lead_reference": "GENERATED_LEAD_REFERENCE",
  "lead_id": 123,
  "status": "new"
}</code></pre>

            <p class="text-muted">
                The exact response fields depend on your existing API controller.
            </p>
        </div>
    </div>

</div>

<script>
function toggleApiKey(id) {
    var field = document.getElementById('api-key-' + id);

    if (field) {
        field.type = field.type === 'password' ? 'text' : 'password';
    }
}

function copyText(id) {
    var field = document.getElementById(id);

    if (!field) {
        return;
    }

    field.type = 'text';
    field.select();
    field.setSelectionRange(0, 999999);

    try {
        var copied = document.execCommand('copy');

        if (copied) {
            alert('Copied successfully');
        } else {
            alert('Copy failed. Please copy manually.');
        }
    } catch (e) {
        alert('Copy failed. Please copy manually.');
    }
}

function copyCode(id) {
    var field = document.getElementById(id);

    if (!field) {
        return;
    }

    field.focus();
    field.select();
    field.setSelectionRange(0, 999999);

    try {
        var copied = document.execCommand('copy');

        if (copied) {
            alert('Integration code copied successfully');
        } else {
            alert('Copy failed. Please copy manually.');
        }
    } catch (e) {
        alert('Copy failed. Please copy manually.');
    }
}
</script>
@endsection
