@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/custom.css') }}">
@endsection

@section('js')
<script src="{{ asset('js/admin/search.js') }}" defer></script>
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
                    <div></div>   
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection