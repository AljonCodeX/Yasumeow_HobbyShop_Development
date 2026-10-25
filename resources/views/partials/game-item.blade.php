<div class="col game-col" data-section="{{ $sectionSlug }}">
    <div class="shop-item">
        <a href="{{ url('/games/' . $game->slug) }}" class="shop-item-link">
            <div class="shop-item-img">
                @if ($game->image)
                    <img src="{{ asset('storage/' . $game->image) }}" alt="{{ $game->name }}">
                @else
                    <i class="bi {{ $game->category?->icon ?? 'bi-controller' }}"></i>
                @endif
            </div>
        </a>

        <div class="shop-item-body">
            <span class="shop-item-cat">{{ $game->category?->name ?? 'Game' }}</span>
            <a href="{{ url('/games/' . $game->slug) }}" class="shop-item-link">
                <h3 class="shop-item-name">{{ $game->name }}</h3>
            </a>
            <p class="game-item-desc">{{ $game->description }}</p>

            <a href="{{ url('/games/' . $game->slug) }}" class="shop-item-btn">View details</a>
        </div>
    </div>
</div>