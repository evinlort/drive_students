@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endsection

@section('js')
<script src="{{ asset('js/calendar/cal2.js') }}" defer></script>
@endsection

@section('content')

<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="favoritesModalLabel">
                    {{ __('Choose lesson') }}
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!-- <div class="error"></div> -->

                <div class="col-xl-12">
                    <div class="alert alert-danger modal_errors">
                        <button type="button" class="close-errors" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="icon-alert-holder"><i class="fa fa-ban" aria-hidden="true"></i></div>
                        <span class="errors"></span>
                    </div>
                </div>

                <div id="lessons">
                    <input type="hidden" name="this_date" value />
                    <input type="hidden" name="user_id" value="{{ $user->id }}" />
                    @foreach ($time_line as $time)
                    <div class="time-string row" data-time="{{ $time }}">
                        <div class="btn-group pull-right col-12" style="margin: 20px 25px 0 0;">
                            <label class="top-right-button">{{ $time }} </label>
                            <label class="switch">
                                <input type="checkbox" name="time[{{ $time }}]" value="0">
                                <span class="slider round"></span>
                            </label>
                            <label class="time_info"></label>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    {{ __('Close') }}
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
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div>
                        <hr />
                        <div class="text-center">{{ __('This range') }}</div>
                        <hr />
                    </div>
                    <div id="calendar">
                        @foreach ($days as $day)
                        <div class="cal-day-name">{{ $day }}</div>
                        @endforeach
                        @php
                        $counter = 0;
                        //$add_days = $end_of_this_month;
                        //dd(count($days_a)/7);
                        @endphp
                        @for($x = 0; $x < count($days_a)/7; $x++) <div class="cal-row">
                            @for ($i = 0; $i < 7; $i++) <div data-day-no="{{ $days_a[$counter]['full'] }}" class="cal-box {{ 
                                        $days_a[$counter][1]==1?
                                            'cal-box-gray':
                                            (
                                                $days_a[$counter][1]==2?
                                                    'cal-box-holiday':
                                                    ''
                                            ) 
                                        }} {{ $days_a[$counter][1]==3?
                                            'cal-box-half_day':'' }}" {{ !$days_a[$counter][1]?'data-toggle="modal" data-target="#favoritesModal">':'' }}>
                                {{--
                                        Display one of those
                                        Show in case student has 1 or more lessons on this day`
                                    --}}
                                @if($days_a[$counter][2])
                                <div class="cal-box-point green-point" title="{{ __('This day contains :lessons of your lessons', ['lessons' => $days_a[$counter][2]]) }}"></div>
                                @endif
                                {{--
                                        Show this in case no more free lessons on this day(or make it gray)
                                    <div class="cal-box-point red-point"></div>
                                    --}}
                                {{ $days_a[$counter++][0] }}
                    </div>
                    @endfor
                </div>
                @endfor
                <div>
                    <hr />
                    <div class="text-center">{{ __('Next range') }}</div>
                    <hr />
                </div>
                @if(isset($days_b))
                @php
                $counter = 0;
                //$add_days = $end_of_this_month;
                //dd(count($days_b)/7);
                @endphp
                @for($x = 0; $x < count($days_b)/7; $x++) <div class="cal-row">
                    @for ($i = 0; $i < 7; $i++) <div data-day-no="{{ $days_b[$counter]['full'] }}" class="cal-box {{ 
                                        $days_b[$counter][1]==1?
                                            'cal-box-gray':
                                            (
                                                $days_b[$counter][1]==2?
                                                    'cal-box-holiday':
                                                    ''
                                            ) 
                                        }} {{ $days_b[$counter][1]==3?
                                            'cal-box-half_day':'' }}" {{ !$days_b[$counter][1]?'data-toggle="modal" data-target="#favoritesModal">':'' }}>
                        {{--
                                        Display one of those
                                        Show in case student has 1 or more lessons on this day`
                                    --}}
                        @if($days_b[$counter][2])
                        <div class="cal-box-point green-point" title="{{ __('This day contains :lessons of your lessons', ['lessons' => $days_b[$counter][2]]) }}"></div>
                        @endif
                        {{--
                                        Show this in case no more free lessons on this day(or make it gray)
                                    <div class="cal-box-point red-point"></div>
                                    --}}
                        {{ $days_b[$counter++][0] }}
            </div>
            @endfor
        </div>
        @endfor
        @endif
    </div>
</div>
</div>
</div>
</div>
</div>
@endsection