<!DOCTYPE html>
<html>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kika ICR PDF Format - {{ Request::segment(1) }}</title>
    <style>
        body {
            font-family: customarialfont;
        }
        .company-name {
            font-size: 35px;
            padding: 5px 10px;
            border: 1px solid #000;
        }

        .text-black {
            color: #000;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .report-delivery {
            padding: 0px 5px;
			border: 1px solid #000;
			font-size: 15;
            background-color: #d5d5d5;
            display: inline-block;
            width: 100%;
        }

        /* .report-delivery tr td {
            font-size: 20px;
        } */

        .report-delivery tr td:last-child {
            text-align: right;
            /* font-size: 16px; */
        }

        .report-delivery span {
            padding: 0 10px;
            font-size: 20px;
        }

        .text-gray {
            color: #b1b1b1;
        }

        .customer-address {
            padding: 5px 10px;
            border: 1px solid #000;
            font-size: 16px;
            display: inline-block;
            width: 100%;
        }

        .address-up {
            display: inline-block;
            width: 100%;
        }

        .address-up table tr td {
            font-weight: bold;
        }

        .address-up table tr td span {
            font-weight: normal;
        }

        .table-header {
            border: 1px solid #000 !important;
            border-style: inset;
            /* border-bottom: 0px; */
            font-size: 14px;
            background-color: #d5d5d5;
            padding: 5px 10px;
            margin: 0 0.5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table,
        th,
        td {
            border: 1px solid #000;
            color: #000;
        }

        table.border-0,
        .border-0 th,
        .border-0 td {
            border: none;
        }

        th,
        td {
            padding: 5px;
            font-size: 14px;
        }

        .exception-symbols-table tr td:nth-child(odd),
        .descriptive-symbols-table tr td:nth-child(odd),
        .location-symbols-table tr td:nth-child(odd) {
            text-align: center;
            width: 50px;
        }

        .discription-location-both {
            display: table;
            width: 100%;
        }

        .item-condition-table thead {
            background-color: #d5d5d5;
        }

        .item-condition-table thead tr th,
        .item-condition-table tbody tr td {
            font-size: 16px;
            font-weight: normal;
        }

        .item-condition-table tbody tr td:nth-child(1),
        .item-condition-table tbody tr td:nth-child(3),
        .item-condition-table tbody tr td:last-child {
            text-align: center;
        }

        .checklist-box {
            border: 1px solid #000;
            padding: 10px;
            width: 100%;
            display: inline-block;
            box-sizing: border-box;
        }

        .checklist-title,
        .left-packed {
            font-size: 18px;
        }

        .left-packed {
            border-bottom: 1px solid #000;
            display: table;
            width: 100%;
        }

        .left-packed div {
            padding: 5px 0;
            width: 49%;
            display: table-cell;
        }

        .left-packed div:last-child {
            text-align: right;
        }

        .red-box {
            padding: 5px 10px;
            border: 1px solid red;
            box-sizing: border-box;
        }

        .black-box {
            padding: 5px 10px;
            border: 1px solid #000;
            text-align: left;
            margin-top: 10px;
        }

        .checbox-list div {
            font-size: 16px;
        }

        .signature {
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
        .address-up table td {
            text-align: left !important;
        }

        .descriptive-symbols-table tbody tr td {
            font-size: 13px !important;
        }

        .discription-location-both .location-inner {
            margin-right: 8px !important;
        }

        .location-symbols-table tr td:nth-child(odd) {
            width: 30px !important;
        }

        .report-delivery tr td:last-child {
            text-align: left;
        }

        .item-condition-table tbody tr td:nth-child(1),
        .item-condition-table tbody tr td:nth-child(3),
        .item-condition-table tbody tr td:last-child {
            text-align: center;
            width: 30px;
        }

        .checbox-list div input[type="checkbox"] {
            display: inline-block;
            width: 15px;
            margin-top: 3px;
            margin-right: 5px;
        }

        .checbox-list div label {
            display: inline-block;
            width: 95%;
            padding-bottom: 2px;
        }

        .item-condition-table tbody tr td:nth-child(2) {
            width: 370px;
        }

        td {
            border: 1px solid #ddd;
        }

        table.item-condition-table thead {
            border-bottom: 1px solid #000;
        }

        table.item-condition-table thead tr th {
            border-right: 1px solid #000;
        }

        .exception-symbols-table tbody tr td {
            border-left: 1px solid #000;
        }

        .exception-symbols-table tbody tr td:last-child {
            border-right: 1px solid #000;
        }

        .exception-symbols-table tbody tr:last-child td {
            border-bottom: 1px solid #000;
        }

        .exception-symbols-table tbody tr:first-child td {
            border-top: 1px solid #000;
        }

        .descriptive-symbols-table tr td:first-child,
        .location-symbols-table tbody tr:last-child {
            border-left: 1px solid #000 !important;
        }

        .descriptive-symbols-table tr td:last-child {
            border-right: 1px solid #000;
        }

        .descriptive-symbols-table tr:last-child {
            border-bottom: 1px solid #000;
        }

        table.item-condition-table tbody tr td {
            border-left: 1px solid #000;
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

        .descriptive-symbols-table tbody tr:first-child td,
        .location-symbols-table tbody tr:first-child td {
            border-top: 1px solid #000;
        }

        .location-symbols-table tbody tr td {
            font-size: 13px !important;
        }

        .f-16 {
            font-size: 16px !important;
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

        .text-right {
            text-align: right !important;
        }

        .f-14 {
            font-size: 15px !important;
        }

        .border-part.new-condition .item-condition-table tbody tr td {
            font-size: 12px !important;
            padding: 2px !important;
        }

        .border-part.new-condition .item-condition-table tbody tr td:nth-child(1) {
            border-right: none !important;
        }

        .border-part.new-condition .item-condition-table tbody tr td:nth-child(2) {
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

        .checklist-title,
        .left-packed {
            font-size: 14px !important;
            padding: 2px 0px !important;
        }

        .description-locations-infos .table-header {
            font-size: 11px !important;
            padding: 2px 8px !important;
        }

        .checklist-box .checklist-title {
            font-size: 14px !important;
            padding: 0px 8px 2px 0px !important;
        }

        .main-wrapper .customer-address .address-up table tr td {
            padding: 3px 5px !important;
        }

        .mb_2 {
            padding-bottom: 5px;
        }

        body.pdf-part {
            font-family: 'Conv_Arial', Sans-Serif;
        }

        @page {
            margin-top: 150px;
        }

        #header {
            position: fixed;
            left: 0px;
            top: -105px;
            right: 0px;
            margin-bottom: 100px;
        }

        .sheet-data table tr.main-text td.count-text span {
            font-weight: 400;
        }

        .sheet-data table tr.main-text td.count-text span {
            width: 11%;
        }

        .sheet-data table {
            border-spacing: -1px;
            border-collapse: inherit;
            border: 0px;
            margin: 0 0 10px 0;
        }

        td.count-text {
            border: 1px solid #000;
            overflow: hidden !important;
            text-align: center;
        }

        .sheet-data {
            width: 100%;
            display: inline-block;
            vertical-align: top;
            /* margin-top: 20px !important; */
        }

        .sheet-data table {
            padding: 0;
        }

        .sheet-content {
            width: 100%;
            font-size: 14px;
            border: solid 1px #000;
            padding: 10px;
            margin: 10px 0;
            min-height: 200px;
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
                            <td class="text-black"><b>Origin : </b><span>{{ $move->uplift_address }}</span></td>
                        </tr>
<!--                        <tr>
                            <td class="text-black" colspan="2">Phone :
                                <span>{{ $move->move->contact->contact_number }}</span></td>
                        </tr>-->
                        <tr>
                            <td class="text-black" colspan="2"><b>Destination : </b>
                                <span>{{ $move->move->delivery ? $move->move->delivery->delivery_address : "" }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-black" colspan="2"><b>Container Number : </b><span>{{ $move->move->container_number }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="sheet-data">
            <table style="table-layout:fixed;">
                <tbody>
                    @php $count = 0; @endphp
                    @foreach($move_items as $key=> $move_item)

                        @php $count++; @endphp
                        @if($count == 1)
                            <tr class="main-text" style="margin-top: 40px;">
                        @endif
                        <td class="count-text"
                            style="background:{{$move_item->is_delivered == 1 ? '#d5d5d5' : '#ffffff'}}; width:8%;">
                            <span>{{ $move_item->item_number }}</span>
                        </td>
                        @if($count == 15)
                            @php $count = 0; @endphp
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="clearfix"></div>
        <div class="sheet-content">
        <?php

            $str = "";

            if(!empty($move_conditions))
            {
                $is_first = true;
                foreach($move_conditions as $item_number =>$move_cond)
                {

                    // echo "<pre>";
                    // print_r($move_conditions);
                    // exit;
                    // print item number with . (dot)
                    $no = $item_number + 1;
                    if(!empty($move_cond))
                    {
                    	$str .= $is_first ? $no."." : " ". $no.".";
                    }
                     if(!empty($move_cond))
                     {
                        $str .= " ";
                        foreach($move_cond as $condition =>$condition_sides)
                        {
                            foreach($condition_sides as $sub_con => $sub_condition_side)
                            {
                                $str .= $sub_con;
                                foreach($sub_condition_side as $key_con => $key_sub_con) {

                                    if(!empty($key_sub_con))
                                    {
                                        $str .= ", ".$key_sub_con;
                                    }
                                }
                                $str .= " - ";
                            }
                            //  $str .= $condition;
                            //  if(!empty($condition_sides))
                            //  $str .= ", ".join(",",$condition_sides);
                            //  $str .= " - ";
                        }
                        $str = substr($str, 0, -3) . '.';
                        $replc = str_replace('- ,', ',', $str);
                        $str = $replc;
                    }
                    // exit;
                    $is_first = false;
                }
            }
            if($str != "") {
                echo $str;
            }
            ?>
            <br>
        </div>

        @if($move_type == 5)
        <div class="clearfix"></div>
        <div class="mb-10 checklist-box">
            <div class="checklist-title mb-10">Post Move Checklist - Delivery</div>
            <div class="mb-10 left-packed" style="width: 100%">
                <div style="width: 49%; float: left">Cartons Left Packed :
                    <b>{{ $move->move->delivery->lp_carton == '0' ? '0' : ($move->move->delivery->lp_carton ? $move->move->delivery->lp_carton : '') }}</b>
                </div>
                <div style="width: 50%; float: right; text-align: right;">Packages Left Packed :
                    <b>{{ $move->move->delivery->lp_package == '0' ? '0' : ($move->move->delivery->lp_package ? $move->move->delivery->lp_package : '') }}</b>
                </div>
            </div>
            <div class="mb-10 checbox-list">
                @foreach($termsAndConditions as $termsAndCondition)
                @if($termsAndCondition->move_type == 5 && $termsAndCondition->move_status == 1)
                <div style="margin-top: 5px">
                    @if(in_array($termsAndCondition->id,$checked['postDeliveryChecked']))
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
                @if($comment->move_type == 5 && $comment->move_status == 1)
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
                <div style="width: 44%; float: left">
                    <div class="mb-10"
                        style="width:100%; margin-top: 7px; border: 1px solid #000; padding: 5px;box-sizing: border-box ">
                        Client/Agent :
                        <span>{{ isset($packageSignature['post_delivery']) ? $packageSignature['post_delivery']->client_name : '' }}</span>
                    </div>
                    <div class="mb-10"
                        style="width:100%; border: 1px solid #000; height: 80px; padding: 5px;box-sizing: border-box">
                        <img style="width:250px; object-fit:contain; height: 80px;"
                            src="{{ isset($packageSignature['post_delivery']) ? $packageSignature['post_delivery']->client_signature : '' }}">
                    </div>
                    <div
                        style="width:100%; border: 1px solid #000; padding: 5px; height: 18px;box-sizing: border-box">
                        {{ isset($packageSignature['post_delivery']) ? date_format($packageSignature['post_delivery']->created_at,'D d M Y') : '' }}
                    </div>
                </div>
                <div style="width: 5%; float: left"></div>
                <div style="width: 45%; float: right">
                
                    <div class="mb-10"
                        style="width:100%; margin-top: 7px; border: 1px solid #000; padding: 5px;box-sizing: border-box">
                        Removalist :
                        <span>{{ isset($packageSignature['post_delivery']) ? $packageSignature['post_delivery']->employee_name : '' }}</span>
                    </div>
                    <div class="mb-10"
                        style="width:100%; border: 1px solid #000; height: 80px; padding: 5px;box-sizing: border-box">
                        <img style="width:250px; object-fit:contain; height: 80px;"
                            src="{{ isset($packageSignature['post_delivery']) ? $packageSignature['post_delivery']->employee_signature : '' }}">
                    </div>
                    <div
                        style="width:100%; border: 1px solid #000; padding: 5px; height: 18px;box-sizing: border-box">
                        {{ isset($packageSignature['post_delivery']) ? date_format($packageSignature['post_delivery']->created_at,'D d M Y') : '' }}
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="clearfix"></div>
    </div>
</body>

</html>
