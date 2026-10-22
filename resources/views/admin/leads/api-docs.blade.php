@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="mb-4">

        <h3>
            Lead API
        </h3>

        <p class="text-muted">

            Campaign-wise lead submission API

        </p>

    </div>


    @forelse($campaigns as $campaign)

        <div class="card mb-4">

            <div class="card-header">

                <div class="d-flex justify-content-between">

                    <strong>
                        {{ $campaign->name }}
                    </strong>

                    <span class="badge bg-success">
                        Active
                    </span>

                </div>

            </div>


            <div class="card-body">


                <h6>
                    Endpoint
                </h6>


                <div class="bg-dark text-white p-3 rounded mb-3">

                    POST

                    {{ url('/api/v1/leads') }}

                </div>


                <h6>
                    API Key
                </h6>


                <div class="input-group mb-4">

                    <input
                        type="text"
                        readonly
                        class="form-control"
                        value="{{ $campaign->api_key }}">


                    <button
                        class="btn btn-secondary"
                        onclick="copyApiKey(
                            '{{ $campaign->api_key }}'
                        )">

                        Copy

                    </button>

                </div>


                <h6>
                    Headers
                </h6>


<pre class="bg-dark text-white p-3 rounded">X-API-KEY: {{ $campaign->api_key }}
Content-Type: application/json</pre>


                <h6 class="mt-4">
                    Required / Available Fields
                </h6>


                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead>

                            <tr>

                                <th>
                                    Field Name
                                </th>

                                <th>
                                    Key
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Required
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        @foreach(
                            $campaign->fields
                            as $field
                        )

                            <tr>

                                <td>
                                    {{ $field->field_name }}
                                </td>

                                <td>
                                    <code>
                                        {{ $field->field_key }}
                                    </code>
                                </td>

                                <td>
                                    {{ $field->field_type }}
                                </td>

                                <td>

                                    @if($field->is_required)

                                        <span
                                            class="badge bg-danger">

                                            Required

                                        </span>

                                    @else

                                        <span
                                            class="badge bg-secondary">

                                            Optional

                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>


                <h6 class="mt-4">
                    Example Request
                </h6>


<pre class="bg-dark text-white p-3 rounded">POST {{ url('/api/v1/leads') }}

Headers:
X-API-KEY: {{ $campaign->api_key }}
Content-Type: application/json

Body:</pre>


                <pre class="bg-dark text-white p-3 rounded">@php
$example = [];

foreach ($campaign->fields as $field) {

    if ($field->field_type === 'email') {
        $example[$field->field_key] = 'customer@example.com';

    } elseif ($field->field_type === 'phone') {
        $example[$field->field_key] = '9876543210';

    } elseif ($field->field_type === 'number') {
        $example[$field->field_key] = '50000';

    } else {
        $example[$field->field_key] = 'Sample Value';
    }
}

echo json_encode(
    $example,
    JSON_PRETTY_PRINT
);
@endphp</pre>


            </div>

        </div>

    @empty

        <div class="alert alert-warning">

            No active campaigns found.

        </div>

    @endforelse

</div>


<script>

function copyApiKey(key)
{
    navigator.clipboard.writeText(key);

    alert('API key copied.');
}

</script>

@endsection