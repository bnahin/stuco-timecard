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
    <table class="table" id="events-table">
        <thead class="thead-dark">
        <tr>
            <th style="width:15%;">Order</th>
            <th>Event Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="events-body">
        @if(count($data))
            @foreach($data as $event)
                <tr data-id="{{ $event->id }}">
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
                        <button class="btn btn-outline-danger delete-event" data-id="{{ $event->id }}" rel="tooltip"
                                title="Delete event"><i
                                class="fas fa-times"></i></button>
                        <button class="btn btn-outline-warning purge-event" data-id="{{ $event->id }}"
                                rel="tooltip"
                                title="Purge event hours"><i
                                class="fas fa-backward"></i></button>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
        <tbody>
        <tr class="bg-light">
            <td><h6 class="text-success"><strong><i class="fas fa-plus"></i> New</strong></h6></td>
            <td>
                <div class="input-group mb-3">
                    <input class="form-control event-input" placeholder="ex. Programming Club">
                    <div class="input-group-append">
                        <button class="btn btn-outline-success add-event" type="button"><i
                                class="fas fa-check"></i></button>
                    </div>
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
        </tbody>
    </table>
</div>