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
                    The Sun Also Rises
                </h4>
            </div>
            <div class="modal-body">
                {{ __('Choose lesson') }}

            </div>
            <div class="modal-footer">
                <button type="button" 
                    class="btn btn-default" 
                    data-dismiss="modal">
                        Close
                </button>
                <span class="pull-right">
                    <button type="button" class="btn btn-primary">
                        Add to Favorites
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
                            $add_days = $month_last_day;
                        @endphp
                        @for($x = 0; $x < ceil($days_in_month/7); $x++)
                            <div class="cal-row">
                            @for ($i = 0; $i < 7; $i++)
                                <div data-day-no="{{ $days_a[$counter][0] }}" 
                                    class="cal-box {{ $days_a[$counter][1]?'cal-box-gray':'' }}"
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
