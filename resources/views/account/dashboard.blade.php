@extends('account.layout')

@section('account')
<h2 class="checkout-section-title">My Reservations</h2>

<div class="account-stats">
    @foreach ($stats as $stat)
        <div class="account-stat">
            <span class="account-stat-count">{{ $stat['count'] }}</span>
            <i class="bi {{ $stat['icon'] }}"></i>
            <span class="account-stat-label">{{ $stat['label'] }}</span>
        </div>
    @endforeach
</div>
@endsection