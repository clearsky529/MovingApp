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
			font-size: 25px;
			padding: 5px 10px;
			text-align: center;
		}
		.text-black{
			color: #000;
		}
		.mb-10{
			margin-bottom: 10px;
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
		.checklist-box{
			width: 100%;
			display: inline-block;
			box-sizing: border-box;
		}
		.checklist-title{
			font-size: 14px;
		}
		.red-box{
			padding: 10px 10px;
			border: 1px solid red;
			text-align: center;
			line-height: 10px;
			margin-top: 5px;
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

		.black-box1{
			padding: 5px 10px;
			border: 1px solid #000;
			text-align: center;
      overflow:hidden;
      word-wrap: break-word;
      word-break: break-all;
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
		.img-box {
			width: 120px;
			height: 120px;
		}
		.img-box img {
			/* width: 100%;
			height: 100%;
			object-fit: cover; */
			width: 17%;
    		height: auto;
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
		<div class="company-name text-black mb-10">{{ $title }}</div>
		<div class="clearfix"></div>
		<div class="mb-10 checklist-box" style="width: 97%">
			<div class="checklist-title mb-10">The Client/Authorised Agent certify that for this {{ $move_type }}:</div>
			<div class="mb-10 checbox-list">
				@foreach($termsAndConditions as $termsAndCondition)

				@if($termsAndCondition->id == 18)
				<div class="text-black " style="text-align: center; font-size: 15px;">   <b > @php
					echo nl2br ('All cartons/packages marked as PBR are to be unpacked by the removalist unless elected otherwise by the client/agent or not included as part of the delivery service.') @endphp</b> </div>
					<div class="red-box" style="text-align: center; ">
						{{-- @php
						var_dump($conditionCheck);
					@endphp --}}

						<input type="checkbox"   name="tnc{{ $termsAndCondition->id }}"   @if(in_array($termsAndCondition->id, $conditionCheck)) class="check" checked   style="vertical-align: sub;color:red; text-align: center; margin-left:26px;" @else  style="vertical-align: middle;color:red;  margin-left:26px;" @endif id="list1">
						<label for="list1" style="width: auto !important"><b  style="font-size: 13px;">{{ $termsAndCondition->terms_and_conditions }}</b></label>
					</div>
				@else
					<div>

						<input type="checkbox" name="tnc{{ $termsAndCondition->id }}" @if(in_array($termsAndCondition->id, $conditionCheck)) checked @endif>
						<label for="list1">{{ $termsAndCondition->terms_and_conditions }}</label>
					</div>
				@endif
				@endforeach
			</div>



			@php
			$pre_comment = '';
			$comment_text = '';
			if($comment) {
				if($move_type == "uplift" && $comment->move_status == 1 && $comment->pre_comment) {
					$pre_comment = $comment->pre_comment . '<br>';
				}
				if($comment->comment) {
					$comment_text = $comment->comment;
				}
			}
			@endphp
			@if($comment)
			<div class="mb-10 black-box" style="display: {{ ($pre_comment != '' || $comment_text != '' ? 'block' : 'none') }}">
				@php
					echo nl2br($pre_comment);
					echo nl2br($comment_text);
                @endphp
			</div>
			@endif
			<div class="clearfix"></div>
			<div class="signature">
				<div class="signature-box">
					<div class="client-name mb-10" style="margin-right: 15px !important;">Client/Agent : <span>{{ $packageSignature ? $packageSignature->client_name ? $packageSignature->client_name : 'N/A' : 'N/A' }}</span>
					</div>
					<div class="mb-10 client-signature" style="margin-right: 15px !important; width: 90% !important">
						<img src="{{ $packageSignature ? $packageSignature->client_signature : asset('storage/image/company-admin/signature/signature-default.svg') }}">
					</div>
				</div>
				<div class="signature-box">
					<div class="client-name mb-10">Removalist : <span>{{ $packageSignature ? $packageSignature->employee_name ? $packageSignature->employee_name : 'N/A' : 'N/A' }}</span>
					</div>
					<div class="mb-10 client-signature" style="width: 94% !important">
						<img src="{{ $packageSignature ? $packageSignature->employee_signature : asset('storage/image/company-admin/signature/signature-default.svg') }}">
					</div>
				</div>
			</div>
			<div class="img-wrap">
				<table class="border-0" style="padding-top: 20px !important">
					@php $count = 0; @endphp
					@foreach($images as $image)
						@foreach($image->image as $image_arr)
							@php $count++; @endphp
							@if($count == 1)
								<tr>
									<td class="img-box">
							@endif
										<img src="{{ $image_arr->image }}" style="padding: 5px 15px 0px 0px !important">
							@if($count == 5)
										@php $count = 0; @endphp
									</td>
								</tr>
							@endif
						@endforeach
					@endforeach
				</table>
			</div>

			<div class="clearfix"></div>
		</div>
		<div class="clearfix"></div>
	</div>
</body>

</html>
