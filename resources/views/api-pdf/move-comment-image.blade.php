<!DOCTYPE html>
<html>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
    <title>{{ $move_name }}</title>

    <style type="text/css">
        .company-name {
            font-size: 25px;
            padding: 10px 10px;
            text-align: left;
        }
        .report-delivery {
            padding: 7px 0px;
            border: 1px solid #000;
            font-size: 20px;
            background-color: #d5d5d5;
            display: inline-block;
            width: 100%;
        }
        .f-14 {
            font-size: 15px !important;
        }
        .text-black {
            color: #000;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .img-wrap {
            width: 100%;
            margin-bottom: 10px;
        }

        .img-wrap table tr td {
            padding: 0 0 5px 0 !important;
        }

        .img-box .img-div {
            height: 120px;
            width: 120px;
            background-color: white;
            text-align: center;
            display: inline-block;
            margin-right: 5px;
            box-sizing: border-box;
            white-space: nowrap;
        }

        .img-box img {
            /* width: 100%;
            height: 100%;
            object-fit: cover; */
            /* width: 100%;
            height: auto; */
            max-width: 120px !important;
            width: auto !important;
            height: 120px !important;
            display: inline-block;
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

        .company-name div {
            margin-top: 5px !important;
            font-size: 13px !important;
        }
    </style>
</head>

<body>
    <script type="text/php">
        if ( isset($pdf) ) {
		$x = 450;
		$y = 88;
		$text = "Page {PAGE_NUM} of {PAGE_COUNT} Pages";
		$font = $fontMetrics->get_font("arial");
		$size = 11.5;
		$color = array(0,0,0);
		$word_space = 0.0;  //  default
		$char_space = 0.0;  //  default
		$angle = 0.0;   //  default
		$pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
	}
</script>
    <div class="main-wrapper">
        <div class="company-name text-black mb-10">{{ $company_name }}</div>
        <div class="clearfix"></div>
        <div class="mb-10 report-delivery">
            @php
                $title_text = '';
                if($comment_type == 0) {
                    $title_text = 'Pre Move Images';
                } elseif($comment_type == 1) {
                    $title_text = 'Post Move Images';
                }
            @endphp
            <span class="text-black f-14">{{ $title_text }} - {{ $title }}</span>
        </div>
        <div class="clearfix"></div>
        <div class="company-name text-black mb-10 f-14">
            <div>
                <span><b>Origin</b> : {{ $origin_agent }}</span>
            </div>
            <div>
                <span><b>Destination</b> : {{ $delivery_agent }}</span>
            </div>
            <div style="margin-bottom: 5px !important;">
                <span><b>Container Number</b> : {{ $container_number }}</span>
            </div>
        </div>

        <div class="clearfix"></div>

       <div class="img-wrap">
				<table class="border-0" style="padding-top: 30px !important; margin-top: 10px">
					@php $count = 0; @endphp
					{{-- @foreach($images as $image) --}}
						@foreach($images as $image_arr)
							@php $count++; @endphp
							@if($count == 1)
								<tr>
									<td class="img-box">
										<div style="width:100%;">
							@endif
											<div class="img-div">
												<img src="{{ $image_arr->image }}" style="">
											</div>
							@if($count == 5)
										@php $count = 0; @endphp
										</div>
									</td>
								</tr>
							@endif
						@endforeach
					{{-- @endforeach --}}
				</table>
			</div>
    </div>
    </div>
</body>

</html>
