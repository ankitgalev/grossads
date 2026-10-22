<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        @yield('title', 'Publisher Dashboard') - GrossAds
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
            background: #0f172a;
            color: white;
        }

        .logo {
            padding: 22px;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 1px solid #334155;
        }

        .logo span {
            color: #f97316;
        }

        .menu {
            padding: 15px;
        }

        .menu-title {
            color: #94a3b8;
            font-size: 11px;
            margin: 18px 10px 7px;
            text-transform: uppercase;
        }

        .menu a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            padding: 11px 12px;
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
            background: white;
            height: 70px;
            padding: 0 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
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

        <a href="{{ route('publisher.dashboard') }}">

            <i class="bi bi-speedometer2"></i>

            Dashboard

        </a>


        <div class="menu-title">
            Campaigns
        </div>

        <a href="#">
            <i class="bi bi-megaphone"></i>
            My Campaigns
        </a>


        <div class="menu-title">
            Leads
        </div>

        <a href="#">
            <i class="bi bi-person-lines-fill"></i>
            My Leads
        </a>


        <div class="menu-title">
            API
        </div>

        <a href="{{ route('publisher.api-docs') }}">
            <i class="bi bi-code-slash"></i>
            API Documentation
        </a>

        <a href="#">
            <i class="bi bi-key"></i>
            API Credentials
        </a>

        <a href="#">
            <i class="bi bi-journal-text"></i>
            API Logs
        </a>


        <div class="menu-title">
            Callback
        </div>

        <a href="#">
            <i class="bi bi-arrow-repeat"></i>
            Callback Settings
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
            @yield('page_title', 'Publisher Dashboard')
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