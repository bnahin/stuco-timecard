{{-- Purple Border --}}
@section('page-title')
    Clock Out
@endsection

<h3 class="card-title">Current Event</h3>
<hr>
<table class="table table-bordered table-striped" id="clock-out-table">
    <tr>
        <td colspan="3"><strong>Event: </strong> {{ $data->getEventName() }}</td>
    </tr>
    <tr>
        <td style="width:25%;"><strong>Clocked out: </strong> {{ $data->start_time->format('g:i A') }}</td>
        <td>{{ "{$data->getFullName()} {$data->student_id}" }}</td>
        <td>Event <strong>{{ $eventCount }} {{-- Number of events for student --}} </strong></td>
    </tr>
    <tr>
        <script>
          window.start_time = "{{ $data->start_time }}"
        </script>
        <td colspan="3"><strong>Elapsed Time: </strong>
            <span id="elapsed">
                <span id="hours" style="display:none;"><strong id="ehours">0</strong> hours </span>
                <span id="minutes" style="display:none;"><strong id="eminutes">0</strong> minutes </span>
                <span id="seconds"><strong id="esecs">0</strong> seconds</span>
            </span>
        </td>
    </tr>
</table>
<div class="form-group">
    <label for="comments">Comments</label>
    <textarea id="comments" class="form-control">{{ $data->comments }}</textarea>
</div>
<form id="clock-out-form" method="post" action="{{ route('clock-out', ['hour' => $data->id]) }}">
    @csrf
    <input type="hidden" id="hour-id" value="{{ $data->id }}">
    <div class="form-group">
        <div class="btn-group" id="clock-out-btns">
            <button class="btn btn-success clock-out" id="co-main" type="button" data-return="/">
                <i class="fas fa-sign-in-alt"></i> Clock Out
            </button>
            <button type="button" id="co-addon" class="btn btn-success dropdown-toggle dropdown-toggle-split"
                    data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item clock-out" href="#" data-return="{{ route('my-hours') }}"><i
                        class="fas fa-eye"></i> Clock Out and View Hours</a>
                <a class="dropdown-item clock-out mark-review" href="#"
                   data-return="{{ route('my-hours') }}"><i class="fas fa-flag"></i> Clock Out
                    and Mark for
                    Review</a>
            </div>
        </div>
        <button class="btn btn-danger" id="clock-remove" data-action="{{ route('delete-hour',['hour' => $data->id]) }}">
            <i
                class="fas fa-times"></i> Remove Time Punch
        </button>
    </div>
</form>
