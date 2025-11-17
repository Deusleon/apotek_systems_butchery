<!DOCTYPE html>
<html>
<head>
    <title>Purchase Returns Report</title>

    <style>
        @page {
            size: A4 landscape;
        }

        body {
            font-size: 13px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table,
        th {
            border-collapse: collapse;
            padding: 8px;
        }

        table,
        td {
            border-collapse: collapse;
            padding: 5px;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group
        }

        tfoot {
            display: table-footer-group
        }

        #table-detail {
            width: 100%;
        }

        #table-detail-main {
            width: 103%;
            margin-top: 2%;
            margin-bottom: -2%;
            border-collapse: collapse;
        }

        #table-detail tr> {
            line-height: 13px;
        }

        #table-detail tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #category {
            text-transform: uppercase;
        }

        h3 {
            font-weight: normal;
        }

        h4 {
            font-weight: normal;
        }

        .full-row {
            width: 100%;
            padding-left: 3%;
            padding-right: 2%;
        }

        .col-50 {
            display: inline-block;
            font-size: 13px;
            width: 50%;
        }

        .col-25 {
            display: inline-block;
            font-size: 13px;
            width: 25%;
        }

        .col-35 {
            display: inline-block;
            font-size: 13px;
            width: 35%;
        }

        .col-15 {
            display: inline-block;
            font-size: 13px;
            width: 15%;
        }

        #container .logo-container {
            padding-top: -2%;
            text-align: center;
            vertical-align: middle;
        }

        #container .logo-container img {
            max-width: 160px;
            max-height: 160px;
        }
    </style>
</head>
<body>

<div class="row" style="padding-top: -2%">
    <!-- Header Section - Updated to match Cash Sales Report style -->
    <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -1%;">
        @if($pharmacy['logo'])
            <img style="max-width: 90px; max-height: 90px;"
                src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
        @endif
        <div style="font-weight: bold; font-size: 16px;">{{$pharmacy['name']}}</div>
        <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
            {{$pharmacy['address']}}<br>
            {{$pharmacy['phone']}}<br>
            {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
        </div><br>
        <div>
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Purchase Returns Report</h3>
            <h4 align="center" style="margin-top: -1%">From: <b>{{date('Y-m-d',strtotime($data->first()->date_range[0] ?? 'now'))}}</b> To:
                <b>{{date('Y-m-d',strtotime($data->first()->date_range[1] ?? 'now'))}}</b>
            </h4>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th align="left" style="width: 1%;">#</th>
                    <th align="left" style="width: 35%;">Product Name</th>
                    <th align="left" style="width: 10%;">Receive Date</th>
                    <th align="center" style="width: 10%;">Qty Received</th>
                    <th align="left" style="width: 10%;">Return Date</th>
                    <th align="center" style="width: 10%;">Qty Returned</th>
                    <th align="right" style="width: 15%;">Refund Amount</th>
                </tr>
                </thead>
                @php $total_refund = 0; @endphp
                @foreach($data as $return)
                    <tr>
                        <td align="left">{{$loop->iteration}}.</td>
                        <td align="left">
                            {{$return->goodsReceiving->product->name ?? ''}} {{$return->goodsReceiving->product->brand ?? ''}} {{$return->goodsReceiving->product->pack_size ?? ''}}{{$return->goodsReceiving->product->sales_uom ?? ''}}
                        </td>
                        <td align="left">{{date('Y-m-d', strtotime($return->goodsReceiving->created_at ?? now()))}}</td>
                        <td align="center">{{number_format($return->received_quantity ?? 0, 0)}}</td>
                        <td align="left">{{date('Y-m-d', strtotime($return->date ?? now()))}}</td>
                        <td align="center">{{number_format($return->return_quantity ?? 0, 0)}}</td>
                        <td align="right">{{number_format(($return->unit_cost ?? 0 * $return->return_quantity ?? 0), 2)}}</td>
                    </tr>
                    @php $total_refund += ($return->unit_cost ?? 0 * $return->return_quantity ?? 0); @endphp
                @endforeach
            </table>

            <hr>

            <div class="full-row" style="padding-top: 1%">
                <div class="col-35"><div class="full-row"></div></div>
                <div class="col-15"></div>
                <div class="col-25"></div>
                <div class="col-25">
                    <div class="full-row">
                        <div class="col-50" align="left"><b>Total Refund: </b></div>
                        <div class="col-50" align="right">{{number_format($total_refund, 2)}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/php">
    if ( isset($pdf) ) {
        $x = 400;
        $y = 560;
        $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;
        $char_space = 0.0;
        $angle = 0.0;
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
     }
</script>

</body>
</html>