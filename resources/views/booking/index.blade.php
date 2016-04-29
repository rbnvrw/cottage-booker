@extends('layouts.app')

@section('content')

@if (count($bookings) > 0)

    @foreach ($bookings as $booking)

        <p>{{ $booking->start }} -> {{ $booking->end }}, {{ $booking->description }}</p>

    @endforeach

@else
    <p>Er zijn nog geen boekingen.</p>
@endif

@endsection
