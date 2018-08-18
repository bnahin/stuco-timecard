@section('page-title')
    Clock Out
@endsection

<h3 class="card-title">Add New Activity</h3>
<hr>
{{-- When entering ID,
 if already clocked out, disable all fields, change border color, add elapsed time and clock in button--}}
<form id="new-activity" action="{{ route('clock-out') }}">
    @csrf
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="student-id">Student ID</label>
            <input type="text" class="form-control" id="student-id" placeholder="ex. 115602"
                {{ Auth::guard('user')->check() ? "value=". Auth::user()->student_id." disabled": '' }}>
        </div>
        <div class="form-group col-md-9" {{ isAdmin() ? 'style=display:none;' : '' }}>
            <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    <h5 id="student-info-name">
                        @auth('user'){{ Auth::user()->full_name }} @endauth</h5>
                    <h6 id="student-info-grade">
                        @auth('user') Grade {{ Auth::user()->student->grade }} @endauth</h6></div>
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