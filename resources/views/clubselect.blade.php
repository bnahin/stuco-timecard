@extends('layouts.login')

@section('content')
    <div class="container" id="club-select">
        <img src="http://ecrchs.net/wp-content/uploads/2015/10/logo.png" alt="ECRCHS" class="ecr-logo">
        <h3 class="text-center">Club Management | Select Club</h3>
        <div class="card">
            <div class="card-body">
                <p>
                    <span class="pull-left"><i class="fas fa-user"></i> {{ $auth->name }}</span>
                    <span class="pull-right">
                        <a href="{{ route('logout') }}" style="color:red" id="clubselect-logout">
                            <i class="fas fa-sign-out-alt"></i> Log Out</a></span>
                    <br>
                </p>
                <hr>

                <div class="m-auto col-md-8">
                    <div class="card border-success bg-light mb-3">
                        <div class="card-body">
                            <h5 class="card-title text-center">Join w/ Code</h5>
                            <p class="card-text">
                                <input type="text" class="form-control form-control-lg" id="join-code"
                                       placeholder="ex. BANBAN" min="6" max="6">
                                <button class="btn btn-block btn-success"><i class="fas fa-plus"></i> Join!</button>
                            </p>
                        </div>
                    </div>
                </div>
                @if(count($clubSelect['student']) && count($clubSelect['admin']))
                    <h5>Student Clubs</h5>
                @endif
                @if(count($clubSelect['student']))
                    <div class="list-group">
                        @foreach($clubSelect['student'] as $club)
                            <a href="{{ route('switch-club', ['club' => $club->id]) }}"
                               class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $club->club_name }}</h5>
                                    <!--TODO: Join date-->
                                    <!-- <small>3 days ago</small>-->
                                    <p class="pull-right"><i class="fas fa-arrow-right"></i></p>

                                </div>
                                <p class="mb-1">{{ $club->settings->club_desc }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
                @if(count($clubSelect['student']) && count($clubSelect['admin']))
                    <h5 style="margin-top:30px;">Admin Clubs</h5>
                @endif
                @if(count($clubSelect['admin']))
                    <div class="list-group">
                        @foreach($clubSelect['admin'] as $club)
                            <a href="{{ route('switch-club', ['club' => $club->id]) }}"
                               class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $club->club_name }}</h5>
                                    <p class="pull-right"><i class="fas fa-arrow-right"></i></p>
                                    <!--TODO: Join date-->
                                    <!-- <small>3 days ago</small>-->
                                </div>
                                <p class="mb-1">{{ $club->settings->club_desc }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection