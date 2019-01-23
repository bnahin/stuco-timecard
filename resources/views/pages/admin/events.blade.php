@php
    $viewDeleted = $param == "deleted";
@endphp

<div id="events">
    <h5>Events Management</h5>
    <hr>
    <div class="card" id="events-info">
        <div class="card-body">
            <i class="fas fa-info-circle"></i>
            Deleting events will only remove it from this list. It will still be included in students' hours and
            statistics if not purged.
        </div>
    </div>
    <ul class="nav nav-tabs justify-content-center" id="events-management-tab" role="tablist">
        <li class="Active Events">
            <a class="nav-link @if($param != "deleted") active @endif" id="home-tab"
               href="{{ route('admin', ['page' => 'events']) }}" role="tab" aria-controls="active"
               aria-selected="true">Active Events</a>
        </li>
        <li class="Deleted Events">
            <a class="nav-link @if($param == "deleted") active @endif" id="profile-tab"
               href="{{ route('admin', ['page' => 'events', 'param' => 'deleted']) }}" role="tab" aria-controls="active"
               aria-selected="false">Deleted Events</a>
        </li>
    </ul>
    <table class="table" id="events-table">
        <thead class="thead-dark">
        <tr>
            @if(!$viewDeleted)
                <th style="width:15%;">Order</th>
                <th>Event Name</th>
                <th>Actions</th>
            @else
                <th>Deleted Date</th>
                <th>Event Name</th>
                <th>Actions</th>
            @endif
        </tr>
        </thead>
        <tbody id="events-body">
        @if(count($data))
            @foreach($data as $event)
                <tr data-id="{{ $event->id }}">
                    @if(!$viewDeleted)
                        <td class="order-arrows">
                            <button class="btn btn-warning order @if($loop->last)btn-hide @endif" data-dir="down"><i
                                    class="fas fa-arrow-down"></i></button>
                            <button class="btn btn-primary order @if($loop->first)btn-hide @endif" data-dir="up"><i
                                    class="fas fa-arrow-up"></i></button>
                        </td>
                        <td>
                            <div class="input-group mb-3">
                                <input class="form-control event-input"
                                       value="{{ $event->event_name }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-success update-event" type="button"
                                            data-id="{{ $event->id }}"><i
                                            class="fas fa-check"></i></button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <button
                                class="btn {{  (!$event->is_active) ? 'btn-outline-info':'btn-primary' }} update-vis"
                                data-id="{{ $event->id }}"
                                rel="tooltip"
                                title="Toggle visibility"><i
                                    class="fas fa-eye"></i></button>
                            <button class="btn btn-outline-danger delete-event" data-id="{{ $event->id }}"
                                    rel="tooltip"
                                    title="Delete event"><i
                                    class="fas fa-times"></i></button>
                            <button class="btn btn-outline-warning purge-event" data-id="{{ $event->id }}"
                                    rel="tooltip"
                                    title="Purge event hours"><i
                                    class="fas fa-backward"></i></button>
                            <a href="{{ route('view-event', $event) }}" target="_blank">
                                <button class="btn btn-success"
                                        rel="tooltip"
                                        title="View event attendance"><i
                                        class="fas fa-users"></i></button>
                            </a>
                        </td>
                    @else
                        <td>{{ $event->deleted_at->format('m/d/Y h:i a') }}</td>
                        <td>{{ $event->event_name }}</td>
                        <td>
                            <button class="btn btn-danger delete-event" data-id="{{ $event->id }}"
                                    data-isperm="true"
                                    rel="tooltip"
                                    title="Permanently delete event"><i
                                    class="fas fa-times"></i> Destroy
                            </button>
                            <button class="btn btn-success restore-event" data-id="{{ $event->id }}"
                                    data-isperm="true"
                                    rel="tooltip"
                                    title="Restore event"><i
                                    class="fas fa-undo"></i> Restore
                            </button>
                        </td>
                    @endif
                </tr>
            @endforeach
        @elseif($viewDeleted)
            <tr>
                <td class="text-center" colspan="3"><i class="fas fa-info-circle"></i> There are no deleted events.</td>
            </tr>
        @endif
        </tbody>
        @if(!$viewDeleted)
            <tbody>
            <tr class="bg-light">
                <td><h6 class="text-success"><strong><i class="fas fa-plus"></i> New</strong></h6></td>
                <td>
                    <div class="input-group mb-3">
                        <input class="form-control event-input" placeholder="ex. Clean up Santa Monica Beach">
                        <div class="input-group-append">
                            <button class="btn btn-outline-success add-event" type="button"><i
                                    class="fas fa-check"></i></button>
                        </div>
                    </div>
                </td>
                <td>&nbsp;</td>
            </tr>
            </tbody>
        @endif
    </table>
    @if($viewDeleted)
        {{ $data->links() }}
    @endif
</div>