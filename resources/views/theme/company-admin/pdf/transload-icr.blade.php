<!DOCTYPE html>
<html>
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Bingo Sheet</title>
<style type="text/css">
.text-center{
	text-align: center;
}
.list-instyled .checked-by {
	margin-top: 10px;
}
.list-instyled{
	margin: 0;
	padding: 0;
}
.list-instyled li{
	list-style: none;
	font-size: 14px;
	margin: 5px 0;
}
.sheet-header,
.sheet-wrapper{
	width: 100%;
	display: inline-block;
	vertical-align: top;
}
.sheet-header{
	background-color: #ffffff;
	border: 1px solid #000;
	padding: 10px;
	margin-top: 15px;
}
.sheet-data{
	width: 100%;
	display: inline-block;
	vertical-align: top;
	/* margin-bottom: 5px; */
	margin-top: 20px !important;
}
.sheet-data table,
.img-wrap table{
	padding: 0;
}
.img-wrap table{
	margin-top: 10px;
}
.img-wrap table tr td:first-child{
	padding-left: 0;
}
.img-wrap table tr td:last-child{
	padding-right: 0;
}
.sheet-wrapper {
	padding-left:15px;
	padding-right:15px;
}
.sheet-left{
	width: 59.5%;
	text-align: left;
	display: inline-block;
	vertical-align: top;
}
.sheet-right{
	width: 39.5%;
	text-align: right;
	display: inline-block;
	vertical-align: top;
}
table, th, td {
	border: solid 1px #ddd;
	padding: 10px;
	text-align: center;
	font-size: 14px;
	font-weight: bold;
}
table {
	border-collapse:collapse;
	width: 100%;
	margin: 20px 0px;
}
.sheet-data table tr.main-text td.count-text span{
	font-weight: 400;
}
.sheet-data table tr.main-text td.count-text span {
	width: 11%;
}
.sheet-data table{
	border-spacing: -1px;
	border-collapse: inherit;
	border: 0px;
}
td.count-text {
	border:1px solid #000;
	overflow: hidden !important;
}
.sheet-clarify {
	width: 100%;
	display: inline-block;
	margin: 0 0 0 0;
	padding:0;
	height:75px;
}
.sheet-clarify .box {
	width: 19%;
	padding: 0 5px;
	text-align: center;
	font-size: 14px;
	display: inline-block;
	margin-top:10px;
	margin-bottom:0 !important;
	/*float:left;*/

}
.sheet-clarify .box p {
	margin: 0 0 5px 0;
	padding: 10px;
	border: 1px solid #ddd;
}
.sheet-content {
	width: 100%;
	font-size: 14px;
    border: solid 1px #000;
    padding: 10px;
    /*box-sizing: border-box;*/
    min-height: 200px;
}
.sheet-content p{
	margin: 5px 0;
}
.blue {
    background: #56c1ff;
}
.green {
    background: #88fa4e;
}
.yellow {
    background: #fff056;
}
.pink {
    background: #ff95ca;
}
.aqua {
    background: #73fdea;
}
.sheet-left ul li,
.sheet-right ul li{
	margin: 0;
}
.location-box {
    border: solid 1px #000 !important;
    padding: 3px 10px !important;
    text-align: center;
    font-size: 14px;
	line-height: 20px;
    font-weight: 400;
    margin: 0 2px 2px 0 !important;
	white-space: nowrap;
    /* display: inline-block !important; */
	/* height: 28px; */
  	/* width: auto !important;  */
}
.red-box {
	border: 1px solid #000;
}
.img-wrap {
    width: 100%;
    margin-bottom: 10px;
}
.flex-container{
	display: inline;
}
.img-box {
    width: 117.5px;
    height: 117.5px;
    text-align: left;
}
/*.img-box img {
    width: 111px;
    height: 111px;
    object-fit: cover;
}
*/.img-box img {
    /*width: 117.5px;
    height: 117.5px;
    object-fit: cover;*/
    width: 100%;
    height: auto;
}
table.border-0,
.border-0 th,
.border-0 td{
    border: none;
}
tbody tr td.number-part {
	border: 1px solid #000;
	font-weight:400;
	width:15px !important;
	padding: 10px 5px !important;
}
.h1-title {
	/* margin-top:40px; */
	/* text-align: center; */
	font-size: 20px;
}
.company-name {
	background-color: #d5d5d5;
	border: 1px solid #000;
	padding: 10px;
}
.sheet-right{
	position: relative;
}
.sheet-right ul li:nth-child(2){
	margin-top: 10px;
}
.date-div {
	background-color: #d5d5d5;
	border: 1px solid #000;
	padding: 0 10px;
	display: inline-block;
	right: 0;
	position: absolute;
	top: 0;
	font-size: 12px;
	/* transform: translate(130px, 0); */
	/*width: 100%;
	float: right; */
}
.page-no {
	visibility: hidden;
	height:10px;
	font-size: 14px;
}
@font-face {
	font-family: 'Conv_Arial';
	src: url('fonts/Arial.eot');
	src: local('☺'), url('fonts/Arial.woff') format('woff'), url('fonts/Arial.ttf') format('truetype'), url('fonts/Arial.svg') format('svg');
	font-weight: normal;
	font-style: normal;
}
body.pdf-part {
	font-family:'Conv_Arial',Sans-Serif;
}

