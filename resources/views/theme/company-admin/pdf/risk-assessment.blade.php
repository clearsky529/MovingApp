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
        table tr th{
            background-color: #d5d5d5;
            text-align: left;
            border-bottom:  2px;
        }
        .f-14 {
            font-size: 15px !important;
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
	</style>
</head>

<body>
	<div class="main-wrapper">
		<div class="company-name text-black mb-10">{{ $move_agent }}</div>
		<div class="clearfix"></div>
        
        <div class="mb-10 report-delivery">
            <span class="text-black f-14">Site Safety Risk Assessment - {{ $title }}</span>
        </div>
		<div class="clearfix"></div>

        <div class="company-name text-black mb-10 f-14">
            <span>Team Leader: {{ $risk_assessment['team_leader'] }}</span>
            <span style="float: right">{{ date_format($risk_assessment['created_at'],'d M Y') }}</span>
        </div>
		<div class="clearfix"></div>

        <div class="mb-10 f-14">
            <table>
                <tr>
                    <th>Hazard</th>
                    <th>Risk</th>
                </tr>
                @php
                $risk_assessment_details = $risk_assessment->riskAssessmentDetail->toArray();
                @endphp
                
                @foreach($risk_assessment_details as $row)
                <tr>
                    @php
                        $risk_title_id = array_column($risk_title,'id');
                        $risk_title_key = array_search($row['risk_title_id'],$risk_title_id);
                        $priority = '';
                            switch ($row['risk_priority']) {
                                case '1':
                                    $priority = "Low";
                                    break;
                                case '2':
                                    $priority = "Medium";
                                    break;
                                case '3':
                                    $priority = "High";
                                    break;
                            }
                    @endphp
                    {{-- <td>{{ $row->risk_title }}</td> --}}
                    <td>{{$risk_title[$risk_title_key]['risk_title']}}</td>
                    <td>{{$priority}}</td>
                </tr>
                @endforeach
            </table>
        </div>
		<div class="clearfix"></div>

        <div class="company-name text-black mb-10 f-14">If a risk is considered Medium or High ensure that either the customer controls the risk and/or {{ $move_agent }} has considered the risk and applied safety controls.</div>
		<div class="clearfix"></div>

        @if($risk_assessment->risk_comment)
            <div class="company-name text-black mb-10 f-14">{{ $risk_assessment->risk_comment }}</div>
            <div class="clearfix"></div>
        @endif
		
	</div>
</body>

</html>
