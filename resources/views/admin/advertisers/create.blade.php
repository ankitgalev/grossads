@extends('layouts.admin')

@section('title', 'Add Advertiser')

@section('page_title', 'Add Advertiser')

@section('content')

<form
    method="POST"
    action="{{ route('admin.advertisers.store') }}">

    @csrf

    <div class="row">

        <div class="col-md-8">

            <div class="table-card mb-4">

                <h5 class="mb-4">
                    Login Information
                </h5>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Name <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Password <span class="text-danger">*</span>
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Confirm Password
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control">

                    </div>

                </div>

            </div>


            <div class="table-card">

                <h5 class="mb-4">
                    Advertiser Information
                </h5>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Company Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="company_name"
                            value="{{ old('company_name') }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Contact Person
                        </label>

                        <input
                            type="text"
                            name="contact_person"
                            value="{{ old('contact_person') }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Industry
                        </label>

                        <input
                            type="text"
                            name="industry"
                            value="{{ old('industry') }}"
                            class="form-control"
                            placeholder="Finance, Insurance, Education...">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Website
                        </label>

                        <input
                            type="url"
                            name="website"
                            value="{{ old('website') }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Country
                        </label>

                        <input
                            type="text"
                            name="country"
                            value="{{ old('country', 'India') }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            City
                        </label>

                        <input
                            type="text"
                            name="city"
                            value="{{ old('city') }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            State
                        </label>

                        <input
                            type="text"
                            name="state"
                            value="{{ old('state') }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Pincode
                        </label>

                        <input
                            type="text"
                            name="pincode"
                            value="{{ old('pincode') }}"
                            class="form-control">

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea
                            name="address"
                            rows="3"
                            class="form-control">{{ old('address') }}</textarea>

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            rows="3"
                            class="form-control">{{ old('notes') }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="table-card">

                <h5>
                    Account
                </h5>

                <hr>

                <div class="alert alert-info">

                    Advertiser will receive access to the
                    Advertiser Dashboard after creation.

                </div>

                <button
                    class="btn btn-primary w-100">

                    <i class="bi bi-check-lg"></i>

                    Create Advertiser

                </button>

                <a
                    href="{{ route('admin.advertisers.index') }}"
                    class="btn btn-outline-secondary w-100 mt-2">

                    Cancel

                </a>

            </div>

        </div>

    </div>

</form>

@endsection