<!DOCTYPE html>
<html>
<head>
	<!-- Required meta tags -->
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Kika Overflow ICR PDF Format - {{ Request::segment(1) }}</title>
	<style>
		body {
            font-family: customarialfont;
        }
		.company-name {
			font-size: 35px;
			padding: 5px 10px;
			border: 1px solid #000;
		}
		.text-black{
			color: #000;
		}
		.mb-10{
			margin-bottom: 10px;
		}
		.report-delivery{
			padding: 0px 5px;
			border: 1px solid #000;
			font-size: 15;
			background-color: #d5d5d5;
			display: inline-block;
			width: 100%;
		}
		/* .report-delivery tr td{
			font-size: 20px;
		} */
		.report-delivery tr td:last-child{
			text-align: right;
			/* font-size: 16px; */
		}
		.report-delivery span{
			padding: 0 10px;
			font-size: 20px;
		}
		.text-gray{
			color: #b1b1b1;
		}
		.customer-address{
			padding: 5px 10px;
			border: 1px solid #000;
			font-size: 16px;
			display: inline-block;
			width: 100%;
		}
		.address-up{
			display: inline-block;
			width: 100%;
		}
		.address-up table tr td{
			font-weight: bold;
		}
		.address-up table tr td span{
			font-weight: normal;
		}
		.table-header{
			border:1px solid #000 !important;
			border-style: inset;
			/* border-bottom: 0px; */
			font-size: 14px;
			background-color: #d5d5d5;
			padding: 5px 10px;
			margin:0 0.5px;
		}
		table {
			border-collapse: collapse;
			width: 100%;
		}
		table, th, td {
			border: 1px solid #000;
			color: #000;
		}
		table.border-0,
		.border-0 th,
		.border-0 td{
			border: none;
		}
		th, td{
			padding: 5px;
			font-size: 14px;
		}
		.exception-symbols-table tr td:nth-child(odd),
		.descriptive-symbols-table tr td:nth-child(odd),
		.location-symbols-table tr td:nth-child(odd){
			text-align: center;
			width: 50px;
		}
		.discription-location-both{
			display: table;
			width: 100%;
		}
		.item-condition-table thead {
			background-color: #d5d5d5;
		}
		.item-condition-table thead tr th,
		.item-condition-table tbody tr td{
			font-size: 16px;
			font-weight: normal;
		}
		.item-condition-table tr,
		.item-condition-table tr td{
			page-break-inside: auto;
		}
		.item-condition-table tbody tr td:nth-child(1),
		.item-condition-table tbody tr td:nth-child(3),
		.item-condition-table tbody tr td:last-child{
			text-align: center;
		}
		.checklist-box{
			border: 1px solid #000;
			padding: 10px;
			width: 100%;
			display: inline-block;
			box-sizing: border-box;
		}
		.checklist-title,
		.left-packed{
			font-size: 18px;
		}
		.left-packed{
			border-bottom: 1px solid #000;
			display: table;
			width: 100%;
		}
		.left-packed div{
			padding: 5px 0;
			width: 49%;
			display: table-cell;
		}
		.left-packed div:last-child{
			text-align: right;
		}
		.red-box{
			padding: 5px 10px;
			border: 1px solid red;
		}
		.black-box{
			padding: 5px 10px;
			border: 1px solid #000;
			text-align: left;
			margin-top: 10px;
      overflow:hidden;
      word-wrap: break-word;
      word-break: break-all;
		}
		.checbox-list div{
			font-size: 16px;
		}
		.signature{
			display: inline-block;
		    width: 100%;
		    vertical-align: top;
		    text-align: center;
		}
		.img-wrap {
			width: 100%;
			margin-bottom: 10px;
		}
		.img-box {
			width: 120px;
			height: 120px;
		}
		.img-box img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}
		.address-up table th,
		.address-up table td{
			text-align: left !important;
		}
		.descriptive-symbols-table tbody tr td {
			font-size:13px !important;
		}
		.discription-location-both .location-inner {
			margin-right:8px !important;
		}
		.location-symbols-table tr td:nth-child(odd) {
			width:30px !important;
		}
		.report-delivery tr td:last-child {
			text-align: left;
		}
		.item-condition-table tbody tr td:nth-child(1), .item-condition-table tbody tr td:nth-child(3), .item-condition-table tbody tr td:last-child {
			text-align: center;
			width: 30px;
		}
		.checbox-list div input[type="checkbox"] {
			display:inline-block;
			width: 15px;
			/* margin-top:3px; */
			margin-right:5px;
		}
		.checbox-list div label {
			display:inline-block;
			width: 95%;
			/* padding-bottom:2px; */
		}
		.item-condition-table tbody tr td:nth-child(2) {
			width:370px;
		}

		 td {
			border:1px solid #ddd;
		}

		table.item-condition-table thead {
			border-bottom:1px solid #000;
		}
		table.item-condition-table thead tr th{
			border-right:1px solid #000;
		}

		.exception-symbols-table tbody tr td:nth-child(1) {
			border-left:1px solid #000;
		}
		/* .exception-symbols-table tbody tr:nth-child(1) td {
			border-top:1px solid #000;
		} */
		.descriptive-symbols-table tr td:nth-child(1) {
			border-left:1px solid #000;
		}
		/* .descriptive-symbols-table tr:nth-child(1) td {
			border-top:1px solid #000;
		} */

		table.item-condition-table tbody tr td{
			border-left:1px solid #000;
		}

		.descriptive-symbols-table tbody tr:last-child td {
			border-bottom: 1px solid #000;
		}
		.location-symbols-table tr td:nth-child(1) {
			border-left:1px solid #000;
		}
		/* .location-symbols-table tr:nth-child(1) td {
			border-top:1px solid #000;
		} */
		.item-condition-table tbody tr:last-child td {
			border-bottom: 1px solid #000;
		}
		.item-condition-table tbody tr td:last-child {
			border-right: 1px solid #000;
		}
		.location-symbols-table tbody tr td {
			font-size:13px !important;
		}
		.f-16 {
			font-size:16px !important;
		}
		.p-10 {
			padding-top: 10px;
		}
		.table-signature {
			transform: translate(-2.3px, 0);
		}

		#header .main-wrapper .report-delivery {
			padding: 0 3px;
		}

		@font-face {
			font-family: 'Conv_Arial';
			src: url('public/fonts/arial/Arial.eot');
			src: local('☺'), url('public/fonts/arial/Arial.woff') format('woff'), url('public/fonts/arial/Arial.ttf') format('truetype'), url('public/fonts/arial/Arial.svg') format('svg');
			font-weight: normal;
			font-style: normal;
		}
		.text-right{
			text-align: right !important;
		}
		.red-box {
			border: 1px solid #000;
		}

		.f-14 {
			font-size: 15px !important;
		}
		.border-part.new-condition .item-condition-table tbody tr td {
			font-size: 12px !important;
			padding: 2px !important;
		}
		.border-part.new-condition .item-condition-table tbody tr td:nth-child(1){
			border-right: none !important;
		}
		.border-part.new-condition .item-condition-table tbody tr td:nth-child(2){
			border-left: none !important;
		}
		.description-locations-infos table tr td {
			font-size: 9px !important;
			padding: 2px !important;
		}
		.checbox-list div label {
			font-size: 12px !important;
			/* padding: 2px 0 !important; */
		}
		.checklist-title, .left-packed {
			font-size: 14px !important;
			padding:2px 0px !important;
		}
		.description-locations-infos .table-header {
			font-size: 11px !important;
			padding:2px 8px !important;
		}
		 .checklist-box .checklist-title {
			font-size: 14px !important;
			padding:0px 8px 2px 0px !important;
		}
		.main-wrapper .customer-address .address-up table tr td {
			padding: 3px 5px !important;
		}
		.mb_2 {
			padding-bottom: 5px;
		}
		body.pdf-part {
			font-family:'Conv_Arial',Sans-Serif;
		}

		@page { margin-top:150px;}
    	#header { position: fixed; left: 0px; top: -105px; right: 0px; margin-bottom:100px;
    	}
		.checbox-list div{
		font-size: 12px;
		}
	</style>
