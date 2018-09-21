@extends('layouts.app')

@section('page-title')
    Announcements
@endsection

@push('scripts')
    <script
        src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey={{ config('services.tinymce.api_key') }}"></script>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <h3 class="card-title">Club Announcements</h3>

                @if(Session::has('newPost') && Session::get('newPost'))
                    <div class="alert alert-success"><strong><i class="fas fa-check"></i> Success!</strong>
                        The announcement has been posted.
                    </div>
                @endif
                @admin
            <!--TinyMCE table here-->
                <div class="accordion mb-2" id="accordion">
                    <div class="card">
                        <div class="card-header" id="new-post-collapse-header">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#new-post-collapse" aria-expanded="true"
                                        aria-controls="new-post-collapse">
                                    <i class="fas fa-plus"></i> New Announcement
                                </button>
                            </h5>
                        </div>

                        <div id="new-post-collapse" class="collapse @if($errors->any()) show @endif"
                             aria-labelledby="new-post-collapse-header"
                             data-parent="#accordion">
                            @if($errors->any())
                                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <strong>The
                                        following errors occurred:</strong>
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="card-body">
                                <form id="new-announcement-form" method="post"
                                      action="{{ route('create-announcement') }}">
                                    @csrf
                                    <div class="form-group row">
                                        <label for="title" class="col-sm-1 col-form-label">Title</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" id="title" name="title"
                                                   placeholder="ex. Go see ECR Drama's fall play!" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <textarea id="announcement-content" name="message" required></textarea>
                                    </div>
                                    <div class="form-group row justify-content-center">
                                        <div class="col-md-4">
                                            <button class="btn btn-success" id="create-announcement"><i
                                                    class="fas fa-check"></i> Create Announcement
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                @endadmin
                <div id="announcement-content">
                    @if(count($announcements))
                        @foreach($announcements as $post)
                            <div class="card mb-3 @if($post->is_global) bg-dev @endif" id="post-{{ $post->id }}">
                                <div class="card-body">
                                    <div class="post-actions">
                                        <button class="btn btn-warning edit-post" data-id="{{ $post->id }}"
                                                rel="tooltip" title="Edit Post"><i
                                                class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button class="btn btn-success save-edits" style="display:none;"
                                                data-id="{{ $post->id }}" rel="tooltip" title="Save Changes"><i
                                                class="fas fa-check"></i></button>
                                        <button class="btn btn-danger delete-post" data-id="{{ $post->id }}"
                                                rel="tooltip" title="Delete Post"><i
                                                class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <h4 class="card-title"><span class="title-static">{{ $post->post_title }}</span>
                                        @admin<input
                                            id="form-title-{{ $post->id }}" type="text" value="{{ $post->post_title }}"
                                            style="display:none; width:80%;" class="form-control">
                                        @endadmin</h4>
                                    <h5 class="card-subtitle mb-2 text-muted">{{ $post->admin->full_name }} @if($post->is_global)
                                            <em>(Developer)</em> @endif</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">{{ $post->created_at->format('m/d/Y g:i A') }}</h6>
                                    <hr>
                                    <p class="card-text">
                                        <div class="message-static">{!! $post->post_body !!}</div>
                                        @admin
                                        <textarea id="post-message-{{ $post->id }}" class="edit-message"
                                                  style="display:none;">{!! $post->post_body !!}</textarea>
                                    @endadmin
                                    @if($post->created_at != $post->updated_at)
                                        <hr><h6 class="text-muted">
                                            <em>Updated {{ $post->updated_at->format('m/d/Y g:i A') }}</em></h6>
                                        @endif
                                        </p>
                                </div>
                            </div>
                        @endforeach
                        {{ $announcements->links() }}
                    @else
                        <div class="alert alert-info"><i class="fas fa-info-circle"></i> No Announcements to show
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection