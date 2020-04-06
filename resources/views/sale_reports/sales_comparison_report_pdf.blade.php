<!DOCTYPE html>
<html>
<head>
    <title>Sales Comparison Report</title>

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
            /*margin-top: 2%;*/
        }

        #table-detail-1 {
            width: 100%;
            margin-top: 3%;
        }

        #table-detail-2 {
            width: 100%;
            margin-top: 0%;
        }

        #table-detail-main {
            width: 103%;
            margin-top: 2%;
            margin-bottom: -2%;
            border-collapse: collapse;
        }

        #table-detail tr > {
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
    <h1 align="center">{{$pharmacy['name']}}</h1>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h3>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['phone']}}</h3>
    <h3 align="center" style="margin-top: -1%">{{$pharmacy['email'].' | '.$pharmacy['website']}}</h3>
    <h2 align="center" style="margin-top: -1%">Sales Comparison Report</h2>
    <h4 align="center" style="margin-top: -1%">{{$pharmacy['date_range']}}</h4>

    <div class="row" style="margin-top: 2%;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th>Date</th>
                    @foreach($data[0]['data'] as $key => $items)
                        <th align="right">{{$key}}</th>
                    @endforeach
                    <th align="right">Total</th>

                </tr>
                </thead>
                @foreach($data[0]['dates'] as $items)
                    <tr>
                        <td>{{date('d-m-Y',strtotime($items))}}</td>
                        @foreach($data[0]['data'] as $keys => $item)
                            <td align="right">{{number_format($data[0]['data'][$keys][$items],2)}}</td>
                        @endforeach
                        <td align="right"><b>{{number_format($data[0]['sum_by_date'][$items][0]['amount'],2)}}</b></td>

                    </tr>
                @endforeach
                <tr>
                    <td><b>Total</b></td>
                    @foreach($data[0]['data'] as $keys => $item)
                        <td align="right"><b>{{number_format($data[0]['sum_by_user'][$keys][0]['amount'],2)}}</b></td>
                    @endforeach
                    <td align="right"><b>{{number_format($data[0]['grand_total'],2)}}</b></td>
                </tr>
            </table>
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

