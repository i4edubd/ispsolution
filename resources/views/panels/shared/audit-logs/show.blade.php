@extends('layouts.panel')

@section('title', 'Audit Log Details')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Audit Log Details</h3>
            <div class="card-toolbar">
                <a href="{{ route('audit-logs.index') }}" class="btn btn-sm btn-secondary">
                    Back to Logs
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Event Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Event</th>
                            <td><span class="badge bg-info">{{ $auditLog->event }}</span></td>
                        </tr>
                        <tr>
                            <th>Time</th>
                            <td>{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>{{ $auditLog->user->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <th>IP Address</th>
                            <td><code>{{ $auditLog->ip_address }}</code></td>
                        </tr>
                        <tr>
                            <th>User Agent</th>
                            <td><small>{{ $auditLog->user_agent }}</small></td>
                        </tr>
                        <tr>
                            <th>URL</th>
                            <td><small>{{ $auditLog->url }}</small></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Additional Information</h5>
                    @if($auditLog->auditable_type)
                        <p><strong>Model:</strong> {{ class_basename($auditLog->auditable_type) }}</p>
                        <p><strong>Model ID:</strong> {{ $auditLog->auditable_id }}</p>
                    @endif
                    
                    @if($auditLog->tags)
                        <p><strong>Tags:</strong>
                            @foreach($auditLog->tags as $tag)
                                <span class="badge bg-secondary">{{ $tag }}</span>
                            @endforeach
                        </p>
                    @endif
                </div>
            </div>

            @if($auditLog->old_values || $auditLog->new_values)
                <hr>
                <div class="row">
                    @if($auditLog->old_values)
                        <div class="col-md-6">
                            <h5>Old Values</h5>
                            <pre class="bg-light p-3">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                    @if($auditLog->new_values)
                        <div class="col-md-6">
                            <h5>New Values</h5>
                            <pre class="bg-light p-3">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
