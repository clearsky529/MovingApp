<!DOCTYPE html>
<html>

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="{{asset('css/pdf.css')}}">
	<title>{{$move_name}}</title>

	<style type="text/css">
		.company-name {
			padding: 5px 10px;
			border: 1px solid #000;
            font-size: 35px;
		}
		.company-name div {
			margin-top: 5px !important;
			font-size: 13px !important;
		}
		.text-black{
			color: #000;
		}
		.mb-10{
			margin-bottom: 10px;
		}
		.report-delivery {
            padding: 5px 0px;
            border: 1px solid #000;
            font-size: 20px;
            background-color: #d5d5d5;
            display: inline-block;
            width: 100%;
        }
		.f-14 {
            font-size: 15px !important;
        }
		.checklist-box{
			width: 100%;
			display: inline-block;
			box-sizing: border-box;
		}
		.checklist-title{
			font-size: 14px;
		}
		.checbox-list div{
			font-size: 12px;
		}
		.checbox-list div input[type="checkbox"] {
			display:inline-block;
			width: 15px;
			margin-top:5px;
			margin-right:5px;
		}
		.checbox-list div label {
			display:inline-block;
			width: 95%;
			padding-bottom:2px;
            vertical-align: top;
		}
		.black-box{
			padding: 5px 10px;
			border: 1px solid #000;
			text-align: left;
			overflow:hidden;
			word-wrap: break-word;
			word-break: break-all;
		}
		.signature{
			display: table;
			width: 100%;
			vertical-align: top;
		}
		.client-name,
		.client-signature,
		.client-date{
			border: 1px solid #000;
		}
		.client-signature{
			text-align: center;
			height: 80px;
			padding: 10px;
			box-sizing: border-box;
			display: inline-block;
			width: 100%;
		}
		.client-signature img {
			width: 100%;
			height: 60px;
		}
		.client-name,
		.client-date{
			font-size: 14px;
			padding: 5px 10px;
		}
		.signature-box {
			width: 40%;
			display: table-cell;
			vertical-align: top;
		}
		.img-wrap {
			width: 100%;
			margin-bottom: 10px;
		}
		.img-wrap table tr td{
			padding: 0 0 5px 0 !important;
		}
		.img-box .img-div{
			height: 120px;
			width: 120px;
			background-color: white;
			text-align: center;
			display: inline-block;
			margin-right:5px;
			box-sizing: border-box;
			white-space: nowrap;
		}
		.img-box img {
			/* width: 100%;
			height: 100%;
			object-fit: cover; */
			/* width: 100%;
    		height: auto; */
			max-width:120px !important;
			width:auto !important;
			height:120px !important;
			display: inline-block;
		}
		.signature .signature-box:first-child {
			margin-right: 15px !important;
		}
		.signature .signature-box:nth-child(2) {
			margin-left: 15px !important;
		}
		body {
            font-family: 'Conv_Arial', Sans-Serif;
        }

		@font-face {
            font-family: 'Conv_Arial';
            src: url('public/fonts/arial/Arial.eot');
            src: local('☺'), url('public/fonts/arial/Arial.woff') format('woff'), url('public/fonts/arial/Arial.ttf') format('truetype'), url('public/fonts/arial/Arial.svg') format('svg');
            font-weight: normal;
            font-style: normal;
        }

        .check:before {
            padding-bottom: 5px;
            margin-bottom: 5px;
            background-color: red;
            display: inline-block;
            visibility: visible;
            position: relative;
            border-color: red !important;
            color: white;
            font-size: 0.79em;
        }
  
        .check:checked:before {
            border: red !important;
            content: '✔';
            height: 10px;
            width: 15px;
            text-align: center;
            display: inline-block;
            font-size: 0.79em;
        }
	</style>
</head>

<body>
	<div class="main-wrapper">
		<div class="company-name text-black mb-10">{{ $company_name }}</div>
		<div class="clearfix"></div>

		<div class="mb-10 report-delivery">
            <span class="text-black f-14">Pre Move Comments - {{ $title }}</span>
        </div>
		<div class="clearfix"></div>

		<div class="company-name text-black mb-10 f-14">
			<div>
				<span><b>Origin</b> : {{$origin_agent}}</span>
			</div>
			<div>
				<span><b>Destination</b> : {{$delivery_agent}}</span>
			</div>
			<div style="margin-top: 5px !important;">
				<span><b>Container Number</b> : {{$container_number}}</span>
			</div>
        </div>
		<div class="clearfix"></div>
		<div class="mb-10 checklist-box">
			<div class="checklist-title mb-10">Pre Move Checklist - {{ ucfirst($move_type) }}:</div>
			@if($termsAndConditions)
			<div class="mb-10 checbox-list">
				@foreach($termsAndConditions as $termsAndCondition)
					<div>
						<input type="checkbox" name="tnc{{ $termsAndCondition['id'] }}" @if(in_array($termsAndCondition['id'], $conditionCheck)) checked @endif>
						<label for="list1">{{ $termsAndCondition['terms_and_conditions'] }}</label>
					</div>
				@endforeach
			</div>
			@endif

			@if($comment)
			@if($comment->comment)
			<div class="mb-10 black-box">
                @php
                    echo nl2br($comment->comment);
                @endphp
			</div>
			@endif
			@endif

			<div class="clearfix"></div>
			<div class="signature">
				<div class="signature-box">
					<div class="client-name mb-10" style="margin-right: 15px !important;">Client/Agent : <span>{{ $packageSignature ? $packageSignature->client_name ? $packageSignature->client_name : 'N/A' : 'N/A' }}</span></div>
					<div class="mb-10 client-signature" style="margin-right: 15px !important; width: 90% !important">
						<img src="{{ $packageSignature ? $packageSignature->client_signature : asset('storage/image/company-admin/signature/signature-default.svg') }}">
					</div>
					<div class="client-date" style="height: 18px;" style="margin-right: 15px !important;">
						{{ $packageSignature ? date_format($packageSignature->created_at,'D d M Y') : '' }}
					</div>
				</div>
				<div class="signature-box">
					<div class="client-name mb-10">Removalist : <span>{{ $packageSignature ? $packageSignature->employee_name ? $packageSignature->employee_name : 'N/A' : 'N/A' }}</span></div>
					<div class="mb-10 client-signature" style="width: 94% !important">
						<img src="{{ $packageSignature ? $packageSignature->employee_signature : asset('storage/image/company-admin/signature/signature-default.svg') }}">
					</div>
					<div class="client-date" style="height: 18px;">
						{{ $packageSignature ? date_format($packageSignature->created_at,'D d M Y') : '' }}
					</div>
				</div>
			</div>
			
		</div>
	</div>
</body>

</html>