/* @page { margin-top:150px;}
#header { position: fixed; left: 0px; top: -165px; right: 0px;margin-bottom:100px; } */

</style>
</head>
<body class="pdf-part">
<script type="text/php">
	if ( isset($pdf) ) {
		$x = 485;
		$y = 45;
		$text = "Page {PAGE_NUM} of {PAGE_COUNT}";
		$font = $fontMetrics->get_font("arial");
		$size = 11;
		$color = array(0,0,0);
		$word_space = 0.0;  //  default
		$char_space = 0.0;  //  default
		$angle = 0.0;   //  default
		$pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
	}
</script>
<div class="sheet-wrapper">
  <div id="header">
	<div class="main-wrapper">
		<div class="company-name text-black mb-10 h1-title">{{$move->move->contact->contact_name}} : {{ $move->move->move_number}} - Tranship Sheet</div>
		<div class="sheet-header" style="width: 97% !important">
			<div class="sheet-left">
				<ul class="list-instyled">
					<!-- <li> {{$move->move->contact->contact_name}} - {{ $move->move->move_number}}</li>
					<li>
						@if($move && $move->movement == 0)
							Into store
						@elseif($move && $move->movement == 1)
							Out of store
						@else
							N/A
						@endif
					</li> -->
					<li>
						@foreach($move_containers as $move_container)
							<span class="location-box" style="background:{{isset( $move_container->color_code) ? $move_container->color_code : '' }}">{{$move_container->container_number}}
							@if($move_container->Category)
								{{  '-'  }}
							@endif

							<?php
								if($cat_name = isset($move_container->Category->category_name) ? $move_container->Category->category_name : '')
								{
								$words = explode(" ", $cat_name);
								$acronym = "";

								foreach ($words as $w) {
								  $acronym .= $w[0];
								}
							}
							else{
								$acronym = '';
							}
							?>
							{{$acronym}}

							</span>
						@endforeach
					</li>
				</ul>
			</div>
			<div class="sheet-right">
				<ul class="list-instyled">
					<li class="date-div">{{ date_format($move->move->created_at,'d M Y') }} : <b>@if($move && $move->movement == 0)
							IN
						@elseif($move && $move->movement == 1)
							OUT
						@else
							N/A
						@endif</b></li>
					<li><span class="page-no">dsd</span></li>
					<!-- <li>
						{{isset($move->move->transhipPrePackageSignature->employee_name) ? $move->move->transhipPrePackageSignature->employee_name : 'N/A'}}</li> -->
					<li class="checked-by"><b>Checked By :</b> {{isset($move->move->transhipPostPackageSignature->employee_name) ? $move->move->transhipPostPackageSignature->employee_name : 'N/A'}} </li>
					<li><b>Staff :</b> {{isset($move->move->transhipPrePackageSignature->employee_name) ? $move->move->transhipPrePackageSignature->employee_name : 'N/A'}}</li>
				</ul>
			</div>
		</div>
	</div>
  </div>


	<div class="clearfix"></div>
	<div class="sheet-data">
		<table  style="table-layout:fixed;">
			<tbody>
			@php $count = 0; @endphp
            @foreach($move_items as $key=> $move_item)

            @php $count++; @endphp
			@if($count == 1)
            	<tr class="main-text" style="margin-top: 40px;">
            @endif
	              	<td class="count-text" style="background:{{$move_item->container ? $move_item->container->containerDetails->color_code : '' }}; width:8%;">
	              		<span>{{ $move_item->item_number }}</span>
	              	</td>
              		@if($count == 20)
					@php $count = 0; @endphp
              	</tr>
              		@endif
			@endforeach
			</tbody>
		</table>
	</div>
	<div class="clearfix"></div>
	<div class="sheet-content" style="width: 97% !important">
		<?php
		$mainString = '';
		$idArr = array_values(array_unique($idArr));
		// $num =  implode(',',$idArr);
		// echo "<pre>";
		// print_r($idArr[0]);
		// die;
		$j=0;
		foreach($move_conditions as $item_number => $move_cond){
			// echo "<pre>";
			// print_r($idArr[$item_number]);

			// $item_number = $item_number + 1;

			foreach($move_cond as $parentKey =>$value){
				if(!empty($value)){
					if($mainString != ''){
						$mainString .= " ";
					}
			        $val = $idArr[$j];
					$mainString = !is_numeric($parentKey) ? $mainString  .= $val . ". " .$parentKey : $mainString  .= $val . ". ";
					$conditionString = '';
					$is_first = true;
					// echo "<pre>";
					// 		print_r($value);
					foreach($value as $subKey =>$subValue){$isNumber = is_numeric($parentKey) ? true : false;

						foreach($subValue as $condition =>$subConditionValue){
								// echo "<pre>";
								// print_r($condition);
								$dot = 0;
								if(Str::contains($condition, ','))
								{
									$dot = 1;
								}
								if($isNumber){
									if($dot){
										$conditionString = !is_numeric($condition) ? $is_first ? $conditionString  .= $condition : $conditionString  .= " : ".$condition : '';
									}else{
										$conditionString = !is_numeric($condition) ? $is_first ? $conditionString  .= $condition : $conditionString  .= " - ".$condition : '';
									}
								}else{
									// echo $dot;
									// if($dot == 0){
									// 	$conditionString = !is_numeric($condition) ? $is_first ? $conditionString  .= $condition : $conditionString  .= "  ".$condition : '';
									// }else{
									// 	$conditionString = !is_numeric($condition) ? $is_first ? $conditionString  .= $condition : $conditionString  .= " - ".$condition : '';
									// }
									$conditionString = !is_numeric($condition) ? $is_first ? $conditionString  .= " : ".$condition : $conditionString  .= " - ".$condition : '';
								}

								$conditionArr = !is_numeric($condition) ? $subConditionValue : $subValue;

							if(!empty($conditionArr))
							$conditionString .= ", ".join(", ",$conditionArr);

							$is_first = false;
						}
					}
					// die;
					$mainString .= $conditionString.". ";
				}
			}
			$j++;
		}
		// die;
		if($mainString != "") {
			echo $mainString;
		}
		?>

		<div>
			<!-- <br> -->
			@foreach($comments as $key => $comment)
                @php
                    echo nl2br($comment->comment);
                @endphp
			@endforeach
		</div>
	</div>


	<div class="clearfix"></div>
	<div class="img-wrap">
		<table class="border-0">
				@php $count = 0; @endphp
				@foreach($condition_images as $condition_image_array)
				@foreach($condition_image_array->conditionImage as $condition_image)
				@php $count++; @endphp
				@if($count == 1)
					<tr>
				@endif
					<td class="img-box">
						<img src="{{ $condition_image->image }}">
					</td>
				@if($count == 5)
				@php $count = 0; @endphp
					</tr>
				@endif
				@endforeach
				@endforeach
		</table>

	</div>
	<div class="clearfix"></div>
</div>

</body>
</html>
