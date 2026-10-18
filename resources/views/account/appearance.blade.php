@extends('account.layout')

@section('account')
<h2 class="checkout-section-title">Appearance</h2>

@php
    $themes = [
        'light'  => ['icon' => 'bi-sun',        'title' => 'Light',  'desc' => 'A bright look with white cards.'],
        'dark'   => ['icon' => 'bi-moon-stars', 'title' => 'Dark',   'desc' => 'A darker look that is easier on the eyes at night.'],
        'system' => ['icon' => 'bi-display',    'title' => 'System', 'desc' => 'Match the light or dark setting of your device.'],
    ];
@endphp

@foreach ($themes as $value => $theme)
    <label class="pay-option">
        <input type="radio" name="theme" value="{{ $value }}">
        <span class="pay-option-icon"><i class="bi {{ $theme['icon'] }}"></i></span>
        <span class="pay-option-body">
            <span class="pay-option-title"><span>{{ $theme['title'] }}</span></span>
            <span class="pay-option-desc d-block">{{ $theme['desc'] }}</span>
        </span>
        <span class="pay-option-check"><i class="bi bi-check-lg"></i></span>
    </label>
@endforeach

<p class="reserve-note mt-2">Your choice is saved on this device and browser.</p>
@endsection