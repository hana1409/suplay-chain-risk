@extends('layouts.app')

@section('title','Register')

@section('content')

<div class="login-page">

    <div class="login-card">

        <div class="login-header">

            <img src="{{ asset('images/logo.png') }}" class="login-logo">

            <h2>Create Account</h2>

            <p>Register to get started</p>

        </div>

        <form>

            <div class="mb-3">

                <label>Full Name</label>

                <input
                    type="text"
                    class="form-control"
                    placeholder="John Doe">

            </div>

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

            <div class="mb-3">

                <label>Confirm Password</label>

                <input
                    type="password"
                    class="form-control"
                    placeholder="********">

            </div>

            <div class="form-check mb-4">

                <input
                    class="form-check-input"
                    type="checkbox"
                    id="agree">

                <label
                    class="form-check-label text-white"
                    for="agree">

                    I agree to the Terms & Conditions

                </label>

            </div>

            <button class="btn-login">

                Register

            </button>

        </form>

        <div class="register-link">

            Already have an account?

            <a href="{{ route('login') }}">

                Login

            </a>

        </div>

    </div>

</div>

@endsection