<html>
<table>
    <tbody>
    <thead>
    <tr>
        <th>Date</th>
        <th>Event</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Duration</th>
    </tr>
    </thead>
    <tbody>
    @if(count($hours))
        @foreach($hours as $hour)
            @if(!$hour->end_time) @continue @endif
            <!-- If marked, warning (yellow) background -->
            <tr>
                <td>{{ $hour->start_time->toDateString() }}</td>
                <td>{!! ($hour->event) ? $hour->event->event_name : "<em>Unknown</em>" !!}</td>
                <td>{{ $hour->start_time->format('g:i A') }}</td>
                <td>{{ $hour->end_time->format('g:i A') }}</td>
                <td>{{  $hour->getTimeDiff() }}</td>
            </tr>
        @endforeach
    @else
        <td colspan="5">
            <strong>No Hours Logged</strong>
        </td>
    @endif
    </tbody>
</table>
</html>