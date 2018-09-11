@extends('layouts.app')

@section('page-title')
    Announcements
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <h3 class="card-title">Club Announcements</h3>
            @admin
            <!--TinyMCE table here-->
                @endadmin
                <div id="announcement-content">
                    @if(count($announcements))
                        @foreach($announcements as $post)
                            <div class="card mb-3 @if($post->is_global) bg-dev @endif">
                                <div class="card-body">
                                    <div class="post-actions">
                                        <button class="btn btn-warning edit-post"><i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button class="btn btn-danger delete-post"><i class="fas fa-times"></i></button>
                                    </div>
                                    <h4 class="card-title">{{ $post->post_title }}</h4>
                                    <h5 class="card-subtitle mb-2 text-muted">{{ $post->admin->full_name }} @if($post->is_global)
                                            <em>(Developer)</em> @endif</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">{{ $post->created_at->format('m/d/Y g:i A') }}</h6>
                                    <hr>
                                    <p class="card-text">{!! $post->post_body !!}
                                    @if($post->created_at !== $post->updated_at)
                                        <hr><h6
                                            class="text-muted">
                                            Updated {{ $post->updated_at->format('m/d/Y g:i A') }}</h6>@endif</p>
                                </div>
                            </div>
                        @endforeach
                        {{ $announcements->links() }}
                    @else
                        <div class="alert alert-info"><i class="fas fa-info-circle"></i> No Announcements to show</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection