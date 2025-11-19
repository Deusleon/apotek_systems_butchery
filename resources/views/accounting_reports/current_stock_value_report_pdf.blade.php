<!DOCTYPE html>
<html>
<head>
    <title>Current Stock Report</title>

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
            /*border-spacing: 5px;*/
            width: 100%;
            /*margin-top: -10%;*/
        }

        #table-detail-main {
            width: 103%;
            margin-top: -12%;
            margin-bottom: -3%;
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
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Current Stock Value Report</h3>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>

    <div class="row" style="margin-top: 5%;">
        <div class="col-md-12">
            <table id="table-detail-main">
                <tr>
                    <td><b>Branch: </b> {{$data[0]['store']}}
                    </td>
            </table>
            <table id="table-detail" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th align="center" style="width: 5%;">#</th>
                    <th align="left">Product</th>
                    <th align="left">Category</th>
                    <th align="right">Total Buy</th>
                    <th align="right">Total Sell</th>
                    <th align="right">Total Profit</th>
                </tr>
                </thead>
                @foreach($data as $item)
                    <tr>
                        <td align="center" style="width: 5%;">{{ $loop->index + 1 }}</td>
                        <td align="left">{{$item['product_name']}}</td>
                        <td align="left">{{$item['category_name']}}</td>
                        <td align="right">{{number_format($item['buy_price'],2)}}</td>
                        <td align="right">{{number_format($item['sell_price'],2)}}</td>
                        <td align="right">{{number_format($item['profit'],2)}}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <hr>

        <table id="table-detail" align="center">
            <tr>
                <td style="width: 5%;"></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><b>Total Buy:</b></td>
                <td align="right">{{number_format(max(array_column($data, 'grand_total_buy')),2)}}</td>
            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><b>Total Sell:</b></td>
                <td align="right">{{number_format(max(array_column($data, 'grand_total_sell')),2)}}</td>
            </tr>
            <tr>
                <td style="width: 5%;"></td>
                <td></td>
                <td></td>
                <td></td>
                <td align="right"><b>Total Profit:</b></td>
                <td align="right">{{number_format(max(array_column($data, 'grand_total_profit')),2)}}</td>
            </tr>
        </table>

        <hr>
        <!-- SUMMARY - Centered like Gross Profit Detail Report -->
        {{-- <div style="margin-top: 20px; padding-top: 10px;">
            <h3 align="center"><b>Summary</b></h3>
            <table style="min-width: 25%; width: auto; margin: 0 auto; background-color: #f8f9fa; border: 1px solid #ddd; border-collapse: collapse;">
                <tr>
                    <td style="padding: 6px; text-align: right;"><b>Total Buy</b></td>
                    <td style="padding: 6px; text-align: center;"><b>:</b></td>
                    <td style="padding: 6px; text-align: right;"><b>{{ number_format(max(array_column($data, 'grand_total_buy')), 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 6px; text-align: right;"><b>Total Sell</b></td>
                    <td style="padding: 6px; text-align: center;"><b>:</b></td>
                    <td style="padding: 6px; text-align: right;"><b>{{ number_format(max(array_column($data, 'grand_total_sell')), 2) }}</b></td>
                </tr>
                <tr>
                    <td style="padding: 6px; text-align: right;"><b>Total Profit</b></td>
                    <td style="padding: 6px; text-align: center;"><b>:</b></td>
                    <td style="padding: 6px; text-align: right;"><b>{{ number_format(max(array_column($data, 'grand_total_profit')), 2) }}</b></td>
                </tr>
            </table>
        </div> --}}

    </div>
</div>

<script type="text/php">
if (isset($pdf)) {

    $width = $pdf->get_width();
    $height = $pdf->get_height();

    // Center horizontally, 30px from bottom
    $x = $width / 2 - 50;
    $y = $height - 30;

    $text = "{PAGE_NUM} of {PAGE_COUNT} pages";

    // Use a safe non-bold font
    $font = $fontMetrics->get_font("helvetica", "normal");

    $size = 10;
    $color = array(0,0,0);

    $pdf->page_text($x, $y, $text, $font, $size, $color);
}
</script>

</body>
</html>