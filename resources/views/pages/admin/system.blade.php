<h5>System Log</h5>
<hr>
<table class="table table-hover" id="syslog-table">
    <thead class="thead-dark">
    <tr>
        <th>Date & Time</th>
        <th>User</th>
        <th>Message</th>
    </tr>
    </thead>
    <tbody>
    @if(count($data))
        @foreach($data as $log)
            @php
                $hasUser = $log->user || $log->admin;
                $auth = ($log->user) ? 'user' : 'admin';

                $bg = '';
            @endphp

            @if($auth == 'admin')
                @php $bg = 'by-admin' @endphp
            @else
                @if (str_contains($log->message, 'Clocked in'))
                    @php $bg = 'clockin' @endphp
                @elseif(str_contains($log->message, 'Clocked out'))
                    @php $bg = 'clockout' @endphp
                @endif
            @endif
            <tr class="@if(strlen($bg)) bg-{{ $bg }} @endif">
                <td>{{ $log->created_at->format('m/d/Y h:i a') }}</td>
                <td>@if($hasUser) {{ $log->$auth->full_name }} @else <em>Removed</em> @endif</td>
                <td>{{ $log->message }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>