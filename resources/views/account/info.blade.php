@extends('account.layout')

@section('account')
<h2 class="checkout-section-title">Account Info</h2>

<form method="POST" action="{{ route('account.info.update') }}" enctype="multipart/form-data" class="account-card">
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
        <label for="photo" class="form-label fw-bold small">Profile photo</label>
        <input id="photo" type="file" name="photo" accept="image/*"
               class="form-control @error('photo') is-invalid @enderror">
        <div class="form-text">JPG or PNG, up to 2 MB.</div>
        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-info fw-bold px-4">Save changes</button>
</form>
@endsection