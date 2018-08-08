@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" id="{{ $clockedOut ? 'clock-in':'new-activity' }}-card">
                    <div class="card-body">
                        @if($clockedOut)
                            @include('partials.clockin')
                        @else
                            @include('partials.clockout')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
