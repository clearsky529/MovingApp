<!DOCTYPE html>
<html>
<head>
	<!-- Required meta tags -->
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Kika ICR PDF Format</title>
	<style>
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
			padding: 5px 10px;
			border: 1px solid #000;
			font-size: 20px;
			background-color: #d5d5d5;
			display: inline-block;
			width: 99%;
		}
		.report-delivery tr td{
			font-size: 20px;
		}
		.report-delivery tr td:last-child{
			text-align: right;
			font-size: 16px;
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
			width: 97%;
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
			border-bottom: 0px;
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
			margin-top:3px;
			margin-right:5px;
		}
		.checbox-list div label {
			display:inline-block;
			width: 95%;
			padding-bottom:2px;
		}
		.item-condition-table tbody tr td:nth-child(2) {
			width:370px;
		}
		.item-condition-table.item-icr-table tbody tr td:nth-child(1) {
			width:10px;
		}
		.item-condition-table.item-icr-table tbody tr td:nth-child(2) {
			text-align: center;
		}
		.item-condition-table.item-icr-table tbody tr td:nth-child(2),
		.item-condition-table.item-icr-table tbody tr td:nth-child(4) {
			width:30px;
		}
		.item-condition-table.item-icr-table tbody tr td:nth-child(3) {
			width:250px;
		}
		.item-condition-table.item-icr-table tbody tr td:last-child {
			width:150px;
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

		.exception-symbols-table tbody tr td {
			border-left:1px solid #000;
		}
		.exception-symbols-table tbody tr td:last-child {
			border-right:1px solid #000;
		}
		.exception-symbols-table tbody tr:last-child td {
			border-bottom:1px solid #000;
		}
		.exception-symbols-table tbody tr:first-child td {
			border-top:1px solid #000;
		}
		.descriptive-symbols-table tr td:first-child, .location-symbols-table tbody tr:last-child {
			border-left:1px solid #000 !important;
		}
		.descriptive-symbols-table tr td:last-child {
			border-right:1px solid #000;
		}
		.descriptive-symbols-table tr:last-child {
			border-bottom:1px solid #000;
		}
		table.item-condition-table tbody tr td{
			border-left:1px solid #000;
		}

		.descriptive-symbols-table tbody tr:last-child td {
			border-bottom: 1px solid #000;
		}
		.location-symbols-table tbody tr td:last-child {
			border-right: 1px solid #000;
		}
		.location-symbols-table tbody tr td:first-child {
			border-left: 1px solid #000;
		}

		.location-symbols-table tbody tr:last-child td {
			border-bottom: 1px solid #000;
		}
		.item-condition-table tbody tr:last-child td {
			border-bottom: 1px solid #000;
		}
		.item-condition-table tbody tr td:last-child {
			border-right: 1px solid #000;
		}
		.descriptive-symbols-table tbody tr:first-child td, .location-symbols-table tbody tr:first-child td {
			border-top:1px solid #000;
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
			padding: 2px 0 !important;
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
		.paper-view-table td,
		.paper-view-table th,
		table.paper-view-table tr td table tr td,
		table.paper-view-table tbody>tr:first-child>td table tr th:nth-child(3) {
			border-bottom: 1px solid #ddd;
		}
		table.paper-view-table tbody>tr:first-child>td {
			border-bottom: none;
			padding-bottom: 0;
		}
		.paper-view-table tr:last-child td{
			border-bottom: none;
		}
		table.paper-view-table {
			border: 1px solid #000;
			border-collapse: collapse;
		}
		table.paper-view-table tr td,
		table.paper-view-table tr th {
			text-align: left;
			padding: 10px;
			font-size: 16px;
			line-height: 16px;
		}
		table.paper-view-table tr:first-child th:first-child {
			width: 23%;
		}
		table.paper-view-table tr:first-child th+th {
			width: 5%;
			text-align: center;
		}
		table.paper-view-table tbody>tr:first-child>td table tr:nth-child(2) th,
		table.paper-view-table tbody>tr:first-child>td table tr:nth-child(3) th {
			width: 5%;
		}
		table.paper-view-table tr:first-child td:first-child {
			width: 42%;
		}
		table.paper-view-table tr:first-child td:last-child {
			width: 30%;
		}
		table.paper-view-table tbody>tr:first-child>td table tr:nth-child(2) td,
		table.paper-view-table tbody>tr:first-child>td table tr:nth-child(3) td {
			width: 95%;
		}
		/* table.paper-view-table tr:nth-child(n+4) td, */
		table.paper-view-table tr table tr td {
			height: 15px;
		}

		@page { margin-top:150px;}
    	#header { position: fixed; left: 0px; top: -105px; right: 0px; margin-bottom:100px;
    	}
	</style>
</head>

<body class="pdf-part">
<script type="text/php">
	if ( isset($pdf) ) {
		$x = 485;
		$y = 88;
		$text = "Page {PAGE_NUM} of {PAGE_COUNT}";
		$font = $fontMetrics->get_font("arial");
		$size = 11.5;
		$color = array(0,0,0);
		$word_space = 0.0;  //  default
		$char_space = 0.0;  //  default
		$angle = 0.0;   //  default
		$pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
	}
</script>
<div id="header">
 <div class="main-wrapper">
	<div class="company-name text-black mb-10">{{ $upliftMove->origin_agent }}</div>
		<div class="clearfix"></div>
		<div class="mb-10 report-delivery">
			<table class="border-0">
				<tr>
					<td class="text-black f-14">Screened Inventory  Report - {{ $move->contact->contact_name }} : {{ $move->move_number }}</td>
				</tr>
			</table>
		</div>
	</div>
  </div>
<div class="main-wrapper">

	<div class="clearfix"></div>
	<div class="mb-10 customer-address">
		<div class="address-up">
			<div style="">
				<table class="border-0">
					<tr>
						<td class="text-black">Origin : <span>{{ $upliftMove->uplift_address }}</span></td>
					</tr>
<!--					<tr>
						<td class="text-black" colspan="2">Phone : <span>{{ $move->contact->contact_number }}</span></td>
					</tr>-->
					<tr>
						<td class="text-black" colspan="2">Destination : <span>{{ $move->delivery ? $move->delivery->delivery_address : "" }}</span></td>
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
				@php
					$exception_break_count = 0;
					$exception_count = 0;
				@endphp
				@foreach($exceptions as $key => $exception)
					@php $exception_break_count++; $exception_count++; @endphp
					@if($exception_break_count == 1)
						<tr>
					@endif
						<td>{{ $exception->condition_code }}</td>
						<td @if(count($exceptions) == 21 && count($exceptions)-1 == $key) colspan="7" @endif>{{ $exception->condition }}</td>
					@if($exception_break_count == 4 || $exception_count == count($exceptions))
						@php $exception_break_count = 0; @endphp
						</tr>
					@endif
				@endforeach
		</table>
	</div>
	<div class="mb-10 discription-location-both">
		<div class="location-inner border-part" style="width: 35%; display: table-cell;">
			<div class="text-black table-header">Descriptive Symbols</div>
			<table class="descriptive-symbols-table">
				@foreach($descriptions as $description)
					@if(!in_array($description->id,[5,6]))
					<tr>
						<td>{{ $description->code }}</td>
						<td>{{ $description->package_status }}</td>
					</tr>
					@endif
				@endforeach
			</table>
		</div>
		<div style="width: 1%; display: table-cell;"></div>
		<div class="border-part" style="width: 59%; display: table-cell;">
			<div class="text-black table-header">Location Symbols</div>
			<table class="location-symbols-table">
				@php
					$location_break_count = 0;
					$location_count = 0;
				@endphp
				@foreach($conditionLocations as $conditionLocation)
					@php $location_break_count++; $location_count++; @endphp
					@if($location_break_count == 1)
						<tr>
					@endif
						<td>{{ $conditionLocation->side_code }}</td>
						<td>{{ $conditionLocation->side }}</td>
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
	?>
		@if($count == 26 || $count == 27 || $count == 28 || $count == 29)
			<div class="mb-10 border-part new-condition" style="table-layout: fixed; width:100%; page-break-after: always;">
		@else
			<div class="mb-10 border-part new-condition" style="table-layout: fixed; width:100%;">
		@endif

		<table class="item-condition-table item-icr-table">
			<thead>
				<tr>
					<th width="10px">No.</th>
					<th width="30px">Cat.</th>
					<th width="250px">Item & Condition</th>
					<th width="30px">Loc.</th>
                    <th width="150px">Notes</th>
				</tr>
			</thead>
			<tbody>
				 @foreach($move_items as $key => $move_item)
           			@foreach($move_item->items as $key => $data)
           			<tr>
           				<td>{{ $data->item_number }}</td>
                        <td>
							<?php
								if(isset($data->itemScreeningCategory))
								{
									if($data->itemScreeningCategory->Category->category_name == 'Quarantine'){
										$catName = 'Q';
									}elseif($data->itemScreeningCategory->Category->category_name == 'Good'){
										$catName = 'G';
									}elseif($data->itemScreeningCategory->Category->category_name == 'Storage'){
										$catName = 'S';
									}elseif($data->itemScreeningCategory->Category->category_name == 'House'){
										$catName = 'H';
									}
									else{
										$catName = 'L2';
									}
								}else{
									$catName = ' ';
								}
							?>
							{{$catName}}
						</td>
                        <td></td>
                        <td></td>
                        <td></td>
           			</tr>
           			@endforeach
                @endforeach


			</tbody>
		</table>
	</div>

	<div class="clearfix"></div>

	<table class="paper-view-table border-0">
		<tbody>
			<tr>
				<td colspan="4">
					<table class="border-0">
						<tr>
							<th>Checked By :</th>
							<td></td>
							<th>Date :</th>
							<td></td>
						</tr>
						<tr>
							<th>Crew :</th>
							<td colspan="3"></td>
						</tr>
						<tr>
							<th>Notes :</th>
							<td colspan="3"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<table class="border-0">
						<tr><td></td></tr>
						<tr><td></td></tr>
						<tr><td></td></tr>
						<tr><td></td></tr>
						<tr><td></td></tr>
						<tr><td></td></tr>
					</table>
				</td>
			</tr>

		</tbody>
	</table>

</div>
</body>
</html>
