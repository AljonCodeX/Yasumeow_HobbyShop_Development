@extends('layouts.app')

@section('content')
<div class="container">
    <div class="p-5 mb-4 bg-dark text-white rounded-3 text-center">
        <h1 class="display-4 fw-bold">Yasumeow Hobby Shop</h1>
        <p class="lead">Beyblade, Mini 4WD, Pokemon TCG and more in San Ildefonso, Bulacan.</p>
        <a href="#how-to-join" class="btn btn-primary btn-lg me-2">How to Join</a>
        <a href="{{ url('/games') }}" class="btn btn-outline-light btn-lg">See Games</a>
    </div>
</div>
@endsection