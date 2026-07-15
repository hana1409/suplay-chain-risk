@extends('layouts.admin')
@section('title', 'Settings')
@section('breadcrumb', 'Settings')

@section('content')

<div class="admin-page-header">
    <h1><i class="bi bi-gear-fill me-2" style="color:var(--admin-accent);"></i>Settings</h1>
    <p>Manage your profile, password, and system configuration</p>
</div>

<div class="row g-4">

    {{-- LEFT: Profile & Password --}}
    <div class="col-lg-6">

        {{-- PROFILE --}}
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <div class="admin-card-title"><i class="bi bi-person-circle"></i> Admin Profile</div>
            </div>

            <form method="POST" action="{{ route('admin.settings.profile') }}">
                @csrf

                <div class="mb-3">
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                        <div class="admin-avatar" style="width:56px;height:56px;font-size:22px;">
                            {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;">{{ $user->name }}</div>
                            <div style="font-size:12px;color:var(--admin-accent);">Administrator</div>
                            <div style="font-size:12px;color:var(--admin-muted);">{{ $user->email }}</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">Full Name</label>
                    <input type="text" name="name" class="admin-input @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')
                    <div style="color:#FC8181;font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label">Email Address</label>
                    <input type="email" name="email" class="admin-input @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')
                    <div style="color:#FC8181;font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-admin btn-admin-primary">
                    <i class="bi bi-check-lg"></i> Update Profile
                </button>
            </form>
        </div>

        {{-- CHANGE PASSWORD --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title"><i class="bi bi-key-fill"></i> Change Password</div>
            </div>

            <form method="POST" action="{{ route('admin.settings.password') }}">
                @csrf

                <div class="mb-3">
                    <label class="admin-form-label">Current Password</label>
                    <input type="password" name="current_password"
                           class="admin-input @error('current_password') is-invalid @enderror"
                           placeholder="••••••••">
                    @error('current_password')
                    <div style="color:#FC8181;font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">New Password</label>
                    <input type="password" name="password"
                           class="admin-input @error('password') is-invalid @enderror"
                           placeholder="Min. 8 characters">
                    @error('password')
                    <div style="color:#FC8181;font-size:12px;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="admin-input"
                           placeholder="Repeat new password">
                </div>

                <button type="submit" class="btn-admin btn-admin-primary">
                    <i class="bi bi-shield-lock"></i> Change Password
                </button>
            </form>
        </div>

    </div>

    {{-- RIGHT: System Settings --}}
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title"><i class="bi bi-sliders2"></i> System Settings</div>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf

                <div class="mb-3">
                    <label class="admin-form-label">System Name</label>
                    <input type="text" name="system_name" class="admin-input"
                           value="{{ old('system_name', $setting?->system_name ?? 'RiskChain Intelligence') }}"
                           required placeholder="Application name">
                </div>

                <div class="mb-3">
                    <label class="admin-form-label">System Email</label>
                    <input type="email" name="system_email" class="admin-input"
                           value="{{ old('system_email', $setting?->system_email ?? 'admin@system.com') }}"
                           required>
                </div>

                <div class="mb-4">
                    <label class="admin-form-label">Auto Refresh Interval (minutes)</label>
                    <input type="number" name="refresh_interval" class="admin-input"
                           value="{{ old('refresh_interval', $setting?->refresh_interval ?? 60) }}"
                           min="10" max="1440" required>
                    <div style="font-size:11px;color:var(--admin-muted);margin-top:4px;">
                        How often API data should be refreshed automatically (10–1440 minutes).
                    </div>
                </div>

                <button type="submit" class="btn-admin btn-admin-primary">
                    <i class="bi bi-check-lg"></i> Save Settings
                </button>
            </form>

            {{-- SYSTEM INFO --}}
            <div style="margin-top:28px;padding-top:20px;border-top:1px solid var(--admin-border);">
                <div style="font-size:12px;color:var(--admin-muted);margin-bottom:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;">
                    System Information
                </div>
                <div style="display:grid;gap:8px;">
                    @foreach([
                        ['label' => 'Laravel Version', 'value' => app()->version()],
                        ['label' => 'PHP Version', 'value' => phpversion()],
                        ['label' => 'Environment', 'value' => app()->environment()],
                        ['label' => 'Timezone', 'value' => config('app.timezone')],
                        ['label' => 'Database', 'value' => config('database.default')],
                    ] as $info)
                    <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;padding:6px 0;border-bottom:1px solid var(--admin-border);">
                        <span style="color:var(--admin-muted);">{{ $info['label'] }}</span>
                        <span style="font-weight:600;color:var(--admin-accent);">{{ $info['value'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
