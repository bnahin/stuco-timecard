@extends('layouts.app')

@section('page-title')
    My Clubs
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card" id="myclubs">
                    <div class="card-body">
                        <h3 class="card-title">My Clubs - @admin Admin @else Student @endadmin</h3>
                        <hr>
                        <div class="alert alert-info"><span class="fas fa-info-circle"></span> Action buttons are
                            currently in development.
                        </div>
                        <table class="table">
                            <thead class="thead-dark">
                            <tr>
                                <th width="45%">Club Name</th>
                                <th>Joined Date</th>
                                @unless(isAdmin())
                                    <th>Actions</th>
                                @endunless
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clubs as $club)
                                <tr>
                                    <td>
                                        <strong>{{ $club->club_name }}</strong><br><span
                                            class="text-muted">{{ $club->settings->club_desc }}</span></td>
                                    <td>
                                        @php $month = $club->pivot->created_at->month; @endphp
                                        @if($month >= 8 && $month <= 12)
                                            First Semester
                                        @elseif ($month >= 1 && $month <= 6)
                                            Second Semester
                                        @else
                                            Summer
                                        @endif
                                        <br>
                                        {{ $club->pivot->created_at->format('m/d/Y') }}</td>
                                    @unless(isAdmin())
                                        <td>
                                            <button class="btn btn-danger leave-club" rel="tooltip"
                                                    title="Leave Club and Delete Hours" data-id="{{ $club->id }}"><i
                                                    class="fas fa-sign-out-alt"></i> Leave
                                            </button>
                                            <button class="btn btn-success archive-mine" rel="tooltip"
                                                    title="Archive Hours"
                                                    onclick="swal('In Development', 'This feature is currently in development.', 'info');"
                                                    data-id="{{ $club->id }}"><i class="fas fa-archive"></i> Archive
                                            </button>
                                        </td>
                                    @endunless
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $clubs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection