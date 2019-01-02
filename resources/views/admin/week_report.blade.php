@extends('layouts.app')

@section('content')
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
                    
                    @foreach($dates as $key => $date)
                    <div class="row text-center border-bottom">
                    
                        <div class="col-6">
                            <a href="{{ url('show_date').'/'.$date }}">
                                {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}
                            </a>
                        </div>
                        <div class="col-6 text-left">
                            <a href="{{ url('show_date').'/'.$date }}">
                            @if($lessons_count[$key]['lessons'] > 0)
                                {{ $lessons_count[$key]['lessons'] }} {{ __('lessons registered')  }}
                            @endif
                            </a>
                        </div>

                        </a>
                    </div>
                    @endforeach
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection