<!DOCTYPE html>
<html>
<head>
    <title>Credit Payments Report</title>

    <style>

        body {
            /*font-size: 30px;*/
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
            margin-top: -10%;
            /*border: 1px solid #FFFFFF;*/
            border-collapse: collapse;
        }

        #table-detail tr {
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
    <h4 align="center">{{$pharmacy['name']}}</h4>
    <h3 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h3>
    <h2 align="center" style="margin-top: -2%">Credit Payment Report</h2>
    <h5 align="center" style="margin-top: -2%">Phone: {{$pharmacy['phone']}}</h5>
    <h4 align="center" style="margin-top: -2%">{{$pharmacy['date_range']}}</h4>

    <div class="row" style="margin-top: 10%;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <thead>
                <tr style="background: #1f273b; color: white; font-size: 0.9em">
                    <th>Customer Name</th>
                    <th align="center">Payment Date</th>
                    <th align="right">Receipt #</th>
                    <th align="right">Amount</th>
                </tr>
                <thead>
                <tbody>
                <?php $total = 0 ?>
                @foreach($data as $payment)
                    <tr>
                        <td>{{$payment->name}}</td>
                        <td align="center">{{date('j M, Y h:i:s a', strtotime($payment->created_at))}}</td>
                        <td align="right">{{$payment->receipt_number}}</td>
                        <td align="right">{{number_format($payment->paid_amount,2)}}</td>
                        <?php $total += $payment->paid_amount ?>
                        @endforeach
                    </tr>
                    <tr style="background: #1f273b; color: white; font-size: 0.9em">
                        <td style="border: none" colspan="2"></td>
                        <td colspan="1" align="right"><b>Total</b></td>
                        <td colspan="1" align="right"><b>{{number_format($total,2)}}</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>

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

</html>

