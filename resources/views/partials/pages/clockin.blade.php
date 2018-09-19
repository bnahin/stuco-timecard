@section('page-title')
    Clock Out
@endsection

<h3 class="card-title" id="clock-in-title">Add New Activity</h3>
<h6 class="card-title">{{ $clubName }}</h6>
<hr>
{{-- When entering ID,
 if already clocked out, disable all fields, change border color, add elapsed time and clock in button--}}
<form id="new-activity" action="{{ route('clock-in') }}">
    @csrf
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="student-id">Student ID</label>
            <input type="text" class="form-control" id="student-id" placeholder="ex. 115602"
                   @if(Auth::guard('user')->check()) value="{{Auth::user()->student->student_id }}" disabled @endif>
        </div>
        <span id="loading-student" style="display:none;"><i class="fas fa-spinner fa-pulse"></i></span>
        <div class="form-group col-md-9" id="student-info" {{ isAdmin() ? 'style=display:none;' : '' }}>
            <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    <h5 id="student-info-name">
                        @auth('user'){{ Auth::user()->full_name }} @endauth</h5>
                    <h6>
                        Grade <span
                            id="student-info-grade">@auth('user') {{ Auth::user()->student->grade }} @endauth</span>
                    </h6></div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="event-name">Event Name</label>
        <select id="event-name" class="form-control">
            @foreach ($events as $event)
                <option value="{{ $event->id }}">{{ $event->event_name }}</option>
            @endforeach
        </select>
    </div>
    @if($settings->allow_comments)
        <div class="form-group">
            <label for="comments">Comments</label>
            <textarea id="comments" class="form-control" rows="2"></textarea>
        </div>
    @endif
    <div class="form-group">
        <p id="current-time-p"><strong>Current Time: </strong> <span id="current-time"></span></p>
        @if(isAdmin())<p id="elapsed-time-p" style="display:none;"><strong>Elasped Time: </strong> <span
                id="hours"></span> hours <span
                id="minutes"></span> minutes <span id="seconds"></span> seconds</p> @endif
    </div>
    @if(!$settings->master)
        <div class="form-group">
            <div class="alert alert-danger col-md-9"><strong><i class="fas fa-exclamation-triangle"></i></strong>
                Timepunches are currently disabled. Contact your club leaders.
            </div>
        </div>
    @elseif(!count($events))
        <div class="form-group">
            <div class="alert alert-danger col-md-5"><strong><i class="fas fa-exclamation-triangle"></i></strong>
                There
                are no
                events published.
            </div>
        </div>
    @endif
    <button type="submit" class="btn btn-primary" id="new-activity-submit" @if(!count($events) || !$settings->master) disabled @endif><i
            class="fas fa-sign-in-alt"></i> Clock In
    </button>
    @if(isAdmin())
        <button type="submit" data-id="0" class="btn btn-success" style="display:none" id="clock-out-submit"
                @if(!count($events)) disabled @endif><i
                class="fas fa-sign-out-alt"></i> Clock Out
        </button>
    @endif
</form>