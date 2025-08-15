<!DOCTYPE html>
<html>
<head>
    <title>Daily Stock Count</title>


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
        }

        #table-top-detail {
            /*border-spacing: 5px;*/
            width: 100%;
            /*margin-top: -10%;*/
            margin-bottom: 3%;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #table-info {
            width: 50%;
            border-spacing: 5px;
        }

        .tab {
            display: inline-block;
            margin-left: 20px;
        }

        #receiver-sign {
            top: 10%;
        }

        #sender-sign {
            margin-top: 0%;
        }

        .topcorner {
            position: absolute;
            top: 0;
            right: 0;
            margin-top: -4%;
            margin-left: 78%;
        }

        .topcorner > p {
            font-size: 10px;
        }

        h3 {
            font-weight: normal;
        }

        h4 {
            font-weight: normal;
        }

    </style>


</head>
<body>
<div class="row">
    <div id="container"  align="center">
        @if($pharmacy['logo'])
            <div class="logo-container">
                <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}"/>
            </div>
        @endif
    </div>
</div>
<h3 align="center" style="margin-top: 2%">Daily Stock Count</h3>
<h3 align="center" style="margin-top: 2%">{{$pharmacy['name']}}</h3>
<h6 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h6>

<div class="row" style="margin-top: 10%">
    <div class="col-md-12">
        <table id="table-detail" align="center">
            <thead>
            <tr style="background: #1f273b; color: white;">
                <th>Product Name</th>
                <th>Brand</th>
                <th>Pack Size</th>
                <th>Sold Qty</th>
                <th>QOH</th>
            </tr>
            </thead>

            <!-- loop the product names here -->
            @foreach($data as $datas)
                <tr>
                    <td>{{ $datas['product_name']}}</td>
                    <td>{{ $datas['brand'] ?? 'N/A'}}</td>
                    <td>{{ $datas['pack_size'] ?? 'N/A'}}</td>
                    <td align="right">
                        <div style="margin-right: 50%">{{ number_format($datas['quantity_sold'],0) }}</div>
                    </td>
                    <td align="right">
                        <div style="margin-right: 50%">{{ number_format($datas['quantity_on_hand'],0) }}</div>
                    </td>
                </tr>
            @endforeach
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

