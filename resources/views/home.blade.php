@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endsection

@section('js')
<script src="{{ asset('js/calendar/cal.js') }}" defer></script>
@endsection

@section('content')

<div class="modal fade" id="favoritesModal" 
    tabindex="-1" role="dialog" 
    aria-labelledby="favoritesModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" 
                    data-dismiss="modal" 
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" 
                    id="favoritesModalLabel">
                    
                </h4>
            </div>
            <div class="modal-body">
                {{ __('Choose lesson') }}
                <div class="error"></div>
                @if ($errors->any())
                <div class="col-xl-6">
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="material-icons">clear</i></span>
                        </button>
                        <div class="icon-alert-holder" ><i class="fa fa-ban" aria-hidden="true"></i></div>
                        <span class="errors"></span>
                    </div>
                </div>
                @endif

                <div id="lessons">
                    <input type="hidden" name="this_date" value />
                    @foreach ($time_line as $time)
                        <div class="time-string" data-time="{{ $time }}">
                            <!-- <label>{{ $time }}</label> -->
                            <div class="btn-group pull-right" style="margin: 20px 25px 0 0;">
                                <label class="top-right-button">{{ $time }} </label>
                                <label class="switch">
                                    <input type="checkbox" name="time[{{ $time }}]" value="0">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <!-- <input type="checkbox" name="time[{{ $time }}]" value="0" /> -->
                        </div>
                    @endforeach
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" 
                    class="btn btn-default" 
                    data-dismiss="modal">
                        Close
                </button>
                <span class="pull-right">
                    <button type="submit" class="btn btn-primary" id="send_time">
                        {{ __('Save') }}
                    </button>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div id="calendar">
                        @foreach ($days as $day)
                            <div class="cal-day-name">{{ $day }}</div>
                        @endforeach
                        @php
                            $counter = 0;
                            //$add_days = $end_of_this_month;
                            //dd(count($days_a)/7);
                        @endphp
                        @for($x = 0; $x < count($days_a)/7; $x++)
                            <div class="cal-row">
                            @for ($i = 0; $i < 7; $i++)
                                <div data-day-no="{{ $days_a[$counter]['full'] }}" 
                                    class="cal-box {{ $days_a[$counter][1]==1?'cal-box-gray':($days_a[$counter][1]==2?'cal-box-holiday':'') }}"
                                    {{ !$days_a[$counter][1]?'data-toggle="modal" data-target="#favoritesModal">':'' }}
                                    >
                                    {{ $days_a[$counter++][0] }}
                                </div>
                            @endfor
                            
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
