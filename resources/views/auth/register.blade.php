@extends('layouts.app')

@section('title','Register')

@section('content')

<div class="login-page">

    <div class="login-card">

        <div class="login-header">

            <img src="{{ asset('images/hand.png') }}" class="login-logo">

            <h2>Create Account</h2>

            <p>Register to get started</p>

        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">

            @csrf

            <div class="mb-3">

                <label>Full Name</label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    placeholder="John Doe"
                    value="{{ old('name') }}"
                    required>

            </div>

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

            <div class="mb-3">

                <label>Confirm Password</label>

                <input
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="********"
                    required>

            </div>

            <div class="form-check mb-4">

                <input
                    class="form-check-input"
                    type="checkbox"
                    id="agree"
                    required>

                <label
                    class="form-check-label text-white"
                    for="agree">

                    I agree to the Terms & Conditions

                </label>

            </div>

            <button type="submit" class="btn-login">

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