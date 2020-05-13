@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/week_report.css') }}">
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }} : 
                    <strong>
                        {{ \Carbon\Carbon::parse($dates[0])->format('d-m-Y') }} - {{ \Carbon\Carbon::parse($dates[count($dates)-1])->format('d-m-Y') }}
                    </strong>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @foreach($dates as $key => $date)
                    <div class="row text-center border-bottom
                        @if( \Carbon\Carbon::parse($date)->format('Y-m-d') == \Carbon\Carbon::parse('now')->format('Y-m-d') )
                            row_in_present
                        @elseif( \Carbon\Carbon::parse($date)->format('Y-m-d') < \Carbon\Carbon::parse('now')->format('Y-m-d') )
                            row_in_past
                        @endif
                    ">
                    
                        <div class="col-6">
                            <a href="{{ url('show_date').'/'.$date }}">
                                {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}
                            </a>
                        </div>
                        <div class="col-6 text-left">
                            <a href="{{ url('show_date').'/'.$date }}">
                            @if(isset($lessons_count[$key]) && $lessons_count[$key]['lessons'] > 0)
                                {{ $lessons_count[$key]['lessons'] }} {{ __('lessons registered')  }}
                            @endif
                            </a>
                        </div>

                        </a>
                    </div>
                    @endforeach
                    @if(isset($next_dates))
                    <div>
                        <hr />
                        <div class="text-center">{{ __('Next range') }}</div>
                        <hr />
                    </div>
                    @foreach($next_dates as $key => $date)
                    <div class="row text-center border-bottom
                        @if( \Carbon\Carbon::parse($date)->format('Y-m-d') == \Carbon\Carbon::parse('now')->format('Y-m-d') )
                            row_in_present
                        @elseif( \Carbon\Carbon::parse($date)->format('Y-m-d') < \Carbon\Carbon::parse('now')->format('Y-m-d') )
                            row_in_past
                        @endif
                    ">
                    
                        <div class="col-6">
                            <a href="{{ url('show_date').'/'.$date }}">
                                {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}
                            </a>
                        </div>
                        <div class="col-6 text-left">
                            <a href="{{ url('show_date').'/'.$date }}">
                            @if(isset($next_lessons_count[$key]) && $next_lessons_count[$key]['lessons'] > 0)
                                {{ $next_lessons_count[$key]['lessons'] }} {{ __('lessons registered')  }}
                            @endif
                            </a>
                        </div>

                        </a>
                    </div>
                    @endforeach
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection