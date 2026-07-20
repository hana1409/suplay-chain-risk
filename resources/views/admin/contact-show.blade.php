@extends('layouts.admin')
@section('title', 'Contact Message Details')
@section('breadcrumb', 'Contact Message Details')

@section('content')

<div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1><i class="bi bi-envelope-open-fill me-2" style="color:var(--admin-accent);"></i>Message Details</h1>
        <p>From {{ $contact->fullName }}</p>
    </div>
    <a href="{{ route('admin.contacts') }}" class="btn-admin btn-admin-ghost">
        <i class="bi bi-arrow-left"></i> Back to Messages
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        {{-- MESSAGE CARD --}}
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="bi bi-chat-left-text-fill"></i>
                    Message Content
                </div>
                <span class="admin-badge {{ $contact->is_read ? 'badge-active' : 'badge-inactive' }}">
                    <i class="bi {{ $contact->is_read ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                    {{ $contact->is_read ? 'Read' : 'Unread' }}
                </span>
            </div>

            <div style="margin-bottom:20px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--admin-muted);margin-bottom:6px;">
                    Subject
                </div>
                <div style="font-size:16px;font-weight:700;color:var(--admin-text);">
                    {{ $contact->subject }}
                </div>
            </div>

            <div style="padding:20px;background:#F0FDF4;border-radius:8px;border:1px solid var(--admin-border);">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--admin-muted);margin-bottom:10px;">
                    Message
                </div>
                <div style="font-size:14px;line-height:1.7;color:var(--admin-text);white-space:pre-wrap;">{{ $contact->message }}</div>
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="d-flex gap-3 flex-wrap">
            <button class="btn-admin btn-admin-primary" onclick="toggleReadStatus()">
                <i class="bi {{ $contact->is_read ? 'bi-circle' : 'bi-check-circle-fill' }}"></i>
                Mark as {{ $contact->is_read ? 'Unread' : 'Read' }}
            </button>
            
            <a href="mailto:{{ $contact->email }}?subject=Re: {{ urlencode($contact->subject) }}" 
               class="btn-admin btn-admin-ghost">
                <i class="bi bi-reply-fill"></i> Reply via Email
            </a>

            <form action="{{ route('admin.contacts.destroy', $contact) }}" 
                  method="POST" 
                  style="display:inline;"
                  onsubmit="return confirm('Delete this message permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-admin btn-admin-danger">
                    <i class="bi bi-trash"></i> Delete Message
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- SENDER INFO --}}
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="bi bi-person-fill"></i>
                    Sender Information
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px;">
                <div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--admin-muted);margin-bottom:4px;">
                        Full Name
                    </div>
                    <div style="font-size:14px;font-weight:600;color:var(--admin-text);">
                        {{ $contact->fullName }}
                    </div>
                </div>

                <div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--admin-muted);margin-bottom:4px;">
                        Email Address
                    </div>
                    <div style="font-size:13px;color:var(--admin-text);">
                        <a href="mailto:{{ $contact->email }}" style="color:var(--admin-accent);text-decoration:none;">
                            <i class="bi bi-envelope-fill me-1"></i>{{ $contact->email }}
                        </a>
                    </div>
                </div>

                @if($contact->phone)
                <div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--admin-muted);margin-bottom:4px;">
                        Phone Number
                    </div>
                    <div style="font-size:13px;color:var(--admin-text);">
                        <a href="tel:{{ $contact->phone }}" style="color:var(--admin-accent);text-decoration:none;">
                            <i class="bi bi-telephone-fill me-1"></i>{{ $contact->phone }}
                        </a>
                    </div>
                </div>
                @endif

                <div>
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--admin-muted);margin-bottom:4px;">
                        Date Received
                    </div>
                    <div style="font-size:13px;color:var(--admin-text);">
                        <i class="bi bi-calendar-fill me-1" style="color:var(--admin-accent);"></i>
                        {{ $contact->created_at->format('d M Y') }}
                    </div>
                    <div style="font-size:12px;color:var(--admin-muted);margin-top:2px;">
                        <i class="bi bi-clock-fill me-1"></i>
                        {{ $contact->created_at->format('H:i:s') }}
                    </div>
                    <div style="font-size:11px;color:var(--admin-muted);margin-top:4px;">
                        ({{ $contact->created_at->diffForHumans() }})
                    </div>
                </div>
            </div>
        </div>

        {{-- STATS --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">
                    <i class="bi bi-info-circle-fill"></i>
                    Statistics
                </div>
            </div>

            <div style="display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px;background:#F0FDF4;border-radius:6px;">
                    <span style="font-size:12px;color:var(--admin-muted);">Message ID</span>
                    <span style="font-size:13px;font-weight:700;color:var(--admin-text);">#{{ $contact->id }}</span>
                </div>
                
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px;background:#F0FDF4;border-radius:6px;">
                    <span style="font-size:12px;color:var(--admin-muted);">Status</span>
                    <span class="admin-badge {{ $contact->is_read ? 'badge-active' : 'badge-inactive' }}">
                        {{ $contact->is_read ? 'Read' : 'Unread' }}
                    </span>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px;background:#F0FDF4;border-radius:6px;">
                    <span style="font-size:12px;color:var(--admin-muted);">Word Count</span>
                    <span style="font-size:13px;font-weight:700;color:var(--admin-text);">{{ str_word_count($contact->message) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleReadStatus() {
    fetch(`/admin/contacts/{{ $contact->id }}/toggle-read`, {
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
            adminToast(`Message marked as ${data.is_read ? 'read' : 'unread'}.`, 'success');
            setTimeout(() => window.location.reload(), 800);
        } else {
            adminToast('Failed to update status.', 'error');
        }
    })
    .catch(() => adminToast('Request failed.', 'error'));
}
</script>
@endpush
