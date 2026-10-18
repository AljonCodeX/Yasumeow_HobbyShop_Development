@if (Auth::user()->profile_photo)
    <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="avatar-circle">
@else
    <span class="avatar-circle avatar-icon" aria-hidden="true"><i class="bi bi-person"></i></span>
@endif