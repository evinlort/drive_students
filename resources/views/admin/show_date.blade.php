@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/custom.css') }}">
@endsection

@section('js')
<script src="{{ asset('js/admin/search.js') }}" defer></script>
@endsection
@section('content')

<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-left" id="favoritesModalLabel">

                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <div class="modal-body">
                <input type="hidden" class="hidden_input_time" />
                <input type="hidden" class="hidden_input_date" value="{{ $date }}" />
                <div class="row student_credentials">
                    <div class="col-12">
                        <label>{{ __('Student name') }} : </label>
                        <label id="student_name" class="strong_name"></label>
                    </div>
                </div>
                <div class="row student_credentials">
                    <div class="col-12">
                        <label>{{ __('Student identity') }} : </label>
                        <label id="student_identity" class="strong_name"></label>
                    </div>
                </div>
                <div class="row student_remove">
                    <div class="col-12">
                        <input type="hidden" class="hidden_input_identity" />


                        <label>{{ __('Remove student from this time') }}</label> <button class="remove_student btn btn-danger">{{ __('Remove') }}</button>
                    </div>
                </div>
                <div class="row student_add">
                    <div class="col-12">
                        <label>{{ __('Reserve for user (by identity or name) ') }}</label>
                        <input list="identities" name="student_identity" type="text" class="form-control w-70 d-inline" autocomplete="off" />
                        <datalist id="identities">
                            @foreach($users as $user)
                            <option value="{{ $user->identity }} - {{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </datalist>
                        <button class="btn btn-primary w-25 add_student">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    {{ __('Close') }}
                </button>
                <!-- <span class="pull-right">
                    <button type="submit" class="btn btn-primary" id="send_time">
                        {{ __('Save') }}
                    </button>
                </span> -->
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-2">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <input required type="text" class="form-control" id="search" name="search" autocomplete="off" autofocus placeholder="{{ __('Enter identity or time') }}">
                        </div>
                        <div class="col-6 text-left">
                            <button type="submit" class="btn btn-primary search_submit mr-2">{{ __('Search') }}</button>
                            <button type="submit" class="btn btn-default search_clear">{{ __('Clear') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }} - <strong>{{ date('d/m/Y', strtotime($date)) }}</strong></div>

                <div id="students_list" class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    @foreach($lessons as $key => $lesson)
                    <div class="row text-center border-bottom mb-2 students_list_row" data-name="{{ $lesson!==null?$lesson->user->name:'' }}" data-time="{{ $time_line[$key] }}" data-identity="{{ $lesson!==null?$lesson->user->identity:'' }}">
                        <div class="col-3 student_time">
                            @if($lesson !== null)
                            {{ Carbon\Carbon::parse($lesson->time)->format('H:i') }}
                            @else
                            {{ $time_line[$key] }}
                            @endif
                        </div>
                        <div class="col-4 text-center border-left student_name">
                            @if($lesson !== null)
                            {{ $lesson->user->name }}
                            @endif
                        </div>
                        <div class="col-4 text-center border-left student_identity">
                            @if($lesson !== null)
                            {{ $lesson->user->identity }}
                            @endif
                        </div>
                        <div class="col-1 text-center border-left student_edit">
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