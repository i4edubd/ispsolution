@extends('layouts.panel')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Two-Factor Authentication</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($isEnabled)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Two-factor authentication is <strong>enabled</strong> for your account.
                        </div>

                        <p>You have <strong>{{ $recoveryCodesCount }}</strong> recovery codes remaining.</p>

                        <div class="mt-4">
                            <a href="{{ route('2fa.recovery-codes') }}" class="btn btn-primary">
                                View Recovery Codes
                            </a>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#disableModal">
                                Disable 2FA
                            </button>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Two-factor authentication is <strong>disabled</strong> for your account.
                        </div>

                        <p>Add an extra layer of security to your account by enabling two-factor authentication.</p>

                        <div class="mt-4">
                            <a href="{{ route('2fa.enable') }}" class="btn btn-primary">
                                Enable 2FA
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Disable 2FA Modal -->
<div class="modal fade" id="disableModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('2fa.disable') }}">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Disable Two-Factor Authentication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to disable two-factor authentication?</p>
                    <div class="mb-3">
                        <label class="form-label">Confirm with your password</label>
                        <input type="password" name="password" class="form-control" required>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Disable 2FA</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
