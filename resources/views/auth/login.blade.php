@extends('layouts.app')

@section('title','Login')

@section('content')

<div class="login-page">

    <div class="login-card">

        <div class="login-header">

            <img src="{{ asset('images/logo.png') }}" class="login-logo">

            <h2>Welcome Back!</h2>

            <p>Login to your account</p>

        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">

            @csrf

            <div class="mb-3">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="example@email.com"
                    value="{{ old('email') }}"
                    required>

            </div>

            <div class="mb-3">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="********"
                    required>

            </div>

            <div class="remember">

                <div>

                    <input type="checkbox" name="remember">

                    Remember me

                </div>

                <a href="#">

                    Forgot Password?

                </a>

            </div>

            <button type="submit" class="btn-login">

                Login

            </button>

        </form>

        <div class="divider">

            Or continue with

        </div>

        <div class="social-login justify-content-center">

            <a href="#" class="btn btn-light rounded-circle p-3">

                <i class="bi bi-google fs-4 text-danger"></i>

            </a>

        </div>

        <div class="register-link">

            Don't have an account?

            <a href="{{ route('register') }}">

                Register

            </a>

        </div>

    </div>

</div>

@endsection