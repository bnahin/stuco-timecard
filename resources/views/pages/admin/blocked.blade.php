@section('page-title')
    Blocked Students
@endsection


<div id="blocks">
    <h5>Blocked Students</h5>
    <hr>
    <!--TODO: Blocked Students w/ action to unblock-->
    <div class="card" id="blocked-info">
        <div class="card-body">
            These students have been blocked from your club. They cannot join, view their hours, or initiate a time
            punch. Dropped students are added here automatically.
        </div>
    </div>
    <table class="table" id="blocked-table">
        <thead>
        <tr class="thead-dark">
            <th>Student ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Blocked On</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @if(count($data))
            @foreach($data as $blocked)
                <tr>
                    <td>
                        {{ $blocked->user->student->student_id }}
                    </td>
                    <td>{{ $blocked->user->last_name }}</td>
                    <td>{{ $blocked->user->first_name }}</td>
                    <td>{{ $blocked->created_at->format('m-d-Y H:i') }}</td>
                    <td>
                        <button class="btn btn-outline-info unblock"
                                data-id="{{ $blocked->id }}">
                            <i class="fas fa-undo"></i> Unblock
                        </button>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>

</div>