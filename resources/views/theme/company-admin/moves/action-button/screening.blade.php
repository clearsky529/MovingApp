<div class="btn-group" style="display: flex;">
    <button type="button" class="btn btn-sm btn-primary">More Actions</button>
    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        @isset($move->screening)
            <li><a href="#" class="change-status" data-status="{{ $move->screening->status }}" data-id="{{ $move->screening->id }}" data-toggle="modal" data-target="#modal-default" data-type="screening">Change Status</a></li>
        @endisset
        <li><a target="_blank" href="{{ route('company-admin.moves.screen-icr',Crypt::encrypt($move->id)) }}">View ICR</a></li>
    </ul>
</div>
