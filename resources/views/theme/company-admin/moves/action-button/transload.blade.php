<div class="btn-group" style="display: flex;">
    <button type="button" class="btn btn-sm btn-primary">More Actions</button>
    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @isset($move->transload)
        <li><a href="{{ route('company-admin.moves.transload-activity',Crypt::encrypt($move->transload->id)) }}" target="_blank">View
                Activity</a></li>
        @endisset
        <li><a href="{{ route('company-admin.moves.transload-icr',Crypt::encrypt($move->id)) }}" target="_blank">View Bingo Sheet</a>
        </li>
        @isset($move->transload->status)
            <li><a href="#" class="change-status" data-status="{{ $move->transload->status }}"
                   data-id="{{ $move->transload->id }}" data-toggle="modal" data-target="#modal-default"
                   data-type="transload">Change status</a></li>
        @endisset
    </ul>
</div>
