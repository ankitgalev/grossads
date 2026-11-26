
 @extends('layouts.app') 


<style>
    .login-page {
        min-height: calc(100vh - 70px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 15px;
        background: linear-gradient(135deg, #eef2f7 0%, #e5ecf5 100%);
    }

    .login-card {
        width: 100%;
        max-width: 430px;
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.10);
    }

    .login-header {
        background: #172337;
        color: #fff;
        text-align: center;
        padding: 30px 20px;
    }

    .login-logo {
        font-size: 29px;
        font-weight: 800;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .login-logo span {
        color: #ff812d;
    }

    .login-header p {
        color: #cbd5e1;
        font-size: 13px;
        margin: 0;
    }

    .login-body {
        padding: 30px;
        background: #fff;
    }

    .login-body .form-control {
        height: 45px;
        border-radius: 6px;
        border: 1px solid #dce2ea;
        font-size: 14px;
    }

    .login-body .form-control:focus {
        border-color: #ff812d;
        box-shadow: 0 0 0 0.15rem rgba(255, 129, 45, 0.15);
    }

    .login-body label {
        font-size: 13px;
        font-weight: 600;
        color: #344054;
    }

    .password-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 12px;
        cursor: pointer;
        color: #7b8492;
    }

    .login-btn {
        background: #ff812d;
        border: 1px solid #ff812d;
        color: #fff;
        font-weight: 600;
        height: 45px;
        border-radius: 6px;
        transition: 0.2s;
    }

    .login-btn:hover {
        background: #ed6d1c;
        border-color: #ed6d1c;
        color: #fff;
    }

    .login-footer {
        text-align: center;
        padding: 15px;
        background: #f8fafc;
        border-top: 1px solid #edf0f4;
        color: #87909d;
        font-size: 12px;
    }

    @media (max-width: 576px) {
        .login-body {
            padding: 25px 20px;
        }

        .login-page {
            padding: 25px 12px;
        }
    }
</style>

<div class="login-page">
    <div class="login-card">

        <div class="login-header">
            <div class="login-logo">
                Gross<span>Ads</span>
            </div>
            <p>Lead Management System</p>
        </div>

        <div class="login-body">

            <h4 class="font-weight-bold mb-1">Welcome Back!</h4>
            <p class="text-muted small mb-4">
                Please login to your account.
            </p>

            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Login failed!</strong>
                    <ul class="mb-0 pl-3 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group mb-3">
                    <label for="email">Email Address</label>
                    <div class="password-wrapper">
                        <input
                            id="email"
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email address"
                            required
                            autocomplete="email"
                            autofocus
                        >
                    </div>

                    @error('email')
                        <span class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="password">Password</label>

                    <div class="password-wrapper">
                        <input
                            id="password"
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >

                        <span class="password-toggle"
                              onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </span>
                    </div>

                    @error('password')
                        <span class="invalid-feedback d-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="remember"
                            id="remember"
                            {{ old('remember') ? 'checked' : '' }}
                        >

                        <label class="form-check-label font-weight-normal"
                               for="remember">
                            Remember Me
                        </label>
                    </div>

                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="small text-primary">
                            Forgot Password?
                        </a>
                    @endif

                </div>

                <button type="submit" class="btn login-btn btn-block">
                    <i class="fas fa-sign-in-alt mr-1"></i>
                    Login to Account
                </button>

            </form>
        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} GrossAds. All Rights Reserved.
        </div>

    </div>
</div>

<script>
    function togglePassword() {
        var password = document.getElementById('password');
        var icon = document.getElementById('passwordIcon');

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>


