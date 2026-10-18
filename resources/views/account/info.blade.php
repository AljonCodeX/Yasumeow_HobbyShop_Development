@extends('account.layout')

@section('account')
<h2 class="checkout-section-title">Account Info</h2>

<form method="POST" action="{{ route('account.info.update') }}" class="account-card">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="name" class="form-label fw-bold small">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
               class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold small">Email</label>
        <input type="email" value="{{ Auth::user()->email }}" class="form-control" disabled>
    </div>

    <div class="mb-4">
        <label for="phone" class="form-label fw-bold small">Mobile number</label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
               placeholder="09171234567" class="form-control @error('phone') is-invalid @enderror">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-info fw-bold px-4">Save changes</button>
</form>

<h2 class="checkout-section-title mt-4">Change Password</h2>

<form method="POST" action="{{ route('account.password.update') }}" class="account-card">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="current_password" class="form-label fw-bold small">Current password</label>
        <input id="current_password" type="password" name="current_password" autocomplete="current-password"
               class="form-control @error('current_password', 'password') is-invalid @enderror" required>
        @error('current_password', 'password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="new_password" class="form-label fw-bold small">New password</label>
        <input id="new_password" type="password" name="password" autocomplete="new-password"
               class="form-control @error('password', 'password') is-invalid @enderror" required>
        <div class="form-text">At least 8 characters.</div>
        @error('password', 'password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-4">
        <label for="new_password_confirmation" class="form-label fw-bold small">Confirm new password</label>
        <input id="new_password_confirmation" type="password" name="password_confirmation"
               autocomplete="new-password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-info fw-bold px-4">Change password</button>
</form>
@endsection