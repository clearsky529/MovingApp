<div class="btn-group" style="display: flex;">
    <button type="button" class="btn btn-sm btn-primary">More Actions</button>
    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li><a target="_blank" href="{{ route('company-admin.moves.delivery-icr',Crypt::encrypt($move->delivery->id)) }}">View ICR</a></li>
        @if($move->uplift->status == 2)
            <li><a href="#" class="change-status" data-status="{{ $move->delivery->status }}" data-id="{{ $move->delivery->id }}" data-toggle="modal" data-target="#modal-default" data-type="delivery">Change Status</a></li>
        @endif
        <li><a target="_blank" href="{{ route('company-admin.moves.edit-delivery',Crypt::encrypt($move->delivery->id)) }}">Edit Delivery</a></li>
        @if($move->deliveryRiskAssessment)
            <li><a target="_blank" href="{{ route('company-admin.moves.delivery-risk-assessment',Crypt::encrypt($move->id)) }}">Risk Assessment</a></li>
        @endif
        <li><a target="_blank" href="{{ route('company-admin.moves.show-delivery',Crypt::encrypt($move->delivery->id)) }}">View Move Details</a></li>
        <li><a target="_blank" href="{{ route('company-admin.moves.delivery-movecomment-pdf',Crypt::encrypt($move->delivery->id))}}">Pre Move Comment</a></li>
        <li><a target="_blank" href="{{ route('company-admin.moves.delivery-postmovecomment-pdf',Crypt::encrypt($move->delivery->id))}}">Post Move Comment</a></li>
        <li><a target="_blank" href="{{ route('company-admin.moves.delivery-icrimage',[Crypt::encrypt($move->id)])}}">View Delivery ICR Images</a></li>
    </ul>
</div>
