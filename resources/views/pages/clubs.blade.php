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
                        <table class="table">
                            <thead class="thead-dark">
                            <tr>
                                <th width="45%">Club Name</th>
                                <th>Joined Date</th>
                                <th>Actions</th>
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
                                    <td>
                                        @if(!isAdmin())
                                            <button class="btn btn-danger leave-club" rel="tooltip"
                                                    title="Leave Club and Delete Hours" data-id="{{ $club->id }}"><i
                                                    class="fas fa-sign-out-alt"></i> Purge and Leave
                                            </button>
                                        @else
                                        <!--TODO move this to club management -->
                                            <button class="btn btn-success archive-club" rel="tooltip"
                                                    title="Archive Hours"
                                                    data-id="{{ $club->id }}"><i class="fas fa-archive"></i> Archive
                                            </button>
                                        @endif
                                    </td>
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
