@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between mb-4">

        <h3>
            Campaigns
        </h3>

        <a href="{{ route('admin.campaigns.create') }}"
           class="btn btn-primary">

            + Create Campaign

        </a>

    </div>


    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Campaign</th>

                            <th>Type</th>

                            <th>Fields</th>

                            <th>Leads</th>

                            <th>Publisher Payout</th>

                            <th>Advertiser Price</th>

                            <th>Status</th>

                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($campaigns as $campaign)

                        <tr>

                            <td>
                                {{ $campaign->id }}
                            </td>

                            <td>

                                <strong>
                                    {{ $campaign->name }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    {{ $campaign->slug }}
                                </small>

                            </td>


                            <td>
                                {{ ucfirst($campaign->campaign_type) }}
                            </td>


                            <td>

                                <!-- {{ $campaign->fields()->count() }} -->
                                {{ $campaign->fields_count}}

                            </td>


                            <td>

                                <span class="badge bg-info">

                                    {{ $campaign->leads_count }}

                                </span>

                            </td>


                            <td>
                                ₹{{ number_format(
                                    $campaign->publisher_payout,
                                    2
                                ) }}
                            </td>


                            <td>
                                ₹{{ number_format(
                                    $campaign->advertiser_price,
                                    2
                                ) }}
                            </td>


                            <td>

                                @if($campaign->status == 'active')

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                @elseif(
                                    $campaign->status == 'paused'
                                )

                                    <span class="badge bg-warning">
                                        Paused
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a href="{{ route(
                                    'admin.campaigns.show',
                                    $campaign->id
                                ) }}"
                                   class="btn btn-sm btn-info">

                                    View

                                </a>


                                <a href="{{ route(
                                    'admin.campaigns.edit',
                                    $campaign->id
                                ) }}"
                                   class="btn btn-sm btn-warning">

                                    Edit

                                </a>
                                <a
                                    href="{{ route('admin.campaigns.assign', $campaign->id) }}"
                                    class="btn btn-sm btn-primary">

                                    <i class="fas fa-user-plus"></i>
                                    Assign
                                </a>

                                <form
                                    action="{{ route(
                                        'admin.campaigns.destroy',
                                        $campaign->id
                                    ) }}"
                                    method="POST"
                                    style="display:inline">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        onclick="return confirm(
                                            'Delete this campaign?'
                                        )"
                                        class="btn btn-sm btn-danger">

                                        Delete

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9"
                                class="text-center py-4">

                                No campaigns found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <div class="mt-3">

        {{ $campaigns->links() }}

    </div>

</div>

@endsection