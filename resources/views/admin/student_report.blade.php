@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/report_print.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Student report') }} - <strong>{{ $user->name }}</strong> <i>{{ $user->identity }}</i></div>
                <div class="row noprint">
                    <div class="col-4 text-center">
                        <a class="btn btn-primary" href="{{ route('download_pdf').'?id='.$user->identity }}">{{ __('Download PDF') }}</a>
                    </div>
                    <div class="col-4 text-center">
                        <a class="btn btn-primary" href="{{ route('download_csv').'?id='.$user->identity }}">{{ __('Download CSV') }}</a>
                    </div>
                    <div class="col-4 text-center">
                        <a class="btn btn-primary" href="#" onClick="print();">{{ __('Print') }}</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <label>{{ __('Student name') }}:</label>
                                <span>{{ $user->name }} {{ $user->identity }}</span>
                            </div>
                        </div>
                        <hr class="noprint">
                        @php
                        $counter = 1;
                        @endphp
                        @foreach($lessons as $lesson)
                        <div class="row">
                            <div class="col-md-2 col-2 text-right">
                                <span>{{ $counter++ }}</span>
                            </div>
                            <div class="col-md-5 col-5 text-center">
                                <span>{{ \Carbon\Carbon::parse($lesson->date)->format('d-m-Y') }}</span>
                            </div>
                            <div class="col-md-5 col-5 text-left">
                                <span>{{ $lesson->time }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection