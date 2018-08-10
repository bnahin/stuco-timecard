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
@endpush

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
                                    <li class="nav-item">
                                        <a href="" class="nav-link active" data-target="studentm"><i
                                                class="fas fa-user"></i>
                                            Student Management </a>
                                        <!-- Class import, student search & add, view hours -->
                                        <!--<p class="text-muted">Test help text.</p>
                                    --></li>
                                    <li class="nav-item">
                                        <a href="" class="nav-link" data-target="tconfig"><i class="fas fa-clock"></i>
                                            View Hours</a>
                                        <!--Turn on/off punches (master), minimum duration, allow mark for review, allow hour deletion [before and after clock in] -->
                                    </li>

                                    <li class="nav-item">
                                        <a href="" class="nav-link" data-target="eventm"><i class="fas fa-calendar"></i>
                                            Events Management
                                        </a>
                                        <!--Events for selection -->
                                    </li>
                                    <li class="nav-item">
                                        <a href="" class="nav-link" id="hourstats"><i class="fas fa-chart-pie"></i> Hour
                                            Statistics
                                        </a>
                                        <!-- Current students clocked out, statistics tables and charts-->
                                    </li>
                                    <li class="nav-item">
                                        <a href="" class="nav-link" data-target="tconfig"><i class="fas fa-cogs"></i>
                                            Timecard Configuration</a>
                                        <!--Turn on/off punches (master), minimum duration, allow mark for review, allow hour deletion [before and after clock in] -->
                                    </li>
                                    <li class="nav-item">
                                        <a href="" class="nav-link" id="syslog"><i class="fas fa-cog"></i> System Log
                                        </a>
                                        <!-- Current students clocked out, statistics tables and charts-->
                                    </li>
                                </ul>

                            </div>
                            <div class="col-md-9 ml-sm-auto">
                                <div id="studentm">
                                    <h5>Student Management</h5>
                                    <hr>
                                    <div class="m-auto col-md-8">
                                        <div class="card border-success mb-3">
                                            <div class="card-header">Student Import</div>
                                            <div class="card-body">
                                                <h5 class="card-title">Upload student data</h5>
                                                <p class="card-text">To replace the enrolled student database, export
                                                    the
                                                    data from Aeries using the command <code>ID FN LN STUEMAIL GR</code>.
                                                    Required fields are Student ID, first name, last name, email, and
                                                    grade.
                                                </p>
                                                <input id="input-import" name="import-file" type="file" class="file"
                                                       data-show-preview="false" data-show-cancel="false"
                                                       data-theme="fa">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <h4>Assign Students</h4>
                                    <table class="table" id="assign-table" data-action="{{ route('get-students') }}">
                                        <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">First Name</th>
                                            <th scope="col">Last Name</th>
                                            <th scope="col">Grade</th>
                                            <th scope="col">Email</th>
                                        </tr>
                                        </thead>
                                    </table>

                                </div>

                                <!--......-->
                                <div id="hourstats" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card text-white bg-primary mb-4">
                                                <div class="card-body">
                                                    <h5 class="card-title"><strong><strong>8</strong> Registered
                                                            Students</strong></h5>
                                                    <p class="card-text">Students that have logged in using ECRCHS
                                                        SSO.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card text-white bg-success mb-4">
                                                <div class="card-body">
                                                    <h5 class="card-title"><strong><strong>40</strong> Assigned
                                                            Students</strong></h5>
                                                    <p class="card-text">Students that have been assigned to Student
                                                        Council.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card text-white bg-info mb-4">
                                                <div class="card-body">
                                                    <h5 class="card-title"><strong><strong>5, 361</strong> Total
                                                            Students</strong></h5>
                                                    <p class="card-text">Students that are enrolled at ECRCHS and were
                                                        in the mass import.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </div>
                        <!-- Change to session app "type" for dynamic title -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection