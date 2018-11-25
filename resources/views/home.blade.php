@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="css/custom.css">
@endsection
@section('content')
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
                        @for($x = 0; $x < 4; $x++)
                            <div class="cal-row">
                            @for ($i = 0; $i < 7; $i++)
                                <div class="cal-box"></div>
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
