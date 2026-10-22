@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h3>Add Meta Lead Ads Integration</h3>
        <p class="text-muted mb-0">
            Enter the Meta credentials and choose where incoming leads should be stored.
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct these errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.meta-integrations.store') }}">
                @csrf

                <div class="form-group">
                    <label>Integration Name</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', 'Meta Lead Ads') }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Facebook Page ID</label>
                        <input type="text" name="page_id" class="form-control"
                               value="{{ old('page_id') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Lead Form ID (optional)</label>
                        <input type="text" name="form_id" class="form-control"
                               value="{{ old('form_id') }}">
                        <small class="text-muted">
                            Leave blank if this integration should accept all forms for this Page.
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Campaign</label>
                    <select name="campaign_id" class="form-control" required>
                        <option value="">-- Select Campaign --</option>
                        @foreach($campaigns as $campaign)
                            <option value="{{ $campaign->id }}"
                                {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                {{ $campaign->name }}
                                ({{ ucfirst($campaign->status) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Publisher Account</label>
                    <select name="publisher_id" id="publisher_id"
                            class="form-control" required>
                        <option value="">-- Select Publisher --</option>
                        @foreach($publishers as $publisher)
                            <option value="{{ $publisher->id }}"
                                {{ old('publisher_id') == $publisher->id ? 'selected' : '' }}>
                                {{ $publisher->name }} — {{ $publisher->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Campaign Publisher Assignment (optional)</label>
                    <select name="campaign_publisher_id" class="form-control">
                        <option value="">-- No specific assignment --</option>
                        @foreach($assignments as $assignment)
                            <option value="{{ $assignment->id }}"
                                {{ old('campaign_publisher_id') == $assignment->id ? 'selected' : '' }}>
                                Assignment #{{ $assignment->id }}
                                — {{ optional($assignment->campaign)->name }}
                                — {{ optional($assignment->publisher)->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">
                        If selected, the assignment must match the campaign and publisher above.
                    </small>
                </div>

                <hr>
                <h5>Meta Credentials</h5>
                <div class="alert alert-warning">
                    Keep these credentials private. Do not put your App Secret or Page Access Token
                    in frontend JavaScript or share them publicly.
                </div>

                <div class="form-group">
                    <label>Meta App Secret</label>
                    <input type="password" name="app_secret" class="form-control"
                           autocomplete="new-password" required>
                </div>

                <div class="form-group">
                    <label>Page Access Token</label>
                    <textarea name="page_access_token" class="form-control"
                              rows="3" autocomplete="off" required>{{ old('page_access_token') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Webhook Verify Token</label>
                    <input type="text" name="verify_token" class="form-control"
                           value="{{ old('verify_token') }}" required>
                    <small class="text-muted">
                        Create your own long, random value. You will enter the same value in Meta Webhooks settings.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    Save Integration
                </button>
                <a href="{{ route('admin.meta-integrations.index') }}"
                   class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection