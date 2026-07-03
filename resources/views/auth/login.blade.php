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

        <form>

            <div class="mb-3">

                <label>Email</label>

                <input
                    type="email"
                    class="form-control"
                    placeholder="example@email.com">

            </div>

            <div class="mb-3">

                <label>Password</label>

                <input
                    type="password"
                    class="form-control"
                    placeholder="********">

            </div>

            <div class="remember">

                <div>

                    <input type="checkbox">

                    Remember me

                </div>

                <a href="#">

                    Forgot Password?

                </a>

            </div>

            <button class="btn-login">

                Login

            </button>

        </form>

        <div class="divider">

            Or continue with

        </div>

        <div class="social-login">

            <button>

                <i class="bi bi-google"></i>

            </button>

            <button>

                <i class="bi bi-github"></i>

            </button>

            <button>

                <i class="bi bi-microsoft"></i>

            </button>

        </div>

        <div class="register-link">

            Don't have an account?

            <a href="/register">

                Register

            </a>

        </div>

    </div>

</div>

@endsection