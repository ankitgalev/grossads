
@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Bulk Lead Upload</h3>
            <p class="text-muted mb-0">
                Import leads using CSV or Excel files.
            </p>
        </div>

        <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">
            Back to Leads
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Import Summary --}}
    @if(session()->has('imported_count'))
        <div class="card mb-4">
            <div class="card-header">
                <strong>Import Summary</strong>
            </div>

            <div class="card-body">
                <div class="row text-center">

                    <div class="col-md-4 mb-3">
                        <h3 class="text-success">
                            {{ session('imported_count') }}
                        </h3>
                        <span>Unique Leads</span>
                    </div>

                    <div class="col-md-4 mb-3">
                        <h3 class="text-warning">
                            {{ session('duplicate_count') }}
                        </h3>
                        <span>Duplicate Leads</span>
                    </div>

                    <div class="col-md-4 mb-3">
                        <h3 class="text-danger">
                            {{ session('skipped_count') }}
                        </h3>
                        <span>Skipped Rows</span>
                    </div>

                </div>

                @if(session('import_errors'))
                    <hr>
                    <strong>Skipped Row Details:</strong>

                    <ul class="mt-2 mb-0">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <strong>Upload Leads</strong>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.leads.bulk-upload.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                {{-- Campaign --}}
                <div class="form-group mb-3">
                    <label for="campaign_id">
                        Select Campaign <span class="text-danger">*</span>
                    </label>

                    <select name="campaign_id"
                            id="campaign_id"
                            class="form-control"
                            required>

                        <option value="">-- Select Campaign --</option>

                        @foreach($campaigns as $campaign)
                            <option value="{{ $campaign->id }}"
                                {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                {{ $campaign->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- Publisher --}}
                <div class="form-group mb-3">
                    <label for="publisher_id">
                        Select Publisher <span class="text-danger">*</span>
                    </label>

                    <select name="publisher_id"
                            id="publisher_id"
                            class="form-control"
                            required
                            disabled>

                        <option value="">-- First Select Campaign --</option>

                    </select>

                    <small class="text-muted">
                        Only publishers assigned to the selected campaign will appear.
                    </small>
                </div>

                {{-- File --}}
                <div class="form-group mb-4">
                    <label for="file">
                        Select CSV / Excel File
                        <span class="text-danger">*</span>
                    </label>

                    <input type="file"
                           name="file"
                           id="file"
                           class="form-control"
                           accept=".csv,.txt,.xlsx,.xls"
                           required>

                    <small class="text-muted">
                        Supported formats: CSV, XLS, XLSX. Maximum size: 10 MB.
                    </small>
                </div>

                <button type="submit"
                        id="uploadButton"
                        class="btn btn-primary">

                    Upload Leads
                </button>

                <a href="{{ route('admin.leads.index') }}"
                   class="btn btn-light">
                    Cancel
                </a>

            </form>

        </div>
    </div>

    {{-- Instructions --}}
    <div class="card mt-4">
        <div class="card-header">
            <strong>Important Instructions</strong>
        </div>

        <div class="card-body">
            <ul class="mb-0">
                <li>First row must contain column headings.</li>
                <li>Column headings should match campaign field keys or field names.</li>
                <li>Email and/or Phone must be available for duplicate checking.</li>
                <li>Duplicate leads will also be saved in the database.</li>
                <li>Duplicate checking is limited to the same campaign and last 90 days.</li>
                <li>Publisher selection is mandatory.</li>
            </ul>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const campaignSelect = document.getElementById('campaign_id');
    const publisherSelect = document.getElementById('publisher_id');

    const publisherUrlTemplate =
        @json(route('admin.leads.bulk-upload.publishers', ['campaignId' => '__CAMPAIGN__']));

    const oldPublisherId = @json(old('publisher_id'));

    function resetPublisher(message) {
        publisherSelect.innerHTML = '';

        const option = document.createElement('option');
        option.value = '';
        option.textContent = message;

        publisherSelect.appendChild(option);
        publisherSelect.disabled = true;
    }

    function loadPublishers(campaignId, selectedPublisher = '') {

        resetPublisher('Loading publishers...');

        if (!campaignId) {
            resetPublisher('-- First Select Campaign --');
            return;
        }

        const url = publisherUrlTemplate.replace(
            '__CAMPAIGN__',
            encodeURIComponent(campaignId)
        );

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Unable to load publishers.');
            }

            return response.json();
        })
        .then(publishers => {

            resetPublisher('-- Select Publisher --');

            if (!publishers.length) {
                resetPublisher('No assigned publishers found');
                return;
            }

            publishers.forEach(publisher => {

                const option = document.createElement('option');

                option.value = publisher.id;
                option.textContent = publisher.name;

                if (String(publisher.id) === String(selectedPublisher)) {
                    option.selected = true;
                }

                publisherSelect.appendChild(option);
            });

            publisherSelect.disabled = false;
        })
        .catch(error => {
            console.error(error);
            resetPublisher('Error loading publishers');
        });
    }

    campaignSelect.addEventListener('change', function () {
        loadPublishers(this.value);
    });

    // Restore publisher list after validation error.
    if (campaignSelect.value) {
        loadPublishers(campaignSelect.value, oldPublisherId);
    }

});
</script>

@endsection
