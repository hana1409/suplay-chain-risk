@extends('layouts.dashboard')
@section('title', 'Admin — Users')
@section('content')
<div class="page-header">
    <div class="page-header-left">
        <div class="page-breadcrumb"><a href="{{ route('admin.dashboard') }}">Admin</a><i class="bi bi-chevron-right"></i><span style="color:var(--accent)">Users</span></div>
        <h1>Manage Users</h1>
    </div>
</div>
<div class="glass-card" style="overflow:hidden;">
    <table class="intel-table">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr></thead>
        <tbody>
            @foreach($users as $u)
            <tr>
                <td style="color:var(--text-muted);">{{ $u->id }}</td>
                <td style="font-weight:600;">{{ $u->name }}</td>
                <td style="color:var(--text-muted);">{{ $u->email }}</td>
                <td><span style="font-size:11px;padding:2px 8px;border-radius:999px;background:{{ $u->role_id===1?'rgba(139,92,246,0.15)':'rgba(100,116,139,0.15)' }};color:{{ $u->role_id===1?'var(--accent)':'var(--text-muted)' }};">{{ $u->role_id===1?'Admin':'User' }}</span></td>
                <td style="color:var(--text-muted);">{{ $u->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding:16px;border-top:1px solid var(--border);">{{ $users->links() }}</div>
</div>
@endsection
