@extends('layouts.login')

@section('content')
    <div class="container" id="club-select">
        <img src="{{ asset('img/ecr-logo.png') }}" alt="ECRCHS" class="ecr-logo">
        <h3 class="text-center">Club Management | Select Club</h3>
        <div class="card">
            <div class="card-body">
                <p>
                    <span class="float-left"><i class="fas fa-user"></i> {{ $auth->name }}</span>
                    <span class="float-right">
                        <a href="{{ route('logout') }}" id="clubselect-logout">
                            <i class="fas fa-sign-out-alt"></i> Log Out</a></span>
                    <br>
                </p>
                <hr>

                <div class="m-auto col-md-8">
                    <div class="card border-success bg-light mb-3">
                        <div class="card-body">
                            @if($errors->has('code'))
                                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i>
                                    @foreach($errors->get('code') as $error) {{ $error }} @endforeach
                                </div>
                            @endif
                            <form id="join-form" action="{{ route('join') }}" method="POST" autocomplete="off">
                                @csrf
                                <h5 class="card-title text-center">Join w/ Code</h5>
                                <p class="card-text">
                                    <input type="text" class="form-control form-control-lg" id="join-code"
                                           placeholder="ex. BANBAN" minlength="6" maxlength="6" name="code" required>
                                    <button type="submit" id="join-btn" class="btn btn-block btn-success"><i
                                            class="fas fa-plus"></i> Join!
                                    </button>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
                <h5>Student Clubs</h5>
                @if(count($clubSelect['student']))
                    <div class="list-group" style="margin-bottom:8px;">
                        @foreach($clubSelect['student'] as $club)
                            <a href="{{ route('switch-club', ['club' => $club->id]) }}"
                               class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $club->club_name }}</h5>
                                    <p class="pull-right">
                                        <i class="fas fa-arrow-right"></i>
                                        <br>
                                        @if($club->announcements()->recent()->count())
                                            <span class="badge badge-info" style="position:absolute;" rel="tooltip"
                                                  title="Announcements">{{ $club->announcements()->recent()->count() }}</span>
                                        @endif
                                    </p>
                                </div>
                                <p class="mb-1">{{ $club->settings->club_desc }}</p>
                                <small class="text-muted">Joined {{ $club->pivot->created_at->format('m/d/Y') }}</small>
                            </a>
                        @endforeach
                    </div>
                    {{ $clubSelect['student']->links() }}
                @endif
                @if(count($clubSelect['admin']))
                    <h5 style="margin-top:30px;">Admin Clubs</h5>
                    <div class="list-group" style="margin-bottom:8px;">
                        @foreach($clubSelect['admin'] as $club)
                            <a href="{{ route('switch-club', ['club' => $club->id]) }}"
                               class="list-group-item list-group-item-action flex-column align-items-start">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $club->club_name }}</h5>
                                    <p class="pull-right">
                                        <i class="fas fa-arrow-right"></i>
                                        <br>
                                        @if($count = count($club->hours()->marked()->count()))
                                            <span class="badge badge-success" style="position:absolute;" rel="tooltip"
                                                  title="Notifications">{{ $count }}</span>
                                        @endif
                                    </p>
                                </div>
                                <p class="mb-1">{{ $club->settings->club_desc }}</p>
                                <small class="text-muted">Created {{ $club->created_at->format('m/d/Y') }}</small>
                            </a>
                        @endforeach
                    </div>
                    {{ $clubSelect['admin']->links() }}
                @endif
            </div>
        </div>
    </div>
@endsection