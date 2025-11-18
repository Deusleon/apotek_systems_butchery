<!DOCTYPE html>
<html>
<head>
    <title>Requisition</title>
    <style>

        * {
            font-family: Verdana, Arial, sans-serif;
        }

         body {
            font-size: 12px;
        }

        table, th, td {
            border-collapse: collapse;
            padding: 10px;
            font-size: x-small;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
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

        .col-100 {
            display: inline-block;
            font-size: 13px;
            width: 90%;
        }

        .col-25 {
            display: inline-block;
            font-size: 13px;
            width: 25%;
        }

         h3 {
            font-weight: normal;
        }

        h4 {
            font-weight: normal;
        }

        #table-detail {
            border-spacing: 6%;
            width: 96%;
            border: none;
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

        /* Header row styling */
        .table-header {
            background: #1f273b;
            color: white;
        }

        /* Table styling */
        .customer-table {
            width: 96%;
            border: none;
            border-collapse: collapse;
            margin: 0 auto;
        }

        .customer-table .index-col {
            width: auto;
            text-align: left;
            padding-left: 5px;
        }

        .customer-table td {
            padding: 2px 2px;
            border: 1px solid #858484;
            font-size: 10px;
            height: 15px;
        }

    </style>
</head>
<body>
<div class="row" style="padding-top: -2%">
    <!-- Header Section - Updated to match Cash Sales Report style -->
    <div style="width: 100%; text-align: center; align-items: center; margin-bottom: -1%;">
        @if($pharmacy['logo'])
            <img style="max-width: 90px; max-height: 90px;"
                src="{{ public_path('fileStore/logo/' . $pharmacy['logo']) }}" />
        @endif
        <div style="font-weight: bold; font-size: 16px;">{{ $pharmacy['name'] }}</div>
        <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
            {{ $pharmacy['address'] }}<br>
            {{ $pharmacy['phone'] }}<br>
            {{ $pharmacy['email'] . ' | ' . $pharmacy['website'] }}
        </div><br>
        <div>
            <h3 align="center" style="font-weight: bold; margin-top: -1%">REQUISITION RECEIPT</h3>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{ date('Y-m-d H:i:s') }}</h4>
        </div>
    </div>

    <table class="customer-table">
        <tbody>
            <tr>
                <td class="index-col" style="width: 25%;">Requisition #</td>
                <td class="index-col" style="width: 25%;">Date</td>
                <td class="index-col" style="width: 25%">From</td>
                <td class="index-col" style="width: 25%;">To</td>
            </tr>
            <tr>
                <td class="index-col" style="width: 25%;">{{ $requisition->req_no }}</td>
                <td class="index-col" style="width: 25%;">{{ date('Y-m-d', strtotime($requisition->created_at)) }}</td>
                <td class="index-col" style="width: 25%">{{ $fromStore->name ?? '' }}</td>
                <td class="index-col" style="width: 25%;">{{ $toStore->name ?? '' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="row">
        <table class="table table-sm" id="table-detail" align="center">
            <tr class="table-header">
                <th align="center" style="width: 5%;">#</th>
                <th align="left">Product Name</th>
                <th align="right">Quantity</th>
            </tr>

            @foreach($requisitionDet as $item)
                <tr>
                    <td align="center" style="width: 5%;">{{ $loop->index + 1 }}</td>
                    <td align="left">
                        {{ $item->products_->name ?? '' }}
                        @if(!empty($item->products_->brand)) {{ $item->products_->brand }} @endif
                        @if(!empty($item->products_->pack_size) && !empty($item->products_->sales_uom))
                            {{ $item->products_->pack_size }}{{ $item->products_->sales_uom }}
                        @endif
                    </td>
                    <td align="right">{{ number_format($item->quantity, 0) ?? '' }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <h6 align="center">Created By: {{ $requisition->creator->name }}</h6>
    <h6 align="center" style="font-style: italic; margin-top: -2%">Created Date: {{ date('Y-m-d', strtotime($requisition->created_at)) }}</h6>
</div>

<script type="text/php">
if (isset($pdf)) {

    $width = $pdf->get_width();
    $height = $pdf->get_height();

    // Slogan at the bottom, above page number
    $x_slogan = $width / 2 - 50;
    $y_slogan = $height - 50;
    $text_slogan = "{{ $pharmacy['slogan'] }}";
    $font_slogan = $fontMetrics->get_font("helvetica", "italic");
    $size_slogan = 10;
    $color_slogan = array(0,0,0);
    $pdf->page_text($x_slogan, $y_slogan, $text_slogan, $font_slogan, $size_slogan, $color_slogan);

    // Page numbers at the bottom
    $x = $width / 2 - 50;
    $y = $height - 30;
    $text = "{PAGE_NUM} of {PAGE_COUNT} pages";
    $font = $fontMetrics->get_font("helvetica", "normal");
    $size = 10;
    $color = array(0,0,0);
    $pdf->page_text($x, $y, $text, $font, $size, $color);
}
</script>

</body>
</html>