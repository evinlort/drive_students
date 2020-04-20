@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Admin panel') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="row border-bottom mb-2">
                        <div class="col-12">
                            <a href="{{ route('choose_student') }}">{{ __('Add/Remove lessons for student') }}</a>
                        </div>
                    </div>
                    <div class="row border-bottom mb-2">
                        <div class="col-12">
                            <a href="{{ route('week_report') }}">{{ __('Show this week report') }}</a>
                        </div>
                    </div>
                    <div class="row border-bottom mb-2">
                        <div class="col-12">
                            <a href="{{ route('studentRegistration') }}">{{ __('New student registration') }}</a>
                        </div>
                    </div>
                    {{--
                    <div class="row border-bottom mb-2">
                        <div class="col-12">
                            <a href="{{ route('siteSettings') }}">{{ __('Site setting') }}</a>
                        </div>
                    </div>
                    --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
