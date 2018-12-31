@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Admin panel') }} - {{  __('Student registration') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @foreach ($errors->all() as $message)
                        <h3>{{ $message }}</h3>
                    @endforeach
                    <form method="POST" action="{{ route('registerStudent') }}">
                        @csrf
                        <div class="form-group">
                            <label for="identity">{{ __('Identity') }}</label>
                            <input required type="number" class="form-control" id="identity" name="identity" autocomplete="off" autofocus placeholder="{{ __('Enter identity') }}">
                        </div>
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input required type="text" class="form-control" id="full_name" name="full_name" placeholder="{{ __('Full name') }}">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="identity">{{ __('Weeks') }}</label>
                                <input required type="number" class="form-control" id="weeks" name="weeks" placeholder="{{ __('Enter weeks') }}" value="{{ $weeks }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="identity">{{ __('Lessons') }}</label>
                                <input required type="number" class="form-control" id="lessons" name="lessons" placeholder="{{ __('Enter lessons') }}" value="{{ $lessons }}">
                            </div>
                        </div>
                        <div class="col-12 mt-3 text-center">
                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
