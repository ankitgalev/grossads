
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Assign Publishers</h3>
            <p class="text-muted mb-0">
                Campaign: <strong>{{ $campaign->name }}</strong>
                |
                Advertiser:
                <strong>{{ optional($campaign->advertiser)->company_name ?? 'N/A' }}</strong>
            </p>
        </div>

        <a href="{{ route('admin.campaigns.index') }}"
           class="btn btn-secondary">
            Back to Campaigns
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <strong>Assign New Publishers</strong>
        </div>

        <div class="card-body">
            <form method="POST"
                  action="{{ route('admin.campaigns.assign.store', $campaign->id) }}">
                @csrf

                <div id="publisherRows">
                    <div class="publisher-row border rounded p-3 mb-3">
                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label>Publisher</label>
                                <select name="publishers[0][publisher_id]"
                                        class="form-control"
                                        required>
                                    <option value="">Select Publisher</option>

                                    @foreach(\App\Models\User::where('role', 'publisher')->orderBy('id')->get() as $publisher)
                                        @if(!$campaign->publishers->contains('id', $publisher->id))
                                            <option value="{{ $publisher->id }}">
                                                {{ $publisher->name ?? $publisher->email }}
                                                (ID: {{ $publisher->id }})
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Payout (₹)</label>
                                <input type="number"
                                       name="publishers[0][publisher_payout]"
                                       class="form-control"
                                       min="0"
                                       step="0.01"
                                       value="{{ $campaign->publisher_payout }}"
                                       required>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Daily Limit</label>
                                <input type="number"
                                       name="publishers[0][daily_limit]"
                                       class="form-control"
                                       min="0"
                                       value="0"
                                       required>
                                <small class="text-muted">0 = unlimited</small>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label>Total Limit</label>
                                <input type="number"
                                       name="publishers[0][total_limit]"
                                       class="form-control"
                                       min="0"
                                       value="0"
                                       required>
                                <small class="text-muted">0 = unlimited</small>
                            </div>

                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="button"
                                        class="btn btn-danger remove-row">
                                    Remove
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <button type="button"
                        id="addPublisher"
                        class="btn btn-outline-primary">
                    + Add Another Publisher
                </button>

                <button type="submit" class="btn btn-success">
                    Save Assignments
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>Assigned Publishers & API Credentials</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead>
                    <tr>
                        <th>Publisher</th>
                        <th>Payout</th>
                        <th>Daily Limit</th>
                        <th>Total Limit</th>
                        <th>API Key</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($campaign->publishers as $publisher)
                        <tr>
                            <td>
                                {{ $publisher->name ?? $publisher->email }}
                                <br>
                                <small class="text-muted">
                                    ID: {{ $publisher->id }}
                                </small>
                            </td>

                            <td>
                                ₹{{ number_format($publisher->pivot->publisher_payout, 2) }}
                            </td>

                            <td>
                                {{ $publisher->pivot->daily_limit ?: 'Unlimited' }}
                            </td>

                            <td>
                                {{ $publisher->pivot->total_limit ?: 'Unlimited' }}
                            </td>

                            <td>
                                <code>{{ $publisher->pivot->api_key }}</code>

                                @if($publisher->pivot->api_key)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary copy-key"
                                            data-key="{{ $publisher->pivot->api_key }}">
                                        Copy
                                    </button>
                                @endif
                            </td>

                            <td>
                                @if($publisher->pivot->status)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Disabled</span>
                                @endif
                            </td>

                            <td>
                                <form action="{{ route('admin.campaigns.assignments.status', [
                                    'campaign' => $campaign->id,
                                    'assignment' => $publisher->pivot->id
                                ]) }}" method="POST" style="display:inline-block;">

                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="btn btn-sm {{ $publisher->pivot->status ? 'btn-warning' : 'btn-success' }}">
                                        {{ $publisher->pivot->status ? 'Disable' : 'Enable' }}
                                    </button>

                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                No publishers assigned yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var container = document.getElementById('publisherRows');
    var index = 1;

    document.getElementById('addPublisher').addEventListener('click', function () {
        var firstRow = container.querySelector('.publisher-row');
        var clone = firstRow.cloneNode(true);

        clone.querySelectorAll('input, select').forEach(function (input) {
            input.name = input.name.replace(
                /publishers\[\d+\]/,
                'publishers[' + index + ']'
            );

            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            } else if (input.name.indexOf('[publisher_payout]') !== -1) {
                input.value = '{{ $campaign->publisher_payout }}';
            } else {
                input.value = '0';
            }
        });

        container.appendChild(clone);
        index++;
    });

    container.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-row')) {
            var rows = container.querySelectorAll('.publisher-row');

            if (rows.length > 1) {
                event.target.closest('.publisher-row').remove();
            }
        }
    });

    document.querySelectorAll('.copy-key').forEach(function (button) {
        button.addEventListener('click', function () {
            var key = this.getAttribute('data-key');

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(key).then(function () {
                    alert('API key copied.');
                });
            } else {
                window.prompt('Copy API key:', key);
            }
        });
    });
});
</script>
@endsection
