<!DOCTYPE html>
<html>

<head>
    <title>Customer Credit Payment Statement</title>

    <style>
        body {
            font-size: 12px;
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
            line-height: 10px;
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

        #container .logo-container {
            padding-top: -2%;
            text-align: center;
            vertical-align: middle;
        }

        #container .logo-container img {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>

<body>
    <div class="row" style="padding-top: -2%">
        <!-- Header Section -->
        <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -3%;">
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
                <h3 align="center" style="font-weight: bold; margin-top: -1%">Customer Credit Payment Statement</h3>
                <h4 align="center" style="margin-top: -1%">From: <b>{{$pharmacy['from_date']}}</b> To:
                    <b>{{$pharmacy['to_date']}}</b>
                </h4>
                <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
            </div>
        </div>
        {{-- @dd($data) --}}
        @foreach($data['grouped_data'] as $datas => $dat)

            <div align="left" style="margin-top: 20px; width: 55%;">
                <div class="full-row" style="margin-bottom: 3px;">
                    <div class="col-50"><b>Receipt Number:</b> {{$datas}}</div>
                </div>

                <div class="full-row" style="width: 100%;">
                    <div class="col-50">
                        <b>Date of Sale:</b> {{date('Y-m-d', strtotime($dat[0]['date']))}}
                    </div>
                    <div class="col-50" style="text-align: right; margin-top: -2%;">
                        <b>Total:</b> {{number_format(($dat[0]['paid_amount'] + $dat[0]['balance']), 2)}}
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top: 0%; margin-left: -5px;">
                <table class="table table-sm" id="table-detail" align="center">
                    <tr style="background: #1f273b; color: white; font-size: 0.9em">
                        <th align="left">#</th>
                        <th align="left">Received By</th>
                        <th align="left">Payment Date</th>
                        <th align="right">Paid Amount</th>
                        <th align="right">Balance</th>
                    </tr>
                    @foreach($dat as $payment)
                        <tr>
                            <td align="left">{{$loop->iteration}}.</td>
                            <td align="left">{{$payment['received_by']}}</td>
                            <td align="left">{{date('Y-m-d', strtotime($payment['created_at']))}}</td>
                            <td align="right">{{number_format($payment['paid_amount'], 2)}}</td>
                            <td align="right">{{number_format($payment['balance'], 2)}}</td>
                        </tr>
                    @endforeach

                </table>
                <hr style="margin-left: 10px;">
            </div>
        @endforeach
        <div style="margin-top: 10px; padding-top: 5px;">
            <h3 align="center"><b>Summary</b></h3>
            <table
                style="width: auto; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd; border-collapse: collapse;">
                <tr>
                    <td style="padding: 4px; text-align: right;"><b>Total</b></td>
                    <td style="padding: 4px; text-align: center;"><b>:</b></td>
                    <td style="padding: 4px; text-align: right;">
                        <b>{{ number_format($data['total_paid'] + $data['total_balance'], 2) }}</b>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 4px; text-align: right;"><b>Paid</b></td>
                    <td style="padding: 4px; text-align: center;"><b>:</b></td>
                    <td style="padding: 4px; text-align: right;"><b>{{ number_format($data['total_paid'], 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 4px; text-align: right;"><b>Balance</b></td>
                    <td style="padding: 4px; text-align: center;"><b>:</b></td>
                    <td style="padding: 4px; text-align: right;">
                        <b>{{ number_format($data['total_balance'], 2) }}</b>
                    </td>
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