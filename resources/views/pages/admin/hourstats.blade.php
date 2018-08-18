@section('page-title')
    Hour Statistics
@endsection

<div id="hourstats">
    <h5>Hour Statistics</h5>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <strong><strong>{{ $data['members'] }}</strong>
                            Club Members</strong></h5>
                    <p class="card-text">Students that have been assigned to the club or joined with the code.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-4">
                <div class="card-body">
                    <h5 class="card-title"><strong><strong>3</strong> On Assignment</strong></h5>
                    <p class="card-text">Students that are currently clocked out.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-4">
                <div class="card-body">
                    <h5 class="card-title"><strong><strong>5, 361</strong> Total
                            Students</strong></h5>
                    <p class="card-text">Students that are enrolled at ECRCHS and were
                        in the mass import.</p>
                </div>
            </div>
        </div>
    </div>
    <hr>
</div>