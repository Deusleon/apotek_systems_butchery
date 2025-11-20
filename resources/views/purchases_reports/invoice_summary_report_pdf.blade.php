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
            border-collapse: collapse;
            padding: 8px;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        #table-detail {
            width: 100%;
        }

        #table-detail-main {
            width: 103%;
            margin-bottom: -6%;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        h3, h4 {
            font-weight: normal;
        }

        #container .logo-container {
            padding-top: -2%;
            text-align: center;
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
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Invoice Summary Report</h3>
            <h4 align="center" style="margin-top: -1%">From: <b>{{date('Y-m-d', strtotime($data[0]['dates'][0]))}}</b> To:
                <b>{{date('Y-m-d', strtotime($data[0]['dates'][1]))}}</b>
            </h4>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>

    <div class="row" style="margin-top: 1%;">
        <div class="col-md-12">

            @php
                $total_invoice_amount = 0;
                $total_paid_amount = 0;
                $total_balance = 0;
            @endphp
            <table id="table-detail" align="center">
                <thead>
                    <tr style="background: #1f273b; color: white; font-size: 0.9em">
                        <th align="center" style="width: 3%;">#</th>
                        <th align="left">Invoice #</th>
                        <th align="left">Supplier</th>
                        <th align="center">Invoice Date</th>
                        <th align="right">Amount</th>
                        <th align="right">Paid</th>
                        <th align="right">Balance</th>
                        <th align="center">Grace Period</th>
                        <th align="center">Due Date</th>
                        <th align="center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                        <tr>
                            <td align="center" style="width: 5%;">{{$loop->iteration}}</td>
                            <td align="left">{{$item->invoice_no}}</td>
                            <td align="left">{{$item->supplier['name']}}</td>
                            <td align="center">{{date('Y-m-d', strtotime($item->invoice_date))}}</td>
                            <td align="right">{{number_format($item->invoice_amount, 2)}}</td>
                            <td align="right">{{number_format($item->paid_amount, 2)}}</td>
                            <td align="right">{{number_format($item->remain_balance, 2)}}</td>
                            <td align="center">{{$item->grace_period}}</td>
                            <td align="center">{{date('Y-m-d', strtotime($item->payment_due_date))}}</td>
                            <td align="center">{{$item->received_status}}</td>
                        </tr>

                        @php
                            $total_invoice_amount += $item->invoice_amount;
                            $total_paid_amount += $item->paid_amount;
                            $total_balance += $item->remain_balance;
                        @endphp
                    @endforeach
                </tbody>
            </table>

            <!-- TOTAL SUMMARY -->
            <div style="margin-top: 20px; padding-top: 10px;">
                <h3 align="center"><b>Total Summary</b></h3>
                <table style="min-width: 30%; width: auto; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 6px; text-align: right;"><b>Total </b></td>
                        <td style="padding: 6px; text-align: center;"><b>:</b></td>
                        <td style="padding: 6px; text-align: right;"><b>{{number_format($total_invoice_amount, 2)}}</b></td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; text-align: right;"><b>Paid</b></td>
                        <td style="padding: 6px; text-align: center;"><b>:</b></td>
                        <td style="padding: 6px; text-align: right;"><b>{{number_format($total_paid_amount, 2)}}</b></td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; text-align: right;"><b>Balance</b></td>
                        <td style="padding: 6px; text-align: center;"><b>:</b></td>
                        <td style="padding: 6px; text-align: right;"><b>{{number_format($total_balance, 2)}}</b></td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>

<script type="text/php">
    if (isset($pdf)) {
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