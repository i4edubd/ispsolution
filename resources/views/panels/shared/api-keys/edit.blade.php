@extends('layouts.panel')

@section('title', 'Edit API Key')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit API Key</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('api-keys.update', $apiKey) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label required">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $apiKey->name) }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rate Limit (requests per minute)</label>
                            <input type="number" name="rate_limit" class="form-control" value="{{ old('rate_limit', $apiKey->rate_limit) }}" min="1" max="1000">
                            @error('rate_limit')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Expiration Date</label>
                            <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at', $apiKey->expires_at?->format('Y-m-d')) }}">
                            @error('expires_at')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $apiKey->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">Active</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="invoices.read" id="perm1" {{ in_array('invoices.read', $apiKey->permissions ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm1">Read Invoices</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="payments.create" id="perm2" {{ in_array('payments.create', $apiKey->permissions ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm2">Create Payments</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="customers.read" id="perm3" {{ in_array('customers.read', $apiKey->permissions ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm3">Read Customers</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update API Key</button>
                            <a href="{{ route('api-keys.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
