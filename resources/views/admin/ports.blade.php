@extends('layouts.admin')
@section('title', 'Manage Ports')
@section('breadcrumb', 'Ports')

@section('content')

<div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1><i class="bi bi-anchor me-2" style="color:var(--admin-accent);"></i>Port Management</h1>
        <p>{{ $ports->total() }} ports — CRUD, Import, Export</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.ports.export') }}" class="btn-admin btn-admin-ghost btn-admin-sm">
            <i class="bi bi-download"></i> Export CSV
        </a>
        <button class="btn-admin btn-admin-ghost btn-admin-sm" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload"></i> Import CSV
        </button>
        <button class="btn-admin btn-admin-primary" data-bs-toggle="modal" data-bs-target="#portModal" onclick="openAddPort()">
            <i class="bi bi-plus-lg"></i> Add Port
        </button>
    </div>
</div>

{{-- FILTERS --}}
<div class="admin-card mb-4">
    <form method="GET" action="{{ route('admin.ports') }}" class="d-flex gap-3 align-items-center flex-wrap">
        <div class="admin-search-wrap" style="flex:1;min-width:200px;">
            <i class="bi bi-search admin-search-icon"></i>
            <input type="text" name="search" class="admin-input" placeholder="Search port name or city..."
                   value="{{ $search ?? '' }}">
        </div>
        <select name="country_id" class="admin-input" style="width:auto;min-width:200px;">
            <option value="">All Countries</option>
            @foreach($countries as $c)
            <option value="{{ $c->id }}" {{ ($countryId ?? '') == $c->id ? 'selected' : '' }}>
                {{ $c->country_name }}
            </option>
            @endforeach
        </select>
        <button type="submit" class="btn-admin btn-admin-primary">
            <i class="bi bi-funnel"></i> Filter
        </button>
        @if($search || $countryId)
        <a href="{{ route('admin.ports') }}" class="btn-admin btn-admin-ghost">
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
                    <th>Port Name</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Type</th>
                    <th>Coordinates</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ports as $port)
                <tr id="port-row-{{ $port->id }}">
                    <td style="color:var(--admin-muted);font-size:12px;">{{ $port->id }}</td>
                    <td style="font-weight:600;">{{ $port->port_name }}</td>
                    <td style="color:var(--admin-muted);">{{ $port->city ?? '—' }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <img src="https://flagcdn.com/w20/{{ strtolower($port->country?->country_code ?? 'xx') }}.png"
                                 width="16" height="11" style="border-radius:2px;" alt="">
                            <span style="font-size:12px;">{{ $port->country?->country_name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="admin-badge badge-user" style="font-size:10px;">{{ $port->port_type }}</span>
                    </td>
                    <td style="font-size:11px;color:var(--admin-muted);">
                        {{ number_format($port->latitude, 3) }}, {{ number_format($port->longitude, 3) }}
                    </td>
                    <td>
                        <span class="admin-badge {{ $port->status === 'Active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ $port->status }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="btn-admin btn-admin-ghost btn-admin-sm btn-admin-icon"
                                    title="Edit"
                                    onclick='openEditPort(@json($port))'>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn-admin btn-admin-danger btn-admin-sm btn-admin-icon"
                                    title="Delete"
                                    onclick="deletePort({{ $port->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:var(--admin-muted);">
                        <i class="bi bi-anchor" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                        No ports found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ports->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--admin-border);">
        {{ $ports->links() }}
    </div>
    @endif
</div>

{{-- ADD / EDIT PORT MODAL --}}
<div class="modal fade admin-modal" id="portModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="portModalTitle">Add Port</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="portForm" onsubmit="submitPort(event)">
                <div class="modal-body">
                    <input type="hidden" id="portId">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="admin-form-label">Port Name *</label>
                            <input type="text" id="fPortName" class="admin-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label">City</label>
                            <input type="text" id="fPortCity" class="admin-input">
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label">Country *</label>
                            <select id="fPortCountry" class="admin-input" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $c)
                                <option value="{{ $c->id }}">{{ $c->country_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label">Port Type *</label>
                            <select id="fPortType" class="admin-input" required>
                                <option value="Seaport">Seaport</option>
                                <option value="Airport">Airport</option>
                                <option value="Inland Port">Inland Port</option>
                                <option value="River Port">River Port</option>
                                <option value="Dry Port">Dry Port</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label">Status *</label>
                            <select id="fPortStatus" class="admin-input" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label">Latitude *</label>
                            <input type="number" step="0.000001" id="fPortLat" class="admin-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="admin-form-label">Longitude *</label>
                            <input type="number" step="0.000001" id="fPortLng" class="admin-input" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-admin-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-admin btn-admin-primary" id="portSubmitBtn">
                        <i class="bi bi-check-lg"></i> Save Port
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- IMPORT MODAL --}}
<div class="modal fade admin-modal" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Ports from CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.ports.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="admin-alert" style="background:rgba(139,92,246,0.1);border:1px solid rgba(139,92,246,0.3);color:#A78BFA;font-size:12px;">
                        <i class="bi bi-info-circle"></i>
                        CSV format: <code>port_name, city, country_code, port_type, latitude, longitude, status</code>
                    </div>
                    <label class="admin-form-label">CSV File *</label>
                    <input type="file" name="csv_file" class="admin-input" accept=".csv,.txt" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-admin btn-admin-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <i class="bi bi-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let editMode = false;

function openAddPort() {
    editMode = false;
    document.getElementById('portModalTitle').textContent = 'Add New Port';
    document.getElementById('portId').value = '';
    document.getElementById('portForm').reset();
    document.getElementById('portSubmitBtn').innerHTML = '<i class="bi bi-plus-lg"></i> Add Port';
}

function openEditPort(port) {
    editMode = true;
    document.getElementById('portModalTitle').textContent = 'Edit Port';
    document.getElementById('portId').value = port.id;
    document.getElementById('fPortName').value = port.port_name;
    document.getElementById('fPortCity').value = port.city || '';
    document.getElementById('fPortCountry').value = port.country_id;
    document.getElementById('fPortType').value = port.port_type;
    document.getElementById('fPortStatus').value = port.status;
    document.getElementById('fPortLat').value = port.latitude;
    document.getElementById('fPortLng').value = port.longitude;
    document.getElementById('portSubmitBtn').innerHTML = '<i class="bi bi-check-lg"></i> Update Port';
    new bootstrap.Modal(document.getElementById('portModal')).show();
}

function submitPort(e) {
    e.preventDefault();
    const portId = document.getElementById('portId').value;
    const url    = portId ? `/admin/ports/${portId}` : '/admin/ports';
    const method = portId ? 'PUT' : 'POST';

    const payload = {
        port_name:  document.getElementById('fPortName').value,
        city:       document.getElementById('fPortCity').value,
        country_id: document.getElementById('fPortCountry').value,
        port_type:  document.getElementById('fPortType').value,
        status:     document.getElementById('fPortStatus').value,
        latitude:   document.getElementById('fPortLat').value,
        longitude:  document.getElementById('fPortLng').value,
    };

    const btn = document.getElementById('portSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

    fetch(url, {
        method,
        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(payload),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            adminToast(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('portModal'))?.hide();
            setTimeout(() => location.reload(), 800);
        } else {
            adminToast(data.message || 'Error saving port.', 'error');
        }
    })
    .catch(() => adminToast('Request failed', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = editMode ? '<i class="bi bi-check-lg"></i> Update Port' : '<i class="bi bi-plus-lg"></i> Add Port';
    });
}

function deletePort(portId) {
    adminDelete(`/admin/ports/${portId}`, 'Delete this port permanently?', () => {
        const row = document.getElementById(`port-row-${portId}`);
        if (row) {
            row.style.opacity = '0';
            row.style.transition = 'opacity 0.3s';
            setTimeout(() => row.remove(), 300);
        }
    });
}
</script>
@endpush
