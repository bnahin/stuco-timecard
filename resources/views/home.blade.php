@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" id="new-activity-card">
                    <div class="card-body">
                        <h3 class="card-title">Add New Activity</h3>
                        <hr>
                        <form id="new-activity" action="{{ url('/hours/new') }}">
                            @csrf
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label for="student-id">Student ID</label>
                                    <input type="text" class="form-control" id="student-id" placeholder="ex. 115602"
                                        {{ Auth::check() ? "value=". Auth::user()->student_id." disabled": '' }}>
                                </div>
                                <div class="form-group col-md-9" {{ !Auth::check() ? 'style="display:none"' : '' }}>
                                    <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
                                        <div class="card-header"><h5>Blake Nahin</h5><h6>Grade 12</h6></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="event-name">Event Name</label>
                                <select id="event-name" class="form-control">
                                    <option value="0">Out of Classroom</option>
                                    @foreach ($events as $event)
                                        <option value="{{ $event->id }}">{{ $event->event_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="comments">Comments</label>
                                <textarea id="comments" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <p><strong>Current Time: </strong> <span id="current-time"></span></p>
                            </div>
                            <button type="submit" class="btn btn-info" id="new-activity-submit"><i
                                    class="fas fa-sign-out-alt"></i> Clock Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
