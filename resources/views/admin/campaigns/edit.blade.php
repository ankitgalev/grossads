@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h3>
                Edit Campaign
            </h3>

            <p class="text-muted mb-0">
                Update campaign details
            </p>

        </div>


        <a
            href="{{ route('admin.campaigns.index') }}"
            class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>
            Back

        </a>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="card">

        <div class="card-header">

            <strong>
                Campaign Information
            </strong>

        </div>


        <div class="card-body">

            <form
                method="POST"
                action="{{ route(
                    'admin.campaigns.update',
                    $campaign->id
                ) }}">

                @csrf

                @method('PUT')

                <input type="hidden" name="campaign_type" value="{{ $campaign->campaign_type }}">
                <input type="hidden" name="publisher_payout" value="{{ $campaign->publisher_payout }}">
                <input type="hidden" name="advertiser_price" value="{{ $campaign->advertiser_price }}">

                <div class="row">


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Campaign Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old(
                                'name',
                                $campaign->name
                            ) }}"
                            required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Advertiser
                        </label>

                        <select
                            name="advertiser_id"
                            class="form-control">

                            <option value="">
                                Select Advertiser
                            </option>

                            @foreach(
                                $advertisers as $advertiser
                            )

                                <option
                                    value="{{ $advertiser->id }}"
                                    {{ $campaign->advertiser_id
                                        == $advertiser->id
                                        ? 'selected'
                                        : ''
                                    }}>

                                    {{ $advertiser->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-control">

                            <option
                                value="active"
                                {{ $campaign->status == 'active'
                                    ? 'selected'
                                    : ''
                                }}>

                                Active

                            </option>

                            <option
                                value="inactive"
                                {{ $campaign->status == 'inactive'
                                    ? 'selected'
                                    : ''
                                }}>

                                Inactive

                            </option>

                        </select>

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="4">{{ old(
                                'description',
                                $campaign->description
                            ) }}</textarea>

                    </div>

                </div>



                <hr>
                <h5 class="mb-3">Advertiser Lead API Delivery</h5>
                <div class="alert alert-info">
                    Enabled delivery sends each lead only once when its status is New.
                    Approved status will not trigger another API request.
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="advertiser_delivery_enabled" value="1"
                           class="form-check-input" id="advertiser_delivery_enabled"
                           {{ old('advertiser_delivery_enabled', $campaign->advertiser_delivery_enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="advertiser_delivery_enabled">
                        Enable advertiser API delivery
                    </label>
                </div>
                <input type="hidden" name="advertiser_delivery_status" value="new">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Delivery Mode</label>
                        <select name="advertiser_delivery_mode" class="form-control" required>
                            <option value="real_time" {{ old('advertiser_delivery_mode', $campaign->advertiser_delivery_mode ?: 'real_time') == 'real_time' ? 'selected' : '' }}>Real-time (send immediately)</option>
                            <option value="cron" {{ old('advertiser_delivery_mode', $campaign->advertiser_delivery_mode) == 'cron' ? 'selected' : '' }}>Cron (process every 5 minutes)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Advertiser API URL</label>
                        <input type="url" name="advertiser_api_url" class="form-control"
                               value="{{ old('advertiser_api_url', $campaign->advertiser_api_url) }}"
                               placeholder="https://advertiser.com/api/leads">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>HTTP Method</label>
                        <select name="advertiser_http_method" class="form-control" required>
                            @foreach(['POST','GET','PUT','PATCH'] as $method)
                                <option value="{{ $method }}" {{ old('advertiser_http_method', $campaign->advertiser_http_method ?: 'POST') == $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Payload Format</label>
                        <select name="advertiser_payload_format" class="form-control" required>
                            @foreach(['json'=>'JSON','form'=>'Form URL Encoded','query'=>'Query Parameters'] as $key=>$label)
                                <option value="{{ $key }}" {{ old('advertiser_payload_format', $campaign->advertiser_payload_format ?: 'json') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Authentication</label>
                        <select name="advertiser_auth_type" class="form-control" required>
                            @foreach(['none'=>'None','bearer'=>'Bearer Token','api_key'=>'API Key','basic'=>'Basic Auth','custom_headers'=>'Custom Headers'] as $key=>$label)
                                <option value="{{ $key }}" {{ old('advertiser_auth_type', $campaign->advertiser_auth_type ?: 'none') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Authentication Config (JSON)</label>
                        <textarea name="advertiser_auth_config" rows="4" class="form-control"
                                  placeholder='{"token":"YOUR_TOKEN"}'>{{ old('advertiser_auth_config', $campaign->advertiser_auth_config ? json_encode($campaign->advertiser_auth_config, JSON_PRETTY_PRINT) : '') }}</textarea>
                        <small class="text-muted">Bearer: token; API Key: header and value; Basic: username and password; Custom: headers object.</small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Field Mapping (JSON)</label>
                        <textarea name="advertiser_field_mapping" rows="4" class="form-control"
                                  placeholder='{"name":"full_name","phone":"mobile"}'>{{ old('advertiser_field_mapping', $campaign->advertiser_field_mapping ? json_encode($campaign->advertiser_field_mapping, JSON_PRETTY_PRINT) : '') }}</textarea>
                        <small class="text-muted">Leave blank to send original campaign field keys.</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Request Timeout (seconds)</label>
                        <input type="number" name="advertiser_timeout" min="1" max="120" class="form-control"
                               value="{{ old('advertiser_timeout', $campaign->advertiser_timeout ?: 15) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Retry Attempts</label>
                        <input type="number" name="advertiser_retry_attempts" min="1" max="10" class="form-control"
                               value="{{ old('advertiser_retry_attempts', $campaign->advertiser_retry_attempts ?: 3) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Cron Batch Size</label>
                        <input type="number" name="advertiser_cron_batch_size" min="1" max="1000" class="form-control"
                               value="{{ old('advertiser_cron_batch_size', $campaign->advertiser_cron_batch_size ?: 100) }}" required>
                    </div>
                </div>


                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fas fa-save"></i>

                    Update Campaign

                </button>

            </form>

        </div>

    </div>

</div>

@endsection