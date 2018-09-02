@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if(Session::has('joined-club'))
                    <div class="alert alert-success"><i class="fas fa-check"></i> You have successfully joined
                        <strong>{{ Session::get('joined-club')->club_name }}</strong>.
                    </div>
                    @php Session::remove('joined-club') @endphp
                @endif
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
