<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        @yield('title', 'Advertiser Dashboard') - GrossAds
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
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            position: fixed;
            background: #111827;
        }

        .logo {
            color: white;
            font-size: 24px;
            font-weight: 700;
            padding: 22px;
            border-bottom: 1px solid #374151;
        }

        .logo span {
            color: #f97316;
        }

        .menu {
            padding: 15px;
        }

        .menu-title {
            color: #9ca3af;
            font-size: 11px;
            text-transform: uppercase;
            padding: 15px 10px 7px;
        }

        .menu a {
            display: block;
            color: #d1d5db;
            padding: 11px 12px;
            text-decoration: none;
            border-radius: 7px;
            margin-bottom: 4px;
        }

        .menu a:hover,
        .menu a.active {
            background: #f97316;
            color: white;
        }

        .main {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 0 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content {
            padding: 25px;
        }

    </style>

</head>

<body>

<div class="sidebar">

    <div class="logo">
        Gross<span>Ads</span>
    </div>

    <div class="menu">

        <div class="menu-title">
            Main
        </div>

        <!-- <a href="{{ route('advertiser.dashboard') }}"> -->
        <a href="{{ route('advertiser.dashboard') }}">

            <i class="bi bi-speedometer2"></i>

            Dashboard

        </a>


        <div class="menu-title">
            Campaigns
        </div>

        <a href="{{ route('advertiser.campaigns') }}">
            <i class="bi bi-megaphone"></i>
            My Campaigns
        </a>


        <div class="menu-title">
            Leads
        </div>

        <a href="{{ route('advertiser.leads') }}">
            <i class="bi bi-person-lines-fill"></i>
            All Leads
        </a>

        <a href="#">
            <i class="bi bi-check-circle"></i>
            Accepted Leads
        </a>

        <a href="#">
            <i class="bi bi-x-circle"></i>
            Rejected Leads
        </a>


        <div class="menu-title">
            Reports
        </div>

        <a href="#">
            <i class="bi bi-bar-chart"></i>
            Lead Reports
        </a>


        <div class="menu-title">
            API
        </div>

        <a href="#">
            <i class="bi bi-code-slash"></i>
            API / Postback
        </a>


        <div class="mt-4">

            <form method="POST"
                  action="{{ route('logout') }}">

                @csrf

                <button class="btn btn-outline-light w-100">

                    <i class="bi bi-box-arrow-right"></i>

                    Logout

                </button>

            </form>

        </div>

    </div>

</div>


<div class="main">

    <div class="topbar">

        <strong>
            @yield('page_title', 'Advertiser Dashboard')
        </strong>

        <div>

            <i class="bi bi-person-circle"></i>

            {{ auth()->user()->name }}

        </div>

    </div>

    <div class="content">

        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif

        @if($errors->any())

            <div class="alert alert-danger">

                @foreach($errors->all() as $error)

                    <div>{{ $error }}</div>

                @endforeach

            </div>

        @endif

        @yield('content')

    </div>

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>