<!DOCTYPE html>
<html>
<head>
    <title>Product Details Report</title>

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

    </style>

</head>
<body>

<h4 align="center">{{$pharmacy['name']}}</h4>
<h3 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h3>
<h2 align="center" style="margin-top: -2%">Product Details Report</h2>
<div class="row" style="margin-top: 10%;">
    <div class="col-md-12">
        <table id="table-detail" align="center">
            <thead>
            <tr style="background: #1f273b; color: white; font-size: 0.9em">
                <th>Code</th>
                <th>Product Name</th>
                <th>Category</th>
{{--                <th>Indication</th>--}}
            </tr>
            </thead>
            <!-- loop the product names here -->
            {{--            @foreach($data as $datas => $dat)--}}
            {{--                <tr>--}}
            {{--                    <td style="border: none" colspan="5" id="category"><b>{{$datas}}</b></td>--}}
            {{--                </tr>--}}
            @foreach($data as $item)
                <tr>
                    <td>{{$item['product_id']}}</td>
                    <td>{{$item['name']}}</td>
                    <td align="">{{$item['category']}}</td>
{{--                    <td align="right">{{$item['indication']}}</td>--}}
                </tr>
            @endforeach
            {{--            @endforeach--}}
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

