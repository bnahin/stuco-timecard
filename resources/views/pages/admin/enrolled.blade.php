@section('page-title')
    Enrolled Students
@endsection


<div id="enrolled">
    <h5>Enrolled Student Database</h5>
    <hr>
    <!--
    <div class="m-auto col-md-8">
        <div class="card border-success mb-3">
            <div class="card-header">Student Import</div>
            <div class="card-body">
                <h5 class="card-title">Upload student data</h5>
                <p class="card-text">To replace the enrolled student database, export
                    the
                    data from Aeries using the command <code>ID FN LN STUEMAIL GR</code>.
                    Required fields are Student ID, first name, last name, email, and
                    grade.
                </p>
                <input id="input-import" name="import-file" type="file" class="file"
                       data-show-preview="false" data-show-cancel="false"
                       data-theme="fa">
            </div>
        </div>
    </div>
    -->
    <hr>
    <table class="table" id="student-db" data-action="{{ route('get-enrolled') }}">
        <thead class="thead-dark">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Grade</th>
            <th scope="col">Email</th>
        </tr>
        </thead>
    </table>
</div>