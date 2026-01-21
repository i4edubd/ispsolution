@extends('layouts.panel')

@section('title', 'Create API Key')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New API Key</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('api-keys.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label required">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            <small class="text-muted">A descriptive name for this API key</small>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rate Limit (requests per minute)</label>
                            <input type="number" name="rate_limit" class="form-control" value="{{ old('rate_limit', 60) }}" min="1" max="1000">
                            @error('rate_limit')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Expiration Date</label>
                            <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            <small class="text-muted">Leave empty for no expiration</small>
                            @error('expires_at')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="invoices.read" id="perm1">
                                <label class="form-check-label" for="perm1">Read Invoices</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="payments.create" id="perm2">
                                <label class="form-check-label" for="perm2">Create Payments</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="customers.read" id="perm3">
                                <label class="form-check-label" for="perm3">Read Customers</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create API Key</button>
                            <a href="{{ route('api-keys.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
