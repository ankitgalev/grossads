@extends('layouts.admin')

@section('title', 'Add Publisher')

@section('page_title', 'Add Publisher')

@section('content')

<form method="POST"
      action="{{ route('admin.publishers.store') }}">

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
                            class="form-control @error('name') is-invalid @enderror">

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror">

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

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


            <div class="table-card mb-4">

                <h5 class="mb-4">
                    Publisher Information
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
                            Website
                        </label>

                        <input
                            type="url"
                            name="website"
                            value="{{ old('website') }}"
                            class="form-control"
                            placeholder="https://example.com">

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Lead Callback URL
                        </label>

                        <input
                            type="url"
                            name="callback_url"
                            value="{{ old('callback_url') }}"
                            class="form-control"
                            placeholder="https://publisher.com/callback">

                        <small class="text-muted">
                            GrossAds will use this URL for lead callback/postback.
                        </small>

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            rows="4"
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

                    <i class="bi bi-info-circle"></i>

                    Publisher will be able to login using the
                    email and password entered here.

                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100">

                    <i class="bi bi-check-lg"></i>

                    Create Publisher

                </button>

                <a
                    href="{{ route('admin.publishers.index') }}"
                    class="btn btn-outline-secondary w-100 mt-2">

                    Cancel

                </a>

            </div>

        </div>

    </div>

</form>

@endsection