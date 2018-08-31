@push('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
@endpush
@push('styles')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endpush

<div id="club-manage">
    <h5>Club Configuration</h5>
    <hr>
    <div class="alert alert-warning"><strong><i class="fas fa-info-circle"></i> Currently in development!</strong></div>
    <form>
        <div class="form-group row">
            <label for="staticName" class="col-sm-2 col-form-label">Club Name</label>
            <div class="col-sm-10">
                <input type="text" readonly class="form-control-plaintext" id="staticName" value="Student Council">
                <p class="text-muted">See the Director of Clubs to request a name change.</p>
            </div>
        </div>
        <div class="form-group">
            <label for="clubDesc" class="col-form-label">Club Description</label>
            <textarea class="form-control" rows="2" id="clubDesc">{!! $settings->club_desc !!}</textarea>
            <p class="text-muted">This is shown on the Club Select page.</p>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="master" class="col-form-label"><strong>Timepunches</strong></label>
                    <input id="master" class="checkbox" type="checkbox" @if($settings->master) checked @endif
                    data-on="<i class='fas fa-play'></i> Allowed"
                           data-off="<i class='fas fa-stop'></i> Disabled" data-onstyle="success"
                           data-offstyle="danger">
                    <p class="text-muted">This will disable clocking in and out for students.</p>

                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="aDeletion" class="col-form-label">Timepunch Deletion</label>
                    <input id="aDeletion" class="checkbox" type="checkbox" @if($settings->allow_delete) checked @endif
                    data-on="Allowed"
                           data-off="Disabled" data-onstyle="success"
                           data-offstyle="danger">
                    <p class="text-muted">This will allow students to delete their own timepunches once complete
                        (clocked out).</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="aMark" class="col-form-label">Mark for Review</label>
                    <input id="aMark" class="checkbox" type="checkbox" @if($settings->allow_mark) checked @endif
                    data-on="Allowed"
                           data-off="Disabled"
                           data-onstyle="success"
                           data-offstyle="danger">
                    <p class="text-muted">This will allow students to mark punches for review.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="aComments" class="col-form-label">Comments</label>
                    <input id="aComments" class="checkbox" type="checkbox" @if($settings->allow_comments) checked @endif
                    data-on="Allowed"
                           data-off="Disabled"
                           data-onstyle="success"
                           data-offstyle="danger">
                    <p class="text-muted">This will allow students to put comments on their timepunches.</p>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-block btn-success" id="save-club" data-action="/admin/club/update"><i
                            class="fas fa-check"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>