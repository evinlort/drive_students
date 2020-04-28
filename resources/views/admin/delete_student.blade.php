@extends('layouts.app')

@section('js')
<script src="{{ asset('js/admin/search.js') }}" defer></script>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Choose student to delete') }}</div>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="card-body">
                    <form method="POST" action="{{ route('delete_student') }}">
                        @csrf
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>{{ __('Choose student (by identity or name) ') }}</label>
                                    <input list="identities" name="choosen_student" type="text"
                                        class="form-control w-70 d-inline" autocomplete="off" />
                                    <datalist id="identities">
                                        @foreach($users as $user)
                                            @if($user->settings->is_admin)
                                                @continue
                                            @endif
                                        <option value="{{ $user->identity }}">{{ $user->name }}</option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit"
                                        class="btn btn-primary w-25 delete_student">{{  __('Next') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection