<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ ($move_type == 5 ? 'Delivery' : 'Uplift') }} Move ICR Images</title>
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
            font-size: 15px;
            background-color: #d5d5d5;
            display: inline-block;
            width: 100%;
        }
        .report-delivery tr td:last-child {
            text-align: right;
        }
        .report-delivery span {
            padding: 0 10px;
            font-size: 20px;
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
        td {
            border: 1px solid #ddd;
        }
        .exception-symbols-table tbody tr td:nth-child(1) {
            border-left: 1px solid #000;
        }
        .exception-symbols-table tbody tr:nth-child(1) td {
            border-top: 1px solid #000;
        }
        .descriptive-symbols-table tr td:nth-child(1) {
            border-left: 1px solid #000;
        }
        .descriptive-symbols-table tr:nth-child(1) td {
            border-top: 1px solid #000;
        }
        .location-symbols-table tr td:nth-child(1) {
            border-left: 1px solid #000;
        }
        .location-symbols-table tr:nth-child(1) td {
            border-top: 1px solid #000;
        }
        .descriptive-symbols-table tr td:last-child {
            border-right: 1px solid #000;
        }
        .descriptive-symbols-table tr:last-child {
            border-bottom: 1px solid #000;
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
        .descriptive-symbols-table tbody tr:first-child td,
        .location-symbols-table tbody tr:first-child td {
            border-top: 1px solid #000;
        }
        .location-symbols-table tbody tr td {
            font-size: 13px !important;
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
        .f-14 {
            font-size: 15px !important;
        }
        .description-locations-infos table tr td {
            font-size: 9px !important;
            padding: 2px !important;
        }
        .description-locations-infos .table-header {
            font-size: 11px !important;
            padding: 2px 8px !important;
        }
        .main-wrapper .customer-address .address-up table tr td {
            padding: 3px 5px !important;
        }
        body.pdf-part {
            font-family: 'Conv_Arial', Sans-Serif;
        }
        @page {
            margin-top: 150px;
        }
        .row {
            width: 100%;
            overflow: hidden;
            border: 1px solid #000;
            margin-bottom: 8px;
            padding: 12px 20px;
        }
        .col-40 {
            float: left;
            width: 40%;
        }
        .col-30 {
            float: left;
            width: 30%;
        }
        .condition_details_div div {
            font-size: 14px !important;
            line-height: 1.5;
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
                        <tr>
                            <td class="text-black" colspan="2">
                                <b>Destination :</b>
                                <span>{{ $move->move->delivery ? $move->move->delivery->delivery_address : '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-black" colspan="2">
                                <b>Container Number :</b><span>{{ $move->move->container_number }}</span>
                            </td>
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
                        @foreach ($exceptions as $key => $exception)
                            @php
                                $exception_break_count++;
                                $exception_count++;
                            @endphp
                            @if ($exception_break_count == 1)
                                <tr>
                            @endif
                            <td @if (count($exceptions) - 3 <= $key) style="border-bottom: 1px solid black" @endif>
                                {{ $exception->condition_code }}</td>
                            <td
                                style="border-right:($exception_break_count == 3 ? '1px solid black' : 'unset'); border-bottom: (count($exceptions)-3 <= $key ? '1px solid black' : 'unset');">
                                {{ $exception->condition }}</td>
                            @if ($exception_break_count == 3 || $exception_count == count($exceptions))
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
                        @foreach ($descriptions as $description)
                            @if (!in_array($description->id, [5, 6]))
                                <tr>
                                    <td style="border-bottom: ($description->id == 4 ? '1px solid #000000' : 'unset')">
                                        {{ $description->code }}</td>
                                    <td
                                        style="border-right: 1px solid black;border-bottom: ($description->id == 4 ? '1px solid #000000' : 'unset')">
                                        {{ $description->package_status }}</td>
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
                        @foreach ($conditionLocations as $key => $conditionLocation)
                            @php
                                $location_break_count++;
                                $location_count++;
                            @endphp
                            @if ($location_break_count == 1)
                                <tr>
                            @endif
                            <td @if (count($conditionLocations) - 3 <= $key) style="border-bottom: 1px solid black" @endif>
                                {{ $conditionLocation->side_code }}</td>
                            <td
                                style="border-right:($location_break_count == 3 ? '1px solid black' : 'unset'); border-bottom: (count($conditionLocations)-3 <= $key ? '1px solid black' : 'unset');">
                                {{ $conditionLocation->side }}</td>
                            @if ($location_break_count == 3 || $location_count == count($conditionLocations))
                                @php $location_break_count = 0; @endphp
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <?php
        $total_items = 1;
        $page_count = 0;
        ?>

        @foreach ($move_items as $move_item)
            @php
            $conditionData = $move_item->condition;
            if($move_type == 5) {
                $conditionData = $move_item->deliveryCondition;
            }
            @endphp
            @if (isset($conditionData) && !empty($conditionData))
                @php
                    $hasConditionImage = $conditionData->contains(function ($conditionData) {
                        return $conditionData->conditionImage->isNotEmpty();
                    });
                @endphp
                @if ($hasConditionImage)
                    @php
                        $conditionDetails = '';
                        foreach ($conditionData as $condition) {
                            $conditionDetails .= $condition->conditionDetails->condition_code . " - ";

                            foreach ($condition->conditionSides as $key => $conditionSide) {

                                if ($key == count($condition->conditionSides) - 1) {
                                    $conditionDetails .= $conditionSide->sideDetails->side_code;
                                } else {
                                    $conditionDetails .= $conditionSide->sideDetails->side_code . ", ";
                                }
                            }
                        }

                        if (substr($conditionDetails, -3) === " - ") {
                            $conditionDetails = substr($conditionDetails, 0, -3);
                        }
                    @endphp

                    @foreach ($conditionData as $condition)
                        @if ($condition->conditionImage()->exists())
                            @if(($total_items == 3 && $check_condition_image != 3) || ($page_count > 1 && $total_items == (5 + $page_count) && $check_condition_image != (5 + $page_count)))
                                <?php $page_count = $total_items; ?>
                                <div class="row" style="page-break-after: always;">
                            @else
                                <div class="row">
                            @endif
                                <div class="col-40 condition_details_div">
                                    @php
                                        $item_package = '';
                                        $item_description = '';
                                    @endphp
                                    @if(isset($move_item->itemUpliftCategory) && isset($move_item->itemUpliftCategory->cartoon_choice) && $move_item->itemUpliftCategory->cartoon_choice != '')
                                        <?php $item_package = $move_item->itemUpliftCategory->cartoon_choice; ?>
                                    @endif
                                    @if($move_item->item_id == 0 && $move_item->item != null)
                                        <?php $item_description = $move_item ? $move_item->item : ''; ?>
                                    @elseif($move_item->item_id == 0)
                                        <?php $data = ''; ?>
                                        @foreach($move_item->cartoonItem as $cartoonItem)
                                        @if($cartoonItem->cartoonItemDetails)
                                            <?php $data .= $cartoonItem->cartoonItemDetails->item.', '; ?>
                                        @else
                                            <?php $data =''; ?>
                                        @endif
                                        @endforeach
                                        <?php $item_description = ($data != '' ? rtrim($data,', ') : ''); ?>
                                    @elseif($move_item->item_id != 0)
                                        <?php $item_description = $move_item->subItems ? $move_item->subItems->subItemDetails->item : str_replace('+','',$move_item->item); ?>
                                    @endif

                                    <div><strong>Item No</strong> : {{$move_item->item_number}}</div>
                                    <div><strong>Package</strong> : {{ $item_package }}</div>
                                    <div><strong>Description</strong> : {{ $item_description }}</div>
                                    <div><strong>Condition</strong> : {{$conditionDetails}}</div>

                                </div>
                                <div class="col-30">
                                    <div style="float: right;width: 130px; height: 130px; text-align: center; vertical-align: middle; line-height: 130px; border: 1px solid #000;">
                                        <img src="{{ isset($condition->conditionImage[0]->image) ? $condition->conditionImage[0]->image : '' }}" style="max-width: 100%; max-height: 100%;">
                                    </div>
                                </div>
                                <div class="col-30">
                                    @if (isset($condition->conditionImage[1]->image))
                                        <div style="float: right;width: 130px; height: 130px; text-align: center; vertical-align: middle; line-height: 130px; border: 1px solid #000;">
                                            <img src="{{ $condition->conditionImage[1]->image }}" style="max-width: 100%; max-height: 100%;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <?php $total_items++; ?>
                        @endif
                    @endforeach
                @endif
            @endif
        @endforeach

    <div class="clearfix"></div>
    </div>
</body>

</html>
