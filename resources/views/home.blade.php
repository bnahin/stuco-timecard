@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" id="{{ $clockedIn ? 'clock-out':'new-activity' }}-card">
                    <div class="card-body">
                        @if($clockedIn)
                            @include('partials.pages.clockout')
                        @else
                            @include('partials.pages.clockin')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
