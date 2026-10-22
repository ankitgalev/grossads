<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Admin Dashboard') - GrossAds
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <style>

        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #111827;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 22px;
            color: white;
            font-size: 24px;
            font-weight: 700;
            border-bottom: 1px solid #374151;
        }

        .sidebar-logo span {
            color: #f97316;
        }

        .sidebar-menu {
            padding: 15px 10px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #d1d5db;
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 4px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #f97316;
            color: #fff;
        }

        .menu-heading {
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 18px 14px 8px;
        }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
        }

        .content-area {
            padding: 25px;
        }

        .stat-card {
            background: #fff;
            border: 0;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #fff7ed;
            color: #f97316;
            font-size: 22px;
        }

        .table-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
        }

    </style>

    @stack('styles')

</head>

<body>

<div class="sidebar">

    <div class="sidebar-logo">
        Gross<span>Ads</span>
    </div>

    <div class="sidebar-menu">

        <div class="menu-heading">
            Main
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">

            <i class="bi bi-speedometer2"></i>

            Dashboard

        </a>


        <div class="menu-heading">
            Users
        </div>

        <a href="{{ route('admin.publishers.index') }}"
        class="{{ request()->routeIs('admin.publishers.*') ? 'active' : '' }}">

            <i class="bi bi-people"></i>

            Publishers

        </a>


        <a href="{{ route('admin.advertisers.index') }}"
        class="{{ request()->routeIs('admin.advertisers.*') ? 'active' : '' }}">

            <i class="bi bi-building"></i>

            Advertisers

        </a>


        <div class="menu-heading">
            Lead Management
        </div>

        <a href="{{ route('admin.campaigns.index') }}" class="{{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}">
            <i class="bi bi-megaphone"></i>
            Lead Campaigns
        </a>

        <!-- <a href="#">
            <i class="bi bi-diagram-3"></i>
            Campaign Assignments
        </a> -->

        <a href="{{ route('admin.leads.index') }}" class="{{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">
            <i class="bi bi-person-lines-fill"></i>
            All Leads
        </a>


        <div class="menu-heading">
            API
        </div>

        <a href="{{ route('admin.api-docs.index') }}">
            <i class="bi bi-code-slash"></i>
            API Documentation
        </a> 

        <!-- <a href="#">
            <i class="bi bi-key"></i>
            API Credentials
        </a> -->

        <!-- <a href="#">
            <i class="bi bi-journal-code"></i>
            API Logs
        </a> -->


         <div class="menu-heading">
            Meta
        </div>

        <a href="{{ route('admin.meta-integrations.index') }}">
            <i class="bi bi-bar-chart"></i>
            Meta Lead Ads
        </a>

        <!-- <a href="#">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            Campaign Reports
        </a>


        <div class="menu-heading">
            Account
        </div>  -->

        <form method="POST"
              action="{{ route('logout') }}">

            @csrf

            <button type="submit"
                    class="btn w-100 text-start text-light">

                <i class="bi bi-box-arrow-right me-2"></i>

                Logout

            </button>

        </form>

    </div>

</div>


<div class="main-content">

    <div class="topbar">

        <div>
            <strong>
                @yield('page_title', 'Dashboard')
            </strong>
        </div>

        <div class="dropdown">

            <button
                class="btn btn-light dropdown-toggle"
                data-bs-toggle="dropdown">

                <i class="bi bi-person-circle"></i>

                {{ auth()->user()->name }}

            </button>

            <ul class="dropdown-menu dropdown-menu-end">

                <li>
                    <span class="dropdown-item-text">
                        {{ auth()->user()->email }}
                    </span>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>

                    <form method="POST"
                          action="{{ route('logout') }}">

                        @csrf

                        <button class="dropdown-item">

                            <i class="bi bi-box-arrow-right"></i>

                            Logout

                        </button>

                    </form>

                </li>

            </ul>

        </div>

    </div>


    <div class="content-area">

        @if(session('success'))

            <div class="alert alert-success alert-dismissible fade show">

                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

        @endif


        @if(session('error'))

            <div class="alert alert-danger alert-dismissible fade show">

                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

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


        @yield('content')

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

@stack('scripts')

</body>

</html>