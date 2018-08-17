<div id="assign">
    <h5>Student Management</h5>
    <hr>
    <h4>Assign Students</h4>
    <p class="text-muted">Students are assigned to your club when they enter your club's
        access code, <code>BANBAN</code>. You can also manually add them here.</p>
    <div class="row justify-content-center">
        <div class="col-xs-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Add by student ID or full name</h5>
                    <p class="card-text text-muted">Use the <a href="#">Enrolled
                            Students</a> page
                        to search for an ID or name.</p>
                    <form id="manual-assign-form" method="post"
                          action="{{ route('manual-assign') }}">
                        @csrf
                        <div class="form-group">
                            <input type="text" class="form-control"
                                   id="assign-input"
                                   placeholder="ex. 115602 or Blake Nahin">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success" id="manual-assign"><i
                                    class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <table class="table table-hover" id="assigned-table">
        <thead class="thead-dark">
        <tr>
            <th>Student ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Grade Level</th>
            <th>Email</th> <!--(ND) if next day)-->
            <th class="print-hide">Actions</th>
        </tr>
        </thead>
        <tbody>
        <!--Students that are a part of the admin's (current user) club -->
        @if($data)
            @foreach($data as $student)
                <tr>
                    <td>{{ $student->student->student_id }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->student->grade }}</td>
                    <td>{{ $student->email }}</td>
                    <td>[Actions]</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>