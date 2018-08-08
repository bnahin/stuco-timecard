@extends('layouts.app')

{{-- DataTables Styles (CDN) --}}
@push('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.2/b-html5-1.5.2/b-print-1.5.2/kt-2.4.0/r-2.2.2/sc-1.5.0/sl-1.2.6/datatables.min.css"/>
@endpush
{{-- DataTables Scripts (CDN) --}}
@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.2/b-html5-1.5.2/b-print-1.5.2/kt-2.4.0/r-2.2.2/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card" id="hours-card">
                    <div class="card-body">
                        <h3 class="card-title">My Hours</h3>
                        <hr>
                        <!--Up here show stats -->
                        @if (count($hours))
                            <div id="stats" class="row text-center">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3>{{ $averageHours }} hours</h3>
                                            <h5>Average time</h5>
                                            <hr>
                                            <canvas id="line-chart" width="400" height="400"></canvas>
                                            <!-- Average time per week/month - line -->
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3>{{ $numEvents }}</h3>
                                            <h5>Events</h5>
                                            <hr>
                                            <!-- Pie chart, event name -->
                                            <canvas id="pie-chart" width="400" height="400"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3>{{ $totalHours }} hours</h3>
                                            <h5>Total time</h5>
                                            <hr>
                                            <canvas id="mixed-chart" width="400" height="400"></canvas>
                                            <!-- Pie chart: time per event -->
                                            <!-- Line chart: time per month -->
                                            <!--Combine? Area chart, time per event (stacked line) per month -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <table class="table table-hover" id="hours-table">
                            <thead class="thead-dark">
                            <tr>
                                <th style="display:none;" class="print-hide">Timestamp</th>
                                <th>Date</th>
                                <th>Event</th>
                                <th>Clocked In</th>
                                <th>Clocked Out</th> <!--(ND) if next day)-->
                                <th>Total Time</th>
                                <th class="print-hide">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($hours))
                                @foreach($hours as $hour)
                                    @php
                                        $inProgress = !$hour->end_time
                                    @endphp
                                    <!-- If marked, warning (yellow) background -->
                                    <tr @if($inProgress) class="table-info" @endif>
                                        <td style="display:none;" class="print-hide">{{ $hour->start_time->timestamp }}</td>
                                        <td>{{ $hour->start_time->toDateString() }}</td>
                                        <td>{{ $hour->getEventName() }}</td>
                                        <td>{{ $hour->start_time->format('g:i A') }}</td>
                                        <td>{!! (!$inProgress) ? $hour->end_time->format('g:i A') : '<em>None</em>' !!}</td>
                                        <td>{!! (!$inProgress) ? $hour->getTimeDiff() : '<em>In Progress</em>'!!}</td>
                                        <td class="print-hide">
                                            <button class="btn btn-outline-info" data-id="{{ $hour->id }}"
                                                    @if($inProgress) disabled @endif><i
                                                    class="fas fa-flag"></i> Mark for Review
                                            </button>
                                        </td>
                                        <!--Admins have all actions! Make this page dynamic btwn users and admins-->
                                    </tr>
                                @endforeach
                            @else
                                <tr class="warning">
                                    <td colspan="6" style="text-align:center" id="no-hours" class="table-danger">
                                        <strong><i class="fas fa-exclamation-triangle"></i> No
                                            hours to
                                            display! :(
                                        </strong>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection