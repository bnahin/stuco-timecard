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
                    <h5 class="card-title"><strong><strong>{{ $data['numClocked'] }}</strong> On Assignment</strong>
                    </h5>
                    <p class="card-text">Students that are currently clocked out.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-4">
                <div class="card-body">
                    <h5 class="card-title"><strong><strong>{{ number_format($data['numEnrolled']) }}</strong> Total
                            Students</strong></h5>
                    <p class="card-text">Students that are enrolled at ECRCHS and were
                        in the mass import.</p>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="m-auto col-md-6">
        <div class="card border-success mb-3">
            <div class="card-header">Hour Export</div>
            <div class="card-body">
                <h5 class="card-title">Export student hours</h5>
                <p class="card-text">This will export all of your club's students' hours to Excel sheets compressed in a
                    zip file.
                </p>
                <button class="btn btn-outline-success btn-block archive-club"
                        data-id="{{ getClubId() }}"
                        data-prev-text="Export Club Hours"><i class="fas fa-archive"></i> Export Club Hours
                </button>
            </div>
        </div>
    </div>
    @if (count(\App\Hour::all()))
        <div id="stats" class="text-center">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ $data['avgHours'] }} hours</h3>
                            <h5>Average duration</h5>
                            <hr>
                            <canvas id="line-chart" width="400" height="400"></canvas>
                            <!-- Average time per week/month - line -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ number_format($data['numEvents']) }}</h3>
                            <h5>Events</h5>
                            <hr>
                            <!-- Pie chart, event name -->
                            <canvas id="pie-chart" width="400" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:6px;">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h3>{{ number_format($data['totalHours']) }} hours</h3>
                            <h5>Total time</h5>
                            <hr>
                            <div class="chart-container" style="position:relative; height:380px;">
                                <canvas id="mixed-chart"></canvas>
                            </div>
                            <!-- Pie chart: time per event -->
                            <!-- Line chart: time per month -->
                            <!--Combine? Area chart, time per event (stacked line) per month -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>