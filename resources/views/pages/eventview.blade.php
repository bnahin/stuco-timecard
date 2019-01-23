@extends('layouts.app')

@section('page-title')
    Event Attendance
@endsection
@push('styles')
    {{-- DataTables Styles (CDN) --}}
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.2/b-html5-1.5.2/b-print-1.5.2/kt-2.4.0/r-2.2.2/sc-1.5.0/sl-1.2.6/datatables.min.css"/>

    {{-- Timepicker --}}
    <link rel="stylesheet" href="{{ asset('datetimepicker/datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datetimepicker/timepicker.min.css') }}">
@endpush

@push('scripts')
    {{-- DataTables Scripts (CDN) --}}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.2/b-colvis-1.5.2/b-html5-1.5.2/b-print-1.5.2/kt-2.4.0/r-2.2.2/sc-1.5.0/sl-1.2.6/datatables.min.js"></script>

    {{-- Timepicker --}}
    <script src="{{ asset('datetimepicker/moment.min.js') }}"></script>
    <script src="{{ asset('datetimepicker/datetimepicker.js') }}"></script>
    <script src="{{ asset('datetimepicker/timepicker.min.js') }}"></script>
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
                    <button type="button" class="btn btn-success action-btn" data-id="0" id="save-timepunch"><i
                            class="fas fa-check"></i> Save
                        changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card" id="hours-card">
                    <div class="card-body">
                        <h3 class="card-title">Event Attendance - <i>{{ $event->event_name }}</i></h3>
                        <hr>
                        <table class="table table-hover" id="hours-table">
                            <thead class="thead-dark">
                            <tr>
                                <th style="display:none;" class="print-hide">Timestamp</th>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Clocked In</th>
                                <th>Clocked Out</th> <!--(ND) if next day)-->
                                <th>Total Time</th>
                                <th class="print-hide">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($event->hours))
                                @foreach($event->hours as $hour)
                                    @php
                                        $inProgress = !$hour->end_time
                                    @endphp
                                    <!-- If marked, warning (yellow) background -->
                                    <tr class="@if($inProgress) table-info @elseif($hour->needs_review) table-warning @endif">
                                        <td style="display:none;"
                                            class="print-hide">{{ $hour->start_time->timestamp }}</td>
                                        <td>{{ $hour->start_time->format('m/d') }}</td>
                                        <td>{{ $hour->user->last_name . ", " . $hour->user->first_name }}</td>
                                        <td>{{ $hour->start_time->format('g:i A') }}</td>
                                        <td>{!! (!$inProgress) ? $hour->end_time->format('g:i A') : '<em>None</em>' !!}</td>
                                        <td>{!! (!$inProgress) ? $hour->getTimeDiff() : '<em>In Progress</em>'!!}</td>
                                        <td class="print-hide">
                                            <div class="btn-group">
                                                <button class="btn btn-warning hour-edit" data-id="{{ $hour->id }}"
                                                        rel="tooltip" title="Edit Timepunch"><i
                                                        class="fas fa-edit"></i></button>
                                                <button class="btn btn-danger remove-timepunch"
                                                        data-id="{{ $hour->id }}" rel="tooltip"
                                                        title="Remove Timepunch"><i class="fas fa-times"></i>
                                                </button>
                                            </div>
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