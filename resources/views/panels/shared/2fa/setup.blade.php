@extends('layouts.panel')

@section('title', 'Setup Two-Factor Authentication')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Setup Two-Factor Authentication</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5>Step 1: Scan QR Code</h5>
                        <p>Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.)</p>
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                    </div>

                    <div class="mb-4">
                        <h5>Or enter this key manually:</h5>
                        <div class="alert alert-info">
                            <code>{{ $secret }}</code>
                        </div>
                    </div>

                    <hr>

                    <form method="POST" action="{{ route('2fa.verify') }}">
                        @csrf
                        <h5>Step 2: Verify</h5>
                        <p>Enter the 6-digit code from your authenticator app to verify the setup:</p>
                        
                        <div class="mb-3">
                            <label class="form-label">Verification Code</label>
                            <input type="text" name="code" class="form-control" maxlength="6" pattern="\d{6}" required autofocus>
                            @error('code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Verify and Enable</button>
                            <a href="{{ route('2fa.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
