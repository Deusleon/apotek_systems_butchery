<!DOCTYPE html>
<html>

<head>
    <title>Customer Payment Statement</title>
    <style>
        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
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

        table,
        th,
        td {
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
            width: 100%;
            /* margin-top: -13%; */
        }

        #table-detail tr> {
            line-height: 13px;
        }

        #table-detail tr:nth-child(even) {
            background-color: #f2f2f2;
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
                    <img src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
                @endif
            </div>
        </div>
    </div>
    <div class="row" style="padding-top: -2%">
        <h1 align="center">{{$pharmacy['name']}}</h1>
        <h3 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['address']}}</h3>
        <h3 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['phone']}}</h3>
        <h3 align="center" style="font-weight: normal;margin-top: -1%">
            {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
        </h3>
        <h2 align="center" style="margin-top: -1%">{{'Customer Credit Payment Statement'}}</h2>
        <h3 align="center" style="font-weight: normal; margin-top: -1%">{{'Customer name: ' . ucfirst($customer)}}</h3>
        <h4 align="center" style="font-weight: normal;margin-top: -1%">{{$pharmacy['date_range']}}</h4>

        {{-- @dd($data) --}}
        @foreach($data['grouped_data'] as $datas => $dat)

            <div align="left" style="margin-top: 30px; width: 55%;">
                <div class="full-row" style="margin-bottom: 3px;">
                    <div class="col-50"><b>Receipt Number:</b> {{$datas}}</div>
                </div>

                <div class="full-row" style="width: 100%;">
                    <div class="col-50">
                        <b>Date of Sale:</b> {{date('j M, Y', strtotime($dat[0]['date']))}}
                    </div>
                    <div class="col-50" style="text-align: right;">
                        <b>Total Amount:</b> {{number_format(($dat[0]['paid_amount'] + $dat[0]['balance']), 2)}}
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: -1%">
                <table class="table table-sm" id="table-detail" align="center">
                    <tr style="background: #1f273b; color: white; font-size: 0.9em">
                        <th align="left">#</th>
                        <th align="left">Payment Date</th>
                        <th align="left">Received By</th>
                        <th align="right">Paid Amount</th>
                        <th align="right">Balance</th>
                    </tr>
                    @foreach($dat as $payment)
                        <tr>
                            <td align="left">{{$loop->iteration}}.</td>
                            <td align="left">{{date('Y-m-d H:i:s', strtotime($payment['created_at']))}}</td>
                            <td align="left">{{$payment['received_by']}}</td>
                            <td align="right">{{number_format($payment['paid_amount'], 2)}}</td>
                            <td align="right">{{number_format($payment['balance'], 2)}}</td>
                        </tr>
                    @endforeach

                </table>
                <hr>
            </div>

        @endforeach
        <div style="margin-top: 10px; padding-top: 5px;">
            <h3 align="center">Overall Summary</h3>
            <table
                style="width: auto; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd; border-collapse: collapse;">
                <tr>
                    <td style="padding: 4px; text-align: right;"><b>Total Amount</b></td>
                    <td style="padding: 4px; text-align: center;"><b>:</b></td>
                    <td style="padding: 4px; text-align: right;">
                        <b>{{ number_format($data['total_paid'] + $data['total_balance'], 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 4px; text-align: right;"><b>Total Paid</b></td>
                    <td style="padding: 4px; text-align: center;"><b>:</b></td>
                    <td style="padding: 4px; text-align: right;"><b>{{ number_format($data['total_paid'], 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 4px; text-align: right;"><b>Total Balance</b></td>
                    <td style="padding: 4px; text-align: center;"><b>:</b></td>
                    <td style="padding: 4px; text-align: right; color: red;">
                        <b>{{ number_format($data['total_balance'], 2) }}</b></td>
                </tr>
            </table>
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