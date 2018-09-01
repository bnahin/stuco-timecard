@extends('layouts.app')

@section('page-title')
    {{ !isAdmin() ? "My": "View" }} Hours
@endsection


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

@push('scripts')
    <script src="{{ asset('datetimepicker/moment.min.js') }}"></script>
    <script src="{{ asset('datetimepicker/datetimepicker.js') }}"></script>
    <script src="{{ asset('datetimepicker/timepicker.min.js') }}"></script>
@endpush
@push('styles')
    <link rel="stylesheet" href="{{ asset('datetimepicker/datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datetimepicker/timepicker.min.css') }}">
@endpush

@section('content')

    <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Timepunch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-hour-form">
                        <input type="hidden" name="id" id="input-id">
                        <div class="form-group row">
                            <label for="event" class="col-sm-2 col-form-label">Event</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="event" name="event">
                                    @if(count($events))
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}">{{ $event->event_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="date" class="col-sm-2 col-form-label">Date</label>
                            <div class="col-sm-4">
                                <input id="date" name="date" type="text" class="form-control">
                            </div>
                        </div>
                        <fieldset class="form-inline">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Start & End Time</label>
                                <div class="col-sm-10" id="start-end-time-col">
                                    <div class="input-group clockpicker" data-placement="right" data-align="top"
                                         data-autoclose="true">
                                        <input type="text" class="form-control" id="start-time" name="start_time">
                                    </div>
                                    <span class="fas fa-minus"></span>
                                    <div class="input-group clockpicker" data-placement="right" data-align="top"
                                         data-autoclose="true">
                                        <input type="text" class="form-control" id="end-time" name="end_time">
                                    </div>

                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                    <button type="button" class="btn btn-danger action-btn remove-timepunch" data-id="0"><i
                            class="fas fa-times"></i> Remove
                        Timepunch
                    </button>
                    <button type="button" class="btn btn-success action-btn" data-id="0" id="save-timepunch"><i class="fas fa-check"></i> Save
                        changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Export Data -->
    <input type="hidden" id="export-clubname"
           value="{{ config('app.school-name')." ". ($clubName ?: 'Club Management') }}">
    <input type="hidden" id="export-header" value="@admin Hours for {{ $fullName }} @else My Hours @endadmin">
    <input type="hidden" id="export-name" value="{{ $fullName }}">
    <input type="hidden" id="export-stuid" value="{{ $studentId }}">
    <input type="hidden" id="export-grade" value="{{ $grade }}">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card" id="hours-card">
                    <div class="card-body">
                        <h3 class="card-title">@admin Hours for {{ $fullName }} @else My Hours @endadmin</h3>
                        <hr>
                        <!--Up here show stats -->
                        @if (count($hours))
                            <div id="stats" class="row text-center">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3>{{ $averageHours }} hours</h3>
                                            <h5>Average duration</h5>
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
                        <input type="hidden" id="user_id" value="{{ $uid }}">
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
                                    <tr class="@if($inProgress) table-info @elseif($hour->needs_review) table-warning @endif">
                                        <td style="display:none;"
                                            class="print-hide">{{ $hour->start_time->timestamp }}</td>
                                        <td>{{ $hour->start_time->toDateString() }}</td>
                                        <td>{{ $hour->getEventName() }}</td>
                                        <td>{{ $hour->start_time->format('g:i A') }}</td>
                                        <td>{!! (!$inProgress) ? $hour->end_time->format('g:i A') : '<em>None</em>' !!}</td>
                                        <td>{!! (!$inProgress) ? $hour->getTimeDiff() : '<em>In Progress</em>'!!}</td>
                                        <td class="print-hide">
                                            @if(isAdmin())
                                                <div class="btn-group">
                                                    <button class="btn btn-warning hour-edit" data-id="{{ $hour->id }}" rel="tooltip" title="Edit Timepunch"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-danger remove-timepunch" data-id="{{ $hour->id }}" rel="tooltip" title="Remove Timepunch"><i class="fas fa-times"></i></button>
                                                </div>
                                            @else
                                                @if(!$hour->needs_review)
                                                    <button class="btn btn-outline-info mark-hour"
                                                            data-id="{{ $hour->id }}"
                                                            @if($inProgress) disabled @endif><i
                                                            class="fas fa-flag"></i> Mark for Review
                                                    </button>
                                                @else
                                                    <button class="btn btn-warning undo-mark" data-id="{{ $hour->id }}"><i class="fas fa-undo"></i> Undo
                                                        Mark for Review
                                                    </button>
                                                @endif
                                            @endif
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