</head>

<body class="pdf-part">
<div class="main-wrapper">

	<div class="clearfix"></div>
	<div class="mb-10 customer-address">
		<div class="address-up">
			<div style="">
				<table class="border-0">
					<tr>
						<td class="text-black"><b>Origin : </b><span style="font-weight: normal;">{{ $move->uplift_address }}</span></td>
					</tr>
<!--					<tr>
						<td class="text-black" colspan="2">Phone : <span>{{ $move->move->contact->contact_number }}</span></td>
					</tr>-->
					<tr>
						<td class="text-black" colspan="2"><b>Destination : </b><span style="font-weight: normal;">{{ $move->move->delivery ? $move->move->delivery->delivery_address : "" }}</span></td>
					</tr>
                    <tr>
                        <td class="text-black" colspan="2"><b>Container Number : </b><span style="font-weight: normal;">{{ $move->move->container_number }}</span></td>
                    </tr>
				</table>
			</div>
		</div>
	</div>

	<div class="clearfix"></div>
	<div class="description-locations-infos">
	<div class="mb-10 border-part">
		<div class="text-black table-header">Exception Symbols</div>
		<table class="exception-symbols-table">
			<tbody>
				@php
					$exception_break_count = 0;
					$exception_count = 0;
				@endphp
				@foreach($exceptions as $key => $exception)
					@php $exception_break_count++; $exception_count++; @endphp
					@if($exception_break_count == 1)
						<tr>
					@endif
						<td @if(count($exceptions)-1 == $key) style="border-bottom: 1px solid black" @endif>{{ $exception->condition_code }}</td>
						<td style="border-right:($exception_break_count == 4 ? '1px solid black' : 'unset'); @if(count($exceptions)-1 == $key) border-bottom: 1px solid black; @endif ">{{ $exception->condition }}</td>
					@if($exception_break_count == 4 || $exception_count == count($exceptions))
						@php $exception_break_count = 0; @endphp
						</tr>
					@endif
				@endforeach
			</tbody>
		</table>
	</div>
	<div class="mb-10 discription-location-both" style="clear: both;">
		<div class="location-inner border-part" style="width: 34%; float: left">
			<div class="text-black table-header">Descriptive Symbols</div>
			<table class="descriptive-symbols-table">
				@foreach($descriptions as $description)
					@if(!in_array($description->id,[5,6]))
					<tr>
						<td style="border-bottom: ($description->id == 4 ? '1px solid #000000' : 'unset')">{{ $description->code }}</td>
						<td style="border-right: 1px solid black;border-bottom: ($description->id == 4 ? '1px solid #000000' : 'unset')">{{ $description->package_status }}</td>
					</tr>
					@endif
				@endforeach
			</table>
		</div>
		<div class="border-part" style="width: 65%; float: right">
			<div class="text-black table-header">Location Symbols</div>
			<table class="location-symbols-table">
				@php
					$location_break_count = 0;
					$location_count = 0;
				@endphp
				@foreach($conditionLocations as $key => $conditionLocation)
					@php $location_break_count++; $location_count++; @endphp
					@if($location_break_count == 1)
						<tr>
					@endif
						<td @if(count($conditionLocations)-3 <= $key) style="border-bottom: 1px solid black" @endif>{{ $conditionLocation->side_code }}</td>
						<td style="border-right:($location_break_count == 3 ? '1px solid black' : 'unset'); border-bottom: (count($conditionLocations)-3 <= $key ? '1px solid black' : 'unset');">{{ $conditionLocation->side }}</td>
					@if($location_break_count == 3 || $location_count == count($conditionLocations))
						@php $location_break_count = 0; @endphp
						</tr>
					@endif
				@endforeach
			</table>
		</div>
	</div>
	</div>
	<?php
		$total_items = $move_items;
		$count = count($total_items);
		$table_padding = '6px';
		if($count == 0) {
			$table_padding = '15px';
		}
	?>
		@if($count == 26 || $count == 27 || $count == 28 || $count == 29)
			<div class="mb-10 border-part new-condition" style="table-layout: fixed; width:100%; page-break-after: always;">
		@else
			<div class="mb-10 border-part new-condition" style="table-layout: fixed; width:100%;">
		@endif

		<table class="item-condition-table">
			<thead>
				<tr style="background-color:#dddddd">
					<th style="padding: {{$table_padding}};">No.</th>
					<th style="padding: {{$table_padding}};">Item and Condition at Origin</th>
					<th style="padding: {{$table_padding}};">D</th>
					<th style="padding: {{$table_padding}};">Condition At Destination</th>
					<th style="padding: {{$table_padding}};" width="8%"></th>
				</tr>
			</thead>
			<tbody>

				@foreach($move_items as $key => $move_item)
					<tr>
						<?php $checkConditionImage = 0; ?>
						<td @if((count($move_items)-1) == $key) style="border-bottom: 1px solid black" @endif>

						{{ $move_item->item_number }}</td>


						<td @if((count($move_items)-1) == $key) style="border-bottom: 1px solid black" @endif>
						<?php
							if($move_item->itemUpliftCategory)

								if($move_item->itemUpliftCategory->cartoon_code == 'FB' || $move_item->itemUpliftCategory->cartoon_code == 'CT')
								{
									echo "";
								}
                                else{
                                    echo $move_item->itemUpliftCategory->cartoon_code.' '.':';
                                }
							else{
								echo "";
							}
								?>
						<!-- <?php ?>
						{{$move_item->itemUpliftCategory ? $move_item->itemUpliftCategory->cartoon_code : ''}} : -->
						@if($move_item->item_id == 0 && $move_item->item != null)
							{{$move_item ? $move_item->item : ''}}
						@elseif($move_item->item_id == 0)
						    <?php $data = ''; ?>
						    @foreach($move_item->cartoonItem as $cartoonItem)
							   @if($cartoonItem->cartoonItemDetails)
							       <?php $data .= $cartoonItem->cartoonItemDetails->item.', '; ?>
							  @else
							       <?php $data =''; ?>
							  @endif
							 
							@endforeach
							{{rtrim($data,', ')}}
						@elseif($move_item->item_id != 0)
						    {{ $move_item->subItems ? $move_item->subItems->subItemDetails->item : str_replace('+','',$move_item->item) }}
					    @endif
						@if($move_item->itemPacker)
              				@if($move_item->itemUpliftCategory->cartoon_code == 'FB')
								@if($move_item->item_id == 36 && $move_item->itemPacker->code == "PBR")
										{{ "-" }}
										{{$move_item->itemPacker->code}}
								@elseif($move_item->item_id == 36 && $move_item->itemPacker->code == "PBO")
									{{ "-" }}
										{{$move_item->itemPacker->code}}
								@elseif($move_item->itemPacker->code == "DBR" || $move_item->itemPacker->code == "DBO")
									{{ "-" }}
									{{$move_item->itemPacker->code}}
								@else

								@endif
							@elseif($move_item->itemUpliftCategory->cartoon_code != 'FB')
									{{ "-" }}
									{{$move_item->itemPacker->code}}
							@else

							@endif
						@endif
							@foreach($move_item->condition as $condition)
							@if($condition)
								{{ "-" }}
								@php
								if($checkConditionImage == 0) {
									$checkConditionImage = ($condition->conditionImage()->exists() && $condition->conditionImage[0]->image ? 1 : 0);
								}
								@endphp
							@endif
								{{$condition->conditionDetails->condition_code}}
								@foreach($condition->conditionSides as $key => $conditionSide)
								@if($key == count($condition->conditionSides)-1)
								{{$conditionSide->sideDetails->side_code}}
								@else
								{{$conditionSide->sideDetails->side_code}},
								@endif
								@endforeach
							@endforeach
						</td>
						<td style="border-bottom: ((count($move_items)-1) == $key ? '1px solid black': 'unset');">
							<strong>{{ ($move_type == 1 && $checkConditionImage == 1 ? 'o' : '') }}</strong>
						</td>
						<td style="border-bottom: ((count($move_items)-1) == $key ? '1px solid black': 'unset'); padding-left: 10px !important;">

						</td>
						<td style="border-bottom: ((count($move_items)-1) == $key ? '1px solid black': 'unset');border-right: 1px solid black;">

						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	@if($move_type == 1)
		<div class="mb-10 checklist-box">
			<div class="checklist-title mb_2">Post Move Checklist - Uplift</div>

			<div class="mb-10 checbox-list">
				@foreach($termsAndConditions as $termsAndCondition)
					@if($termsAndCondition->move_type == 1 && $termsAndCondition->move_status == 1)
						<div style="margin-top: 5px">
							@if(in_array($termsAndCondition->id, $checked['postUpliftChecked']))
								<img src="{{asset('/frontend/assets/images/black-check.png')}}" alt="" style="width: 21px; height: 21px;vertical-align: middle">
							@else
								<img src="{{asset('/frontend/assets/images/black-unchecked.png')}}" alt="" style="width: 20px; height: 20px;vertical-align: middle;">
							@endif
							<label for="list1">&nbsp;&nbsp;{{ $termsAndCondition->terms_and_conditions }}</label>
						</div>
					@endif
				@endforeach
			</div>
			@foreach($comments as $key => $comment)
				@if($comment->move_type == 1 && $comment->move_status  == 1)
					@if($comment->comment)
                    <div class="mb-10 black-box">
                        @php
                            echo nl2br($comment->comment);
                        @endphp
                    </div>
					@endif
				@endif
			@endforeach
			<div class="clearfix"></div>
			<div style="width: 100%">
				<div style="width: 49%; float: left">
					<div class="mb-10" style="width:100%; margin-top: 7px; border: 1px solid #000; padding: 5px;">Client/Agent : <span>{{ $packageSignature['uplift'] ? $packageSignature['uplift']->client_name : '' }}</span></div>
					<div class="mb-10" style="width:100%; border: 1px solid #000; padding: 5px; height: 80px;">
					<img style="width:320px; object-fit:contain; height: 80px;" src="{{ $packageSignature['uplift'] ? $packageSignature['uplift']->client_signature : '' }}">
					</div>
					<div style="width:100%; border: 1px solid #000; padding: 5px; height: 18px;">
						{{ $packageSignature['uplift'] ? date_format($packageSignature['uplift']->created_at,'D d M Y') : '' }}
					</div>
				</div>
				<div style="width: 50%; float: right">
					<div class="mb-10" style="width:100%; margin-top: 7px; border: 1px solid #000; padding: 5px;">
						Removalist : <span>{{ $packageSignature['uplift'] ? $packageSignature['uplift']->employee_name : '' }}</span>
					</div>
					<div class="mb-10" style="width:100%; border: 1px solid #000; height: 80px; padding: 5px;">
						<img style="width:320px; object-fit:contain; height: 80px;" src="{{ $packageSignature['uplift'] ? $packageSignature['uplift']->employee_signature : '' }}">
					</div>
					<div style="width:100%; border: 1px solid #000; height: 18px; padding: 5px;">
						{{ $packageSignature['uplift'] ? date_format($packageSignature['uplift']->created_at,'D d M Y') : '' }}
					</div>
				</div>
			</div>
		</div>
	@endif

	<div class="clearfix"></div>
</div>
</body>
</html>
