@extends('layouts.admin')

@section('title', 'Edit Publisher')

@section('page_title', 'Edit Publisher')

@section('content')

<form method="POST"
      action="{{ route('admin.publishers.update', $publisher) }}">

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
                            value="{{ old('name', $publisher->user->name) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $publisher->user->email) }}"
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
                            Confirm New Password
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
                    Publisher Information
                </h5>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Company Name
                        </label>

                        <input
                            type="text"
                            name="company_name"
                            value="{{ old('company_name', $publisher->company_name) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Contact Person
                        </label>

                        <input
                            type="text"
                            name="contact_person"
                            value="{{ old('contact_person', $publisher->contact_person) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $publisher->phone) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Website
                        </label>

                        <input
                            type="url"
                            name="website"
                            value="{{ old('website', $publisher->website) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Callback URL
                        </label>

                        <input
                            type="url"
                            name="callback_url"
                            value="{{ old('callback_url', $publisher->callback_url) }}"
                            class="form-control">

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            rows="4"
                            class="form-control">{{ old('notes', $publisher->notes) }}</textarea>

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

                @if($publisher->status == 1)

                    <div class="alert alert-success">

                        <i class="bi bi-check-circle"></i>

                        Active

                    </div>

                @else

                    <div class="alert alert-secondary">

                        <i class="bi bi-pause-circle"></i>

                        Inactive

                    </div>

                @endif


                <button
                    class="btn btn-primary w-100">

                    <i class="bi bi-save"></i>

                    Update Publisher

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