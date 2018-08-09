{{-- Purple Border --}}

<h3 class="card-title">Current Event</h3>
<hr>
<table class="table table-bordered table-striped" id="clock-in-table">
    <tr>
        <td colspan="3"><strong>Event: </strong> {{ $data->getEventName() }}</td>
    </tr>
    <tr>
        <td style="width:25%;"><strong>Clocked out: </strong> {{ $data->start_time->format('g:i A') }}</td>
        <td>{{ "{$data->getFullName()} {$data->student_id}" }}</td>
        <td>Event <strong>{{ $eventCount }} {{-- Number of events for student --}} </strong></td>
    </tr>
    <tr>
        <td colspan="3"><strong>Elapsed Time: </strong> <span
                id="elapsed"><strong>4</strong> hours <strong>5</strong> minutes <strong>45</strong> seconds</span>
        </td>
    </tr>
</table>
<div class="form-group">
    <label for="comments">Comments</label>
    <textarea id="comments" class="form-control">{{ $data->comments }}</textarea>
</div>
<form id="clock-in-form" method="post" action="{{ route('clock-in', ['hour' => $data->id]) }}">
    @csrf
    <input type="hidden" id="hour-id" value="{{ $data->id }}">
    <div class="form-group">
        <div class="btn-group" id="clock-in-btns">
            <button class="btn btn-success clock-in" id="ci-main" type="button" data-return="/">
                <i class="fas fa-sign-in-alt"></i> Clock In
            </button>
            <button type="button" id="ci-addon" class="btn btn-success dropdown-toggle dropdown-toggle-split"
                    data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item clock-in" href="#" data-return="{{ route('my-hours') }}"><i
                        class="fas fa-eye"></i> Clock In and View Hours</a>
                <a class="dropdown-item clock-in" href="#"
                   data-return="{{ route('hour-mark', ['hour' => $data->id]) }}"><i class="fas fa-flag"></i> Clock In
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
