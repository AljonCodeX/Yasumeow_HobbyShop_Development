<span class="avatar-wrap">
    @if (Auth::user()->profile_photo)
        <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="avatar-circle">
    @else
        <span class="avatar-circle avatar-icon" aria-hidden="true"><i class="bi bi-person"></i></span>
    @endif

    @if (($favouriteCount ?? 0) > 0)
        <span class="avatar-badge" title="Favourites">{{ $favouriteCount }}</span>
    @endif
</span>