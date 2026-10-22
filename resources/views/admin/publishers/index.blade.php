@extends('layouts.admin')

@section('title', 'Publishers')

@section('page_title', 'Publishers')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h3 class="mb-1">
            Publishers
        </h3>

        <p class="text-muted mb-0">
            Manage GrossAds publishers
        </p>

    </div>

    <a href="{{ route('admin.publishers.create') }}"
       class="btn btn-primary">

        <i class="bi bi-plus-lg"></i>

        Add Publisher

    </a>

</div>


<div class="table-card">

    <form method="GET"
          action="{{ route('admin.publishers.index') }}"
          class="row g-2 mb-4">

        <div class="col-md-5">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control"
                placeholder="Search company, name, email, phone">

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

            <a href="{{ route('admin.publishers.index') }}"
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

                    <th>Publisher</th>

                    <th>Company</th>

                    <th>Phone</th>

                    <th>Callback URL</th>

                    <th>Status</th>

                    <th width="180">Action</th>

                </tr>

            </thead>

            <tbody>

            @forelse($publishers as $publisher)

                <tr>

                    <td>
                        {{ $publishers->firstItem() + $loop->index }}
                    </td>

                    <td>

                        <strong>
                            {{ $publisher->user->name ?? '-' }}
                        </strong>

                        <br>

                        <small class="text-muted">
                            {{ $publisher->user->email ?? '-' }}
                        </small>

                    </td>

                    <td>
                        {{ $publisher->company_name }}
                    </td>

                    <td>
                        {{ $publisher->phone ?? '-' }}
                    </td>

                    <td>

                        @if($publisher->callback_url)

                            <span
                                class="text-success"
                                title="{{ $publisher->callback_url }}">

                                Configured

                            </span>

                        @else

                            <span class="text-muted">
                                Not configured
                            </span>

                        @endif

                    </td>

                    <td>

                        @if($publisher->status == 1)

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
                                href="{{ route('admin.publishers.show', $publisher) }}"
                                class="btn btn-sm btn-outline-info">

                                <i class="bi bi-eye"></i>

                            </a>

                            <a
                                href="{{ route('admin.publishers.edit', $publisher) }}"
                                class="btn btn-sm btn-outline-primary">

                                <i class="bi bi-pencil"></i>

                            </a>

                            <form
                                method="POST"
                                action="{{ route('admin.publishers.toggle-status', $publisher) }}">

                                @csrf

                                <button
                                    class="btn btn-sm btn-outline-warning"
                                    title="Toggle Status">

                                    <i class="bi bi-power"></i>

                                </button>

                            </form>

                            <form
                                method="POST"
                                action="{{ route('admin.publishers.destroy', $publisher) }}"
                                onsubmit="return confirm('Are you sure you want to delete this publisher?');">

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

                        No publishers found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    <div class="mt-3">

        {{ $publishers->links() }}

    </div>

</div>

@endsection