@extends('layouts.admin')
@section('title', 'Manage Users')
@section('breadcrumb', 'Users')

@section('content')

<div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1><i class="bi bi-people-fill me-2" style="color:var(--admin-accent);"></i>Manage Users</h1>
        <p>Total: <strong>{{ $users->total() }}</strong> users registered</p>
    </div>
</div>

{{-- SEARCH --}}
<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.users') }}" class="d-flex gap-3 align-items-center flex-wrap">
        <div class="admin-search-wrap" style="flex:1;min-width:220px;">
            <i class="bi bi-search admin-search-icon"></i>
            <input type="text" name="search" class="admin-input" placeholder="Search name or email..."
                   value="{{ $search ?? '' }}">
        </div>
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="bi bi-search"></i> Search
        </button>
        @if($search)
        <a href="{{ route('admin.users') }}" class="btn-admin btn-admin-ghost">
            <i class="bi bi-x"></i> Clear
        </a>
        @endif
    </form>
</div>

{{-- TABLE --}}
<div class="admin-card" style="padding:0;">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr id="user-row-{{ $u->id }}">
                    <td style="color:var(--admin-muted);font-size:12px;">{{ $u->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="admin-avatar" style="width:34px;height:34px;font-size:13px;">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <span style="font-weight:600;">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--admin-muted);">{{ $u->email }}</td>
                    <td>
                        <span class="admin-badge {{ $u->role_id === 1 ? 'badge-admin' : 'badge-user' }}">
                            {{ $u->role_id === 1 ? 'Admin' : ($u->role_id === 0 ? 'Suspended' : 'User') }}
                        </span>
                    </td>
                    <td>
                        <span class="admin-badge {{ $u->role_id === 0 ? 'badge-inactive' : 'badge-active' }}" id="status-{{ $u->id }}">
                            {{ $u->role_id === 0 ? 'Inactive' : 'Active' }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--admin-muted);">{{ $u->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            @if($u->id !== auth()->id())
                            {{-- Toggle active/inactive --}}
                            <button class="btn-admin btn-admin-ghost btn-admin-sm btn-admin-icon"
                                    title="{{ $u->role_id === 0 ? 'Activate' : 'Suspend' }}"
                                    onclick="toggleUser({{ $u->id }}, this)">
                                <i class="bi {{ $u->role_id === 0 ? 'bi-person-check' : 'bi-person-slash' }}"></i>
                            </button>
                            {{-- Delete --}}
                            <button class="btn-admin btn-admin-danger btn-admin-sm btn-admin-icon"
                                    title="Delete"
                                    onclick="deleteUser({{ $u->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                            @else
                            <span style="font-size:11px;color:var(--admin-muted);">You</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--admin-muted);">
                        <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($users->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--admin-border);">
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function toggleUser(userId, btn) {
    const icon = btn.querySelector('i');
    icon.className = 'bi bi-hourglass-split';
    btn.disabled = true;

    fetch(`/admin/users/${userId}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const statusBadge = document.getElementById(`status-${userId}`);
            if (data.status === 'suspended') {
                statusBadge.className = 'admin-badge badge-inactive';
                statusBadge.textContent = 'Inactive';
                icon.className = 'bi bi-person-check';
                btn.title = 'Activate';
            } else {
                statusBadge.className = 'admin-badge badge-active';
                statusBadge.textContent = 'Active';
                icon.className = 'bi bi-person-slash';
                btn.title = 'Suspend';
            }
            adminToast(data.message, 'success');
        } else {
            adminToast(data.message, 'error');
            icon.className = 'bi bi-person-slash';
        }
    })
    .catch(() => adminToast('Request failed', 'error'))
    .finally(() => { btn.disabled = false; });
}

function deleteUser(userId) {
    if (!confirm('Are you sure you want to permanently delete this user?')) return;

    adminDelete(`/admin/users/${userId}`, null, () => {
        const row = document.getElementById(`user-row-${userId}`);
        if (row) {
            row.style.opacity = '0';
            row.style.transition = 'opacity 0.3s';
            setTimeout(() => row.remove(), 300);
        }
    });
}
</script>
@endpush
