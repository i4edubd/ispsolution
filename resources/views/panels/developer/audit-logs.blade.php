@extends('panels.layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="mb-4">
                <h2 class="text-2xl font-bold">Audit Logs & Change History</h2>
                <p class="text-gray-600">Track all system changes and user activities</p>
            </div>

            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Total Logs</h6>
                            <h3>{{ number_format($stats['total']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">Today</h6>
                            <h3>{{ number_format($stats['today']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">This Week</h6>
                            <h3>{{ number_format($stats['this_week']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-muted">This Month</h6>
                            <h3>{{ number_format($stats['this_month']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('developer.audit-logs') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="event_filter" class="form-label">Event Type</label>
                            <select id="event_filter" name="event" class="form-select">
                                <option value="">All Events</option>
                                <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Created</option>
                                <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Updated</option>
                                <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                                <option value="login" {{ request('event') === 'login' ? 'selected' : '' }}>Login</option>
                                <option value="logout" {{ request('event') === 'logout' ? 'selected' : '' }}>Logout</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="user_filter" class="form-label">User</label>
                            <input type="text" id="user_filter" name="user" value="{{ request('user') }}" 
                                   placeholder="Search by user name" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>

                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>

                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                            <a href="{{ route('developer.audit-logs') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>

                    <!-- Quick Filters -->
                    <div class="mt-3">
                        <span class="text-muted small">Quick Filters:</span>
                        <button type="button" onclick="setQuickFilter('today')" class="btn btn-sm btn-link">Today</button>
                        <button type="button" onclick="setQuickFilter('yesterday')" class="btn btn-sm btn-link">Yesterday</button>
                        <button type="button" onclick="setQuickFilter('week')" class="btn btn-sm btn-link">This Week</button>
                        <button type="button" onclick="setQuickFilter('month')" class="btn btn-sm btn-link">This Month</button>
                    </div>
                </div>
            </div>

            <!-- Audit Logs Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Audit Logs</h3>
                    <button onclick="exportLogs()" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Event</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Timestamp</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($log->event === 'created') badge-success
                                                @elseif($log->event === 'updated') badge-info
                                                @elseif($log->event === 'deleted') badge-danger
                                                @else badge-secondary
                                                @endif">
                                                {{ ucfirst($log->event) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                                        <td><code>{{ class_basename($log->auditable_type) }}</code></td>
                                        <td class="text-truncate" style="max-width: 200px;" title="{{ $log->description ?? 'No description' }}">
                                            {{ $log->description ?? 'No description' }}
                                        </td>
                                        <td>{{ $log->ip_address }}</td>
                                        <td>
                                            <span title="{{ $log->created_at }}">
                                                {{ $log->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <button onclick="viewDetails({{ $log->id }})" class="btn btn-sm btn-link" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p>No audit logs found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="logDetailsContent">
                <div class="text-center py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
function setQuickFilter(period) {
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    const today = new Date();
    
    switch(period) {
        case 'today':
            dateFrom.value = today.toISOString().split('T')[0];
            dateTo.value = today.toISOString().split('T')[0];
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            dateFrom.value = yesterday.toISOString().split('T')[0];
            dateTo.value = yesterday.toISOString().split('T')[0];
            break;
        case 'week':
            const weekStart = new Date(today);
            weekStart.setDate(today.getDate() - today.getDay());
            dateFrom.value = weekStart.toISOString().split('T')[0];
            dateTo.value = today.toISOString().split('T')[0];
            break;
        case 'month':
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            dateFrom.value = monthStart.toISOString().split('T')[0];
            dateTo.value = today.toISOString().split('T')[0];
            break;
    }
}

function viewDetails(logId) {
    const modal = new bootstrap.Modal(document.getElementById('logDetailsModal'));
    const content = document.getElementById('logDetailsContent');
    
    modal.show();
    
    // Fetch log details via AJAX
    fetch(`/api/audit-logs/${logId}`)
        .then(response => response.json())
        .then(data => {
            content.innerHTML = `
                <dl class="row">
                    <dt class="col-sm-3">Event:</dt>
                    <dd class="col-sm-9"><span class="badge badge-info">${data.event}</span></dd>
                    
                    <dt class="col-sm-3">User:</dt>
                    <dd class="col-sm-9">${data.user || 'System'}</dd>
                    
                    <dt class="col-sm-3">Type:</dt>
                    <dd class="col-sm-9"><code>${data.type}</code></dd>
                    
                    <dt class="col-sm-3">Description:</dt>
                    <dd class="col-sm-9">${data.description || 'N/A'}</dd>
                    
                    <dt class="col-sm-3">IP Address:</dt>
                    <dd class="col-sm-9">${data.ip_address}</dd>
                    
                    <dt class="col-sm-3">User Agent:</dt>
                    <dd class="col-sm-9 small">${data.user_agent || 'N/A'}</dd>
                    
                    <dt class="col-sm-3">Timestamp:</dt>
                    <dd class="col-sm-9">${data.timestamp}</dd>
                    
                    ${data.old_values ? `
                        <dt class="col-sm-3">Old Values:</dt>
                        <dd class="col-sm-9"><pre class="bg-light p-2 rounded"><code>${JSON.stringify(data.old_values, null, 2)}</code></pre></dd>
                    ` : ''}
                    
                    ${data.new_values ? `
                        <dt class="col-sm-3">New Values:</dt>
                        <dd class="col-sm-9"><pre class="bg-light p-2 rounded"><code>${JSON.stringify(data.new_values, null, 2)}</code></pre></dd>
                    ` : ''}
                </dl>
            `;
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Failed to load details</div>';
        });
}

function exportLogs() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '/developer/audit-logs/export?' + params.toString();
}
</script>
@endsection
