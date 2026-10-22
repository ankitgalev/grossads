@extends('layouts.admin')

@section('title', 'Advertisers')

@section('page_title', 'Advertisers')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">
            Advertisers
        </h3>

        <p class="text-muted mb-0">
            Manage GrossAds advertisers
        </p>

    </div>

    <a
        href="{{ route('admin.advertisers.create') }}"
        class="btn btn-primary">

        <i class="bi bi-plus-lg"></i>

        Add Advertiser

    </a>

</div>


<div class="table-card">

    <form
        method="GET"
        action="{{ route('admin.advertisers.index') }}"
        class="row g-2 mb-4">

        <div class="col-md-5">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control"
                placeholder="Search company, contact, email, phone">

        </div>

        <div class="col-md-3">

            <select name="status"
                    class="form-select">

                <option value="">
                    All Status
                </option>

                <option value="1"
                    {{ request('status') === '1' ? 'selected' : '' }}>
                    Active
                </option>

                <option value="0"
                    {{ request('status') === '0' ? 'selected' : '' }}>
                    Inactive
                </option>

            </select>

        </div>

        <div class="col-md-2">

            <button class="btn btn-dark w-100">

                <i class="bi bi-search"></i>

                Search

            </button>

        </div>

        <div class="col-md-2">

            <a
                href="{{ route('admin.advertisers.index') }}"
                class="btn btn-outline-secondary w-100">

                Reset

            </a>

        </div>

    </form>


    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead>

                <tr>

                    <th>#</th>

                    <th>Advertiser</th>

                    <th>Company</th>

                    <th>Industry</th>

                    <th>Phone</th>

                    <th>Status</th>

                    <th width="180">Action</th>

                </tr>

            </thead>

            <tbody>

            @forelse($advertisers as $advertiser)

                <tr>

                    <td>
                        {{ $advertisers->firstItem() + $loop->index }}
                    </td>

                    <td>

                        <strong>
                            {{ $advertiser->user->name ?? '-' }}
                        </strong>

                        <br>

                        <small class="text-muted">
                            {{ $advertiser->user->email ?? '-' }}
                        </small>

                    </td>

                    <td>
                        {{ $advertiser->company_name }}
                    </td>

                    <td>
                        {{ $advertiser->industry ?? '-' }}
                    </td>

                    <td>
                        {{ $advertiser->phone ?? '-' }}
                    </td>

                    <td>

                        @if($advertiser->status == 1)

                            <span class="badge bg-success">
                                Active
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                Inactive
                            </span>

                        @endif

                    </td>

                    <td>

                        <div class="btn-group">

                            <a
                                href="{{ route('admin.advertisers.show', $advertiser) }}"
                                class="btn btn-sm btn-outline-info">

                                <i class="bi bi-eye"></i>

                            </a>

                            <a
                                href="{{ route('admin.advertisers.edit', $advertiser) }}"
                                class="btn btn-sm btn-outline-primary">

                                <i class="bi bi-pencil"></i>

                            </a>

                            <form
                                method="POST"
                                action="{{ route('admin.advertisers.toggle-status', $advertiser) }}">

                                @csrf

                                <button
                                    class="btn btn-sm btn-outline-warning">

                                    <i class="bi bi-power"></i>

                                </button>

                            </form>

                            <form
                                method="POST"
                                action="{{ route('admin.advertisers.destroy', $advertiser) }}"
                                onsubmit="return confirm('Are you sure you want to delete this advertiser?');">

                                @csrf

                                @method('DELETE')

                                <button
                                    class="btn btn-sm btn-outline-danger">

                                    <i class="bi bi-trash"></i>

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7"
                        class="text-center py-5 text-muted">

                        No advertisers found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    <div class="mt-3">

        {{ $advertisers->links() }}

    </div>

</div>

@endsection