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
                    
                    @foreach($lessons as $key => $lesson)
                    <div class="row text-center border-bottom mb-2" data-identity="{{ $lesson!==null?$lesson->user->identity:'' }}">
                        <div class="col-3">
                            @if($lesson !== null)
                                {{ Carbon\Carbon::parse($lesson->time)->format('H:i') }}
                            @else
                                {{ $time_line[$key] }}
                            @endif
                        </div>
                        <div class="col-4 text-center border-left">
                            @if($lesson !== null)
                                {{ $lesson->user->name }}
                            @endif
                        </div>
                        <div class="col-4 text-center border-left">
                            @if($lesson !== null)
                                {{ $lesson->user->identity }} 
                            @endif
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