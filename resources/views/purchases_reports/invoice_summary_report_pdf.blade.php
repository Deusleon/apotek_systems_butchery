<!DOCTYPE html>
<html>
<head>
    <title>Invoice Summary Report</title>

    <style>
        @page {
            size: A4 landscape;
        }

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
    <h2 align="center" style="margin-top: -1%">Invoice Summary Report</h2>

    <div class="row" style="margin-top: 10%;">
        <div class="col-md-12">

            <table id="table-detail-main">
                <tr>
                    <td style="background: #1f273b; color: white"><b>From
                            Date:</b> {{date('d-m-Y',strtotime($data[0]['dates'][0]))}}</td>
                    <td style="background: #1f273b; color: white"><b>To
                            Date:</b> {{date('d-m-Y',strtotime($data[0]['dates'][1]))}}</td>
                </tr>
            </table>

            <table id="table-detail" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th style="text-align: center">Invoice #</th>
                    <th style="text-align: center">Supplier</th>
                    <th style="text-align: center">Invoice Date</th>
                    <th style="text-align: center">Invoice Amount</th>
                    <th style="text-align: center">Paid amount</th>
                    <th style="text-align: center">Balance</th>
                    <th style="text-align: center">Grace Period</th>
                    <th style="text-align: center">Due Date</th>
                    <th style="text-align: center">Status</th>


                </tr>
                </thead>
                @foreach($data as $item)
                    <tr>
                        <td>{{$item->invoice_no}}</td>
                        <td>{{$item->supplier['name']}}</td>
                        <td>{{date('Y-d-m',strtotime($item->invoice_date))}}</td>
                        <td align="right">{{number_format($item->invoice_amount,2)}}</td>
                        <td align="right">{{number_format($item->paid_amount,2)}}</td>
                        <td align="right">{{number_format($item->remain_balance,2)}}</td>
                        <td align="right">
                            <div style="margin-right: 50%"></div>{{$item->grace_period}}
                        </td>
                        <td style="font-size: 0.9em">{{date('Y-m-d',strtotime($item->payment_due_date))}}</td>
                        <td>{{$item->received_status}}</td>
                    </tr>
                @endforeach
            </table>
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
        $word_space = 0.0;  //  default
        $char_space = 0.0;  //  default
        $angle = 0.0;   //  default
        $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);


     }

</script>

</body>
</html>

