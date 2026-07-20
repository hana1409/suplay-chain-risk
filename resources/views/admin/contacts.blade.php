@extends('layouts.admin')
@section('title', 'Contact Messages')
@section('breadcrumb', 'Contact Messages')

@section('content')

<div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1><i class="bi bi-envelope-fill me-2" style="color:var(--admin-accent);"></i>Contact Messages</h1>
        <p>{{ $contacts->total() }} messages total</p>
    </div>
</div>

{{-- FILTERS --}}
<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.contacts') }}" class="d-flex gap-3 align-items-center flex-wrap">
        <div class="admin-search-wrap" style="flex:1;min-width:200px;">
            <i class="bi bi-search admin-search-icon"></i>
            <input type="text" name="search" class="admin-input" placeholder="Search name, email, subject..."
                   value="{{ request('search') }}">
        </div>
        <select name="status" class="admin-input" style="width:auto;min-width:140px;">
            <option value="">All Status</option>
            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
        </select>
        <button type="submit" class="btn-admin btn-admin-primary"><i class="bi bi-funnel"></i> Filter</button>
        @if(request('search') || request('status'))
        <a href="{{ route('admin.contacts') }}" class="btn-admin btn-admin-ghost"><i class="bi bi-x"></i> Clear</a>
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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                <tr id="contact-row-{{ $contact->id }}" style="{{ !$contact->is_read ? 'background:#F0FDF4;' : '' }}">
                    <td style="color:var(--admin-muted);font-size:12px;">{{ $contact->id }}</td>
                    <td>
                        <div style="font-weight:{{ !$contact->is_read ? '700' : '600' }};font-size:13px;">
                            {{ $contact->fullName }}
                        </div>
                    </td>
                    <td style="font-size:12px;color:var(--admin-muted);">{{ $contact->email }}</td>
                    <td style="font-size:12px;color:var(--admin-muted);">{{ $contact->phone ?? '—' }}</td>
                    <td>
                        <div style="font-size:13px;max-width:280px;">{{ Str::limit($contact->subject, 50) }}</div>
                    </td>
                    <td>
                        <button class="admin-badge {{ $contact->is_read ? 'badge-active' : 'badge-inactive' }}"
                                style="cursor:pointer;border:none;"
                                onclick="toggleReadStatus({{ $contact->id }}, {{ $contact->is_read ? 'true' : 'false' }})"
                                id="badge-{{ $contact->id }}">
                            <i class="bi {{ $contact->is_read ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                            {{ $contact->is_read ? 'Read' : 'Unread' }}
                        </button>
                    </td>
                    <td style="font-size:12px;color:var(--admin-muted);">{{ $contact->created_at->format('d M Y, H:i') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.contacts.show', $contact) }}"
                               class="btn-admin btn-admin-ghost btn-admin-sm btn-admin-icon" title="View Message">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn-admin btn-admin-danger btn-admin-sm btn-admin-icon"
                                    title="Delete"
                                    onclick="deleteContact({{ $contact->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:var(--admin-muted);">
                        <i class="bi bi-envelope" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No contact messages found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contacts->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--admin-border);">
        {{ $contacts->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function deleteContact(contactId) {
    if (!confirm('Delete this contact message permanently?')) return;
    
    fetch(`/admin/contacts/${contactId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById(`contact-row-${contactId}`);
            if (row) {
                row.style.opacity = '0';
                row.style.transition = 'opacity 0.3s';
                setTimeout(() => row.remove(), 300);
            }
            adminToast('Contact message deleted successfully.', 'success');
        } else {
            adminToast(data.message || 'Delete failed.', 'error');
        }
    })
    .catch(() => adminToast('Request failed.', 'error'));
}

function toggleReadStatus(contactId, currentStatus) {
    fetch(`/admin/contacts/${contactId}/toggle-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById(`badge-${contactId}`);
            const row = document.getElementById(`contact-row-${contactId}`);
            
            if (data.is_read) {
                badge.className = 'admin-badge badge-active';
                badge.innerHTML = '<i class="bi bi-check-circle-fill"></i> Read';
                row.style.background = '';
            } else {
                badge.className = 'admin-badge badge-inactive';
                badge.innerHTML = '<i class="bi bi-circle"></i> Unread';
                row.style.background = '#F0FDF4';
            }
            
            // Update onclick attribute
            badge.setAttribute('onclick', `toggleReadStatus(${contactId}, ${data.is_read})`);
            
            adminToast(`Message marked as ${data.is_read ? 'read' : 'unread'}.`, 'success');
        } else {
            adminToast('Failed to update status.', 'error');
        }
    })
    .catch(() => adminToast('Request failed.', 'error'));
}
</script>
@endpush
