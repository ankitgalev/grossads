@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">
                Create Campaign
            </h3>

            <small class="text-muted">
                Create an advertiser campaign with dynamic lead fields
            </small>
        </div>

        <a href="{{ route('admin.campaigns.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>
            Back

        </a>

    </div>


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


    <form method="POST"
          action="{{ route('admin.campaigns.store') }}">

        @csrf


        <div class="row">

            <!-- LEFT -->

            <div class="col-lg-8">

                <div class="card mb-4">

                    <div class="card-header">
                        <strong>Campaign Information</strong>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Advertiser
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="advertiser_id"
                                        class="form-control"
                                        required>

                                    <option value="">
                                        Select Advertiser
                                    </option>

                                    @foreach($advertisers as $advertiser)

                                        <option value="{{ $advertiser->id }}"
                                            {{ old('advertiser_id') == $advertiser->id ? 'selected' : '' }}>

                                            {{ $advertiser->company_name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Campaign Name
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ old('name') }}"
                                       placeholder="Example: Personal Loan"
                                       required>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Campaign Type
                                </label>

                                <select name="campaign_type"
                                        class="form-control">

                                    <option value="lead">
                                        Lead
                                    </option>

                                    <option value="call">
                                        Call
                                    </option>

                                    <option value="survey">
                                        Survey
                                    </option>

                                    <option value="other">
                                        Other
                                    </option>

                                </select>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Status
                                </label>

                                <select name="status"
                                        class="form-control">

                                    <option value="active">
                                        Active
                                    </option>

                                    <option value="paused">
                                        Paused
                                    </option>

                                    <option value="inactive">
                                        Inactive
                                    </option>

                                </select>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Default Publisher Payout
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        ₹
                                    </span>

                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           name="publisher_payout"
                                           class="form-control"
                                           value="{{ old('publisher_payout', 0) }}">

                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Advertiser Price
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        ₹
                                    </span>

                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           name="advertiser_price"
                                           class="form-control"
                                           value="{{ old('advertiser_price', 0) }}">

                                </div>

                            </div>


                            <div class="col-12 mb-3">

                                <label class="form-label">
                                    Description
                                </label>

                                <textarea name="description"
                                          rows="4"
                                          class="form-control"
                                          placeholder="Campaign description">{{ old('description') }}</textarea>

                            </div>


                            <div class="col-12 mb-3">

                                <label class="form-label">
                                    Advertiser Postback URL
                                </label>

                                <input type="url"
                                       name="postback_url"
                                       class="form-control"
                                       value="{{ old('postback_url') }}"
                                       placeholder="https://example.com/postback">

                            </div>

                        </div>

                    </div>

                </div>


                <!-- DYNAMIC FIELDS -->

                <div class="card mb-4">

                    <div class="card-header d-flex justify-content-between align-items-center">

                        <strong>
                            Lead Fields
                        </strong>

                        <button type="button"
                                class="btn btn-sm btn-primary"
                                id="addField">

                            <i class="fas fa-plus"></i>
                            Add Field

                        </button>

                    </div>


                    <div class="card-body">

                        <div id="fieldsContainer">

                            <!-- Fields will be inserted here -->

                        </div>


                        <div id="noFields"
                             class="text-center text-muted py-4">

                            No fields added yet.

                            <br>

                            Click
                            <strong>Add Field</strong>
                            to create campaign lead fields.

                        </div>

                    </div>

                </div>

            </div>


            <!-- RIGHT -->

            <div class="col-lg-4">

                <div class="card mb-4">

                    <div class="card-header">

                        <strong>
                            Campaign Preview
                        </strong>

                    </div>

                    <div class="card-body">

                        <p class="text-muted">

                            Create the campaign first.

                            After creation you can assign publishers
                            and generate publisher-specific API credentials.

                        </p>

                        <hr>

                        <div class="small">

                            <strong>Flow</strong>

                            <ol class="mt-2">

                                <li>Select advertiser</li>

                                <li>Create campaign</li>

                                <li>Add dynamic fields</li>

                                <li>Save campaign</li>

                                <li>Assign publishers</li>

                                <li>Generate API</li>

                            </ol>

                        </div>

                    </div>

                </div>


                <button type="submit"
                        class="btn btn-success btn-lg w-100">

                    <i class="fas fa-save"></i>

                    Create Campaign

                </button>

            </div>

        </div>

    </form>

