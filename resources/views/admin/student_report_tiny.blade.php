@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <label>{{ __('Student:') }}</label>
            <span>{{ $user->name }} {{ $user->identity }}</span>
        </div>
    </div>
    <hr>
    @php
    $counter = 1;
    @endphp
    @foreach($lessons as $lesson)
    <div class="row">
        <div class="col-md-2 col-2 text-right">
            <span>{{ $counter++ }}</span>
        </div>
        <div class="col-md-5 col-5 text-center">
            <span>{{ \Carbon\Carbon::parse($lesson->date)->format('d-m-Y') }}</span>
        </div>
        <div class="col-md-5 col-5 text-left">
            <span>{{ $lesson->time }}</span>
        </div>
    </div>
    @endforeach
</div>

@endsection