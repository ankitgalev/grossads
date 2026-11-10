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