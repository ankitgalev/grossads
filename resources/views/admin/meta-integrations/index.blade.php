 @extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Meta Lead Ads Integrations</h3>
            <p class="text-muted mb-0">
                Connect Facebook and Instagram lead forms with GrossAds.
            </p>
        </div>
        <a href="{{ route('admin.meta-integrations.create') }}"
           class="btn btn-primary">
            + Add Integration
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            Please check the submitted details.
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Integration</th>
                        <th>Page ID</th>
                        <th>Form ID</th>
                        <th>Campaign</th>
                        <th>Publisher</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($integrations as $integration)
                    <tr>
                        <td>{{ $integrations->firstItem() + $loop->index }}</td>
                        <td>{{ $integration->name }}</td>
                        <td>{{ $integration->page_id }}</td>
                        <td>{{ $integration->form_id ?: 'All forms' }}</td>
                        <td>
                            {{ optional($integration->campaign)->name ?: 'Campaign deleted' }}
                        </td>
                        <td>
                            {{ optional($integration->publisher)->name ?: 'Publisher deleted' }}
                        </td>
                        <td>
                            @if($integration->status)
                                <span class="badge badge-success">Enabled</span>
                            @else
                                <span class="badge badge-secondary">Disabled</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST"
                                  action="{{ route('admin.meta-integrations.toggle', $integration->id) }}"
                                  onsubmit="return confirm('Change this integration status?');">
                                @csrf
                                <button type="submit"
                                        class="btn btn-sm {{ $integration->status ? 'btn-danger' : 'btn-success' }}">
                                    {{ $integration->status ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            No Meta integrations configured yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $integrations->links() }}
        </div>
    </div>
</div>
@endsection