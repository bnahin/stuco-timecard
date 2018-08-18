@extends('layouts.app')

@push('styles')
    <!-- FileInput -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.9/css/fileinput.min.css" media="all"
          rel="stylesheet" type="text/css"/>

    <!-- DataTables -->
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/dt-1.10.18/kt-2.4.0/r-2.2.2/sc-1.5.0/datatables.min.css"/>

@endpush
@push('scripts')
    <!-- FileInput -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.9/js/fileinput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.9/themes/fa/theme.min.js"></script>

    <!--DataTables-->
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/dt-1.10.18/kt-2.4.0/r-2.2.2/sc-1.5.0/datatables.min.js"></script>

    <!--jQuery Validate
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js"></script>
    -->
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2>Club Administration <code>{{ $clubCode }}</code></h2>
                <div class="card" id="admin-card">
                    <div class="card-body">
                        <div class="container row">
                            <div class="col-md-3" id="adm-nav-col">
                                <ul class="nav flex-column" id="admin-nav">
                                    <li class="nav-item nav-header">
                                        USER MANAGEMENT
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin', ['page' => 'assign']) }}"
                                           class="nav-link @if($page == "assign") active @endif"
                                           data-target="assign"><i class="fas fa-user"></i>
                                            Student Management </a>
                                        <!-- Class import, student search & add, view hours -->
                                        <!--<p class="text-muted">Test help text.</p>
                                    --></li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin', ['page' => 'blocked']) }}"
                                           class="nav-link @if($page == "blocked") active @endif"
                                           data-target="blocked"><i class="fas fa-minus-circle"></i>
                                            Blocked Students </a>
                                        <!-- Class import, student search & add, view hours -->
                                        <!--<p class="text-muted">Test help text.</p>
                                    --></li>
                                    <li class="nav-item no-bottom">
                                        <a href="{{ route('admin', ['page' => 'enrolled']) }}"
                                           class="nav-link @if($page == "enrolled") active @endif"
                                           data-target="enrolled"><i class="fas fa-users"></i>
                                            Enrolled Students
                                        </a>
                                        <!-- Current students clocked out, statistics tables and charts-->
                                    </li>
                                    <li class="nav-item nav-header">
                                        Student Hours
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin', ['page' => 'marked']) }}"
                                           class="nav-link @if($page == "marked") active @endif"
                                           data-target="marked"><i class="fas fa-clock"></i>
                                            View Marked Hours</a>
                                        <!--Turn on/off punches (master), minimum duration, allow mark for review, allow hour deletion [before and after clock in] -->
                                    </li>
                                    <li class="nav-item no-bottom">
                                        <a href="{{ route('admin', ['page' => 'hourstats']) }}"
                                           class="nav-link @if($page == "hourstats") active @endif"
                                           data-target="hourstats"><i class="fas fa-chart-pie"></i>
                                            Hour Statistics
                                        </a>
                                        <!-- Current students clocked out, statistics tables and charts-->
                                    </li>
                                    <li class="nav-item nav-header">
                                        Configuration
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin', ['page' => 'events']) }}"
                                           class="nav-link @if($page == "events") active @endif"
                                           data-target="events">
                                            <i class="fas fa-calendar"></i>
                                            Events Management
                                        </a>
                                        <!--Events for selection -->
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin', ['page' => 'timecard']) }}"
                                           class="nav-link @if($page == "timecard") active @endif"
                                           data-target="timecard"><i class="fas fa-cogs"></i>
                                            Timecard Configuration</a>
                                        <!--Turn on/off punches (master), minimum duration, allow mark for review, allow hour deletion [before and after clock in] -->
                                    </li>
                                    <li class="nav-item no-bottom">
                                        <a href="{{ route('admin', ['page' => 'system']) }}"
                                           class="nav-link @if($page == "system") active @endif" data-target="system">
                                            <i class="fas fa-cog"></i> System Log
                                        </a>
                                    </li>
                                </ul>

                            </div>
                            <div class="col-md-9 ml-sm-auto" id="adm-content">
                                @include('pages.admin.'.$page)
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Change to session app "type" for dynamic title -->
            </div>
        </div>
    </div>
@endsection