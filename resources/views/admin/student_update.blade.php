@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/update.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ __('Update student') }} - <strong>{{ $user->name }}</strong> <i>{{ $user->identity }}</i></div>
            <div class="card-body">
                <div class="container">
                    <div class="row">
                        <div class="col-6 text-center">{{ __('Weeks') }}</div>
                        <div class="col-6 text-center">{{ __('Lessons') }}</div>
                    </div>
                    <form action="{{ route('updateStudentSettings') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <div class="row mb-5">
                            <div class="col-6 text-center">
                                <input type="number" min=0 name="weeks" id="weeks" class="form-control" placeholder="{{ __('Enter weeks') }}" value="{{ $settings->weeks }}">
                            </div>
                            <div class="col-6 text-center">
                                <input type="number" min=0 name="lessons" id="lessons" class="form-control" placeholder="{{ __('Enter lessons') }}" value="{{ $settings->lessons }}">
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-6 text-center">{{ __('Phone Number') }}</div>
                            <div class="col-6 text-center">{{ __('Address') }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-center">
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="{{ __('Phone Number') }}" value="{{ $user->phone }}">
                            </div>
                            <div class="col-6 text-center">
                                <input type="text" name="address" id="address" class="form-control" placeholder="{{ __('Address') }}" value="{{ $user->address }}">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection