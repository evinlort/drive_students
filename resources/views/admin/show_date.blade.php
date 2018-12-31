@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/custom.css') }}">
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @foreach($lessons as $lesson)
                    <div class="row text-center border-bottom mb-2">
                        <div class="col-3">
                                {{ Carbon\Carbon::parse($lesson->time)->format('H:i') }}
                        </div>
                        <div class="col-4 text-center border-left">
                            {{ $lesson->user->name }} 
                        </div>
                        <div class="col-4 text-center border-left">
                            {{ $lesson->user->identity }} 
                        </div>
                        <div class="col-1 text-center border-left">
                            <a href="#"><span class="glyphicon glyphicon-wrench"></span></a>
                        </div>
                    </div>
                    @endforeach
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection