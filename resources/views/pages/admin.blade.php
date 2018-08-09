@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2>Class Administration</h2>
                <div class="card" id="admin-card">
                    <div class="card-body">
                        <div class="container row">
                            <div class="col-md-3" id="adm-nav-col">
                                <ul class="nav flex-column" id="admin-nav">
                                    <li class="nav-item active">
                                        <a href="" class="nav-link" data-target="studentm"><i class="fas fa-user"></i> Student Management </a>
                                        <!-- Class import, student search & add, view hours -->
                                    </li>
                                    <li class="nav-item">
                                        <a href="" class="nav-link" data-target="tconfig"><i class="fas fa-clock"></i> Timecard Configuration</a>
                                        <!--Turn on/off punches (master), minimum duration, allow mark for review, allow hour deletion [before and after clock in] -->
                                    </li>
                                    <li class="nav-item">
                                        <a href="" class="nav-link" data-target="eventm"><i class="fas fa-calendar"></i> Events Management
                                        </a>
                                        <!--Events for selection -->
                                    </li>
                                    <li class="nav-item">
                                        <a href="" class="nav-link" id="hourstats"><i class="fas fa-chart-pie"></i> Hour Statistics
                                        </a>
                                        <!-- Current students clocked out, statistics tables and charts-->
                                    </li>
                                    <li class="nav-item">
                                        <a href="" class="nav-link" id="syslog"><i class="fas fa-cog"></i> System Log
                                        </a>
                                        <!-- Current students clocked out, statistics tables and charts-->
                                    </li>
                                </ul>

                            </div>
                            <div class="col-md-9 ml-sm-auto">
                                Content
                            </div>
                        </div>
                        <!-- Change to session app "type" for dynamic title -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection