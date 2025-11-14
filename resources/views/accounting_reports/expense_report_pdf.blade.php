<!DOCTYPE html>
<html>
<head>
    <title>Expense Report</title>

    <style>

        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
            /*border: 1px solid black;*/
            border-collapse: collapse;
            padding: 10px;
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
            /*border-spacing: 5px;*/
            width: 100%;
            /*margin-top: -10%;*/
        }

        #table-detail-main {
            width: 103%;
            margin-top: -10%;
            margin-bottom: 1%;
            border-collapse: collapse;
        }

        #table-detail tr > {
            line-height: 13px;
        }

        tr:nth-child(even) {
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

<div class="row">
    <div id="container">
        <div class="logo-container">
            @if($pharmacy['logo'])
                <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}"/>
            @endif
        </div>
    </div>
</div>
<div class="row" style="padding-top: -2%">
    <h1 align="center">{{$pharmacy['name']}}</h1>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h3>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['phone']}}</h3>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['email'].' | '.$pharmacy['website']}}</h3>
    <h2 align="center" style="margin-top: -1%">Expense Report</h2>
    <h4 align="center" style="margin-top: -1%">From: {{date('Y-m-d',strtotime($data->first()->from))}} To: {{date('Y-m-d',strtotime($data->first()->to))}}</h4>
    <div class="row" style="margin-top: 3%;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th align="left" style="width: 14%;">Date</th>
                    <th align="left" style="width: 14%;">Pay Method</th>
                    <th align="left" style="width: 14%;">Category</th>
                    <th align="left" style="width: 14%;">Updated by</th>
                    <th align="left" style="width: 30%;">Description</th>
                    <th align="right" style="width: 14%;">Amount</th>
                    {{--                <th>Payment Method</th>--}}
                </tr>
                </thead>
                @foreach($data as $item)
                    <tr>
                        <td align="left" style="width: 14%;">{{date('Y-m-d',strtotime($item->created_at))}}</td>
                        <td align="left" style="width: 14%;">
                            @if($item->payment_method_id == 1)
                                CASH
                            @elseif($item->payment_method_id == 2)
                                MOBILE MONEY
                            @elseif($item->payment_method_id == 3)
                                BANK
                            @elseif($item->payment_method_id == 4)
                                CHEQUE
                            @elseif($item->payment_method_id == 5)
                                OTHER
                            @else
                                UNKNOWN
                            @endif
                        </td>
                        <td align="left" style="width: 14%;">{{$item->accExpenseCategory['name']}}</td>
                        <td align="left" style="width: 14%;">{{$item->user['name'] ?? 'Unknown'}}</td>
                        <td align="left" style="width: 30%;">{{$item->expense_description}}</td>
                        <td align="right" style="width: 14%;">{{number_format($item->amount,2)}}</td>

                    </tr>
                @endforeach
            </table>
        </div>

        <hr>

        <div class="full-row" style="padding-top: 1%">
            <div class="col-35">
                <div class="full-row">
                </div>

            </div>
            <div class="col-15"></div>
            <div class="col-25"></div>
            <div class="col-25">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Total: </b></div>
                    <div class="col-50"
                         align="right">{{number_format($data->last()->total,2)}}</div>
                </div>
            </div>
        </div>

    </div>
</div>

<script type="text/php">
    if ( isset($pdf) ) {
        $x = 280;
        $y = 820;
        $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
        $font = null;
        $size = 10;
        $color = array(0,0,0);
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);


     }







</script>

</body>
</html>

