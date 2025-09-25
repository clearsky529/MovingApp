<div class="btn-group" style="display: flex;">
    <button type="button" class="btn btn-sm btn-primary">More Actions</button>
    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li><a target="_blank" href="{{ route('company-admin.moves.uplift-icr', Crypt::encrypt($move->uplift->id)) }}">View ICR</a></li>

        @if ($move->is_overflow == 1)
            <li><a target="_blank" href="{{ route('company-admin.moves.uplift-overflow-icr', Crypt::encrypt($move->uplift->id)) }}">View
                    Overflow ICR</a></li>
        @endif

        <li><a href="javascript:void(0)" class="change-status" data-status="{{ $move->uplift->status }}"
               data-id="{{ $move->uplift->id }}" data-toggle="modal" data-target="#modal-default" data-type="uplift">Change
                Status</a></li>
        <li><a href="javascript:void(0)" data-delete_move_id="{{ $move->id }}" onclick="openEamilDeliveryIcrModel({{ $move->id }})" class="btn"
               style="text-align: left !important;">Email Delivery ICR</a></li>

        @if ($move->is_destination_agent_kika == 0 && $move->delivery)
            <li><a target="_blank" href="{{ route('company-admin.moves.send-to-delivery', Crypt::encrypt($move->uplift->id)) }}">Send To
                    Destination Agent</a></li>
        @endif

        @if ($move->uplift->status != 2)
            <li><a target="_blank" href="{{ route('company-admin.moves.edit-uplift', Crypt::encrypt($move->uplift->id)) }}">Edit
                    Uplift</a></li>
        @endif

        @if ($move->upliftRiskAssessment)
            <li><a target="_blank" href="{{ route('company-admin.moves.uplift-risk-assessment', Crypt::encrypt($move->id)) }}">Risk
                    Assessment</a></li>
        @endif

        <li><a target="_blank" href="{{ route('company-admin.moves.show-uplift', Crypt::encrypt($move->uplift->id)) }}">View Move
                Details</a></li>

        {{-- @if ($move->controlling_agent_email == $userId->email && isset($move->transload))
            @if (($move->uplift->status == 1) || ($move->screening->status == 1) || ($move->transload->status == 1) || ($move->delivery->status == 1))
                <li><a href="javascript:void(0)" data-delete_inprg_move_id="{{ $move->id }}" onclick="openDeleteInprgModel({{ $move->id }})" class="btn delete-inprg-move"
                       style="text-align: left !important;">Delete Move</a></li>
            @elseif (($move->uplift->status == 0 || $move->uplift->status == 2) || ($move->screening->status == 0 || $move->screening->status == 2) || ($move->transload->status == 0 || $move->transload->status == 1) || ($move->delivery->status == 0 || $move->delivery->status == 1))
                <li><a href="javascript:void(0)" data-delete_move_id="{{ $move->id }}"  onclick="openDeleteModel({{ $move->id }})" class="btn delete-move"
                       style="text-align: left !important;">Delete Move</a></li>
            @endif
        @endif --}}

        @if ($move->controlling_agent_email == $userId->email)
            @if (($move->uplift && $move->uplift->status != 2) || ($move->screening && $move->screening->status != 2) || ($move->transload && $move->transload->status != 2) || ($move->delivery && $move->delivery->status != 2))
                <li><a href="javascript:void(0)" data-delete_inprg_move_id="{{ $move->id }}" onclick="openDeleteInprgModel({{ $move->id }})" class="btn delete-inprg-move"
                       style="text-align: left !important;">Delete Move</a></li>
            @elseif (($move->uplift && $move->uplift->status == 2) || ($move->screening && $move->screening->status == 2) || ($move->transload && $move->transload->status == 2) || ($move->delivery && $move->delivery->status == 2))
                <li><a href="javascript:void(0)" data-delete_move_id="{{ $move->id }}"  onclick="openDeleteModel({{ $move->id }})" class="btn delete-move"
                       style="text-align: left !important;">Delete Move</a></li>
            @endif
        @endif
        
        @isset($move->transload)
            @if (($move->uplift->status == 1) || ($move->screening->status == 1) || ($move->transload->status == 1) || ($move->delivery->status == 1))
                <li><a href="javascript:void(0)" data-unarchive_inprg_move_id="{{ $move->id }}" onclick="openUnarchiveInprgMoveModel({{ $move->id }})" class="btn unarchive-inprg-move"
                       style="text-align: left !important;">Archive Move</a></li>
            @elseif (($move->uplift->status == 0 || $move->uplift->status == 2) || ($move->screening->status == 0 || $move->screening->status == 2) || ($move->transload->status == 0 || $move->transload->status == 1) || ($move->delivery->status == 0 || $move->delivery->status == 1))
                <li><a href="javascript:void(0)" data-archive_move_id="{{ $move->id }}" onclick="openArchiveMoveModel({{ $move->id }})"  class="btn archive-move"
                       style="text-align: left !important;">Archive Move</a></li>
            @endif
        @endif

        @if (!$move->delivery)
            <li><a target="_blank" href="{{ route('company-admin.moves.create-delivery', Crypt::encrypt($move->id)) }}">Create
                    Delivery</a></li>
        @endif

        <li>
            <a target="_blank" href="{{ route('company-admin.moves.uplift-movecomment-pdf', [Crypt::encrypt($move->id), Crypt::encrypt($move->company_id)]) }}">Pre
                Move Comment</a></li>
        <li>
            <a target="_blank" href="{{ route('company-admin.moves.uplift-postmovecomment-pdf', [Crypt::encrypt($move->id), Crypt::encrypt($move->company_id)]) }}">Post
                Move Comment</a></li>
        <li><a target="_blank" href="{{ route('company-admin.moves.uplift-icrimage', [Crypt::encrypt($move->id)]) }}">View Uplift ICR
                Images</a></li>
    </ul>
</div>
