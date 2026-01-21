@extends('layouts.panel')

@section('title', 'API Keys')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">API Key Management</h3>
            <div class="card-toolbar">
                <a href="{{ route('api-keys.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New API Key
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Rate Limit</th>
                            <th>Expires</th>
                            <th>Last Used</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apiKeys as $key)
                            <tr>
                                <td>{{ $key->name }}</td>
                                <td>
                                    @if($key->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Revoked</span>
                                    @endif
                                </td>
                                <td>{{ $key->rate_limit }} req/min</td>
                                <td>
                                    @if($key->expires_at)
                                        {{ $key->expires_at->format('Y-m-d') }}
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    @if($key->last_used_at)
                                        {{ $key->last_used_at->diffForHumans() }}
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('api-keys.edit', $key) }}" class="btn btn-sm btn-primary">Edit</a>
                                    @if($key->is_active)
                                        <form method="POST" action="{{ route('api-keys.destroy', $key) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                Revoke
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No API keys found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $apiKeys->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
