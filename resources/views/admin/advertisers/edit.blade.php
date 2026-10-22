@extends('layouts.admin')

@section('title', 'Edit Advertiser')

@section('page_title', 'Edit Advertiser')

@section('content')

<form
    method="POST"
    action="{{ route('admin.advertisers.update', $advertiser) }}">

    @csrf

    @method('PUT')

    <div class="row">

        <div class="col-md-8">

            <div class="table-card mb-4">

                <h5 class="mb-4">
                    Login Information
                </h5>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $advertiser->user->name) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $advertiser->user->email) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            New Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control">

                        <small class="text-muted">
                            Leave blank to keep current password.
                        </small>

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
                        </label>

                        <input
                            type="text"
                            name="company_name"
                            value="{{ old('company_name', $advertiser->company_name) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Contact Person
                        </label>

                        <input
                            type="text"
                            name="contact_person"
                            value="{{ old('contact_person', $advertiser->contact_person) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $advertiser->phone) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Industry
                        </label>

                        <input
                            type="text"
                            name="industry"
                            value="{{ old('industry', $advertiser->industry) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Website
                        </label>

                        <input
                            type="url"
                            name="website"
                            value="{{ old('website', $advertiser->website) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Country
                        </label>

                        <input
                            type="text"
                            name="country"
                            value="{{ old('country', $advertiser->country) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            City
                        </label>

                        <input
                            type="text"
                            name="city"
                            value="{{ old('city', $advertiser->city) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            State
                        </label>

                        <input
                            type="text"
                            name="state"
                            value="{{ old('state', $advertiser->state) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Pincode
                        </label>

                        <input
                            type="text"
                            name="pincode"
                            value="{{ old('pincode', $advertiser->pincode) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea
                            name="address"
                            rows="3"
                            class="form-control">{{ old('address', $advertiser->address) }}</textarea>

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            rows="3"
                            class="form-control">{{ old('notes', $advertiser->notes) }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="table-card">

                <h5>
                    Account Status
                </h5>

                <hr>

                @if($advertiser->status == 1)

                    <div class="alert alert-success">
                        Active
                    </div>

                @else

                    <div class="alert alert-secondary">
                        Inactive
                    </div>

                @endif

                <button
                    class="btn btn-primary w-100">

                    <i class="bi bi-save"></i>

                    Update Advertiser

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