</div>


<style>

.field-row {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f9fa;
}

.field-row:hover {
    background: #ffffff;
}

</style>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        let fieldIndex = 0;

        const container =
            document.getElementById(
                'fieldsContainer'
            );

        const noFields =
            document.getElementById(
                'noFields'
            );


        document.getElementById(
            'addField'
        ).addEventListener(
            'click',
            function () {

                noFields.style.display =
                    'none';


                const html = `

                    <div class="field-row">

                        <div class="d-flex justify-content-between mb-3">

                            <strong>
                                Field ${fieldIndex + 1}
                            </strong>

                            <button
                                type="button"
                                class="btn btn-sm btn-danger remove-field">

                                <i class="fas fa-trash"></i>

                            </button>

                        </div>


                        <div class="row">

                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Field Name
                                </label>

                                <input
                                    type="text"
                                    name="fields[${fieldIndex}][field_name]"
                                    class="form-control"
                                    placeholder="Example: Full Name"
                                    required>

                            </div>


                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Field Key
                                </label>

                                <input
                                    type="text"
                                    name="fields[${fieldIndex}][field_key]"
                                    class="form-control"
                                    placeholder="Example: name"
                                    required>

                            </div>


                            <div class="col-md-4 mb-3">

                                <label class="form-label">
                                    Field Type
                                </label>

                                <select
                                    name="fields[${fieldIndex}][field_type]"
                                    class="form-control field-type">

                                    <option value="text">
                                        Text
                                    </option>

                                    <option value="email">
                                        Email
                                    </option>

                                    <option value="phone">
                                        Phone
                                    </option>

                                    <option value="number">
                                        Number
                                    </option>

                                    <option value="date">
                                        Date
                                    </option>

                                    <option value="select">
                                        Select
                                    </option>

                                    <option value="textarea">
                                        Textarea
                                    </option>

                                </select>

                            </div>


                            <div class="col-md-8 mb-3 options-wrapper"
                                 style="display:none;">

                                <label class="form-label">

                                    Select Options

                                </label>

                                <textarea
                                    name="fields[${fieldIndex}][options]"
                                    rows="2"
                                    class="form-control"
                                    placeholder="Male, Female, Other"></textarea>

                                <small class="text-muted">

                                    Separate options with comma or new line.

                                </small>

                            </div>


                            <div class="col-md-4 mb-3">

                                <label class="form-label d-block">

                                    Required?

                                </label>

                                <div class="form-check mt-2">

                                    <input
                                        type="checkbox"
                                        name="fields[${fieldIndex}][is_required]"
                                        value="1"
                                        class="form-check-input">

                                    <label class="form-check-label">

                                        This field is required

                                    </label>

                                </div>

                            </div>

                        </div>

                    </div>

                `;


                container.insertAdjacentHTML(
                    'beforeend',
                    html
                );


                fieldIndex++;

            }
        );


        container.addEventListener(
            'click',
            function (event) {

                if (
                    event.target.closest(
                        '.remove-field'
                    )
                ) {

                    event.target.closest(
                        '.field-row'
                    ).remove();


                    if (
                        container.children.length === 0
                    ) {

                        noFields.style.display =
                            'block';

                    }

                }

            }
        );


        container.addEventListener(
            'change',
            function (event) {

                if (
                    event.target.classList.contains(
                        'field-type'
                    )
                ) {

                    const row =
                        event.target.closest(
                            '.field-row'
                        );

                    const options =
                        row.querySelector(
                            '.options-wrapper'
                        );


                    if (
                        event.target.value ===
                        'select'
                    ) {

                        options.style.display =
                            'block';

                    } else {

                        options.style.display =
                            'none';

                    }

                }

            }
        );

    }
);

</script>

@endsection