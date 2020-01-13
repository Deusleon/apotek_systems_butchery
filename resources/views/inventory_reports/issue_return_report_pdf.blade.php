<!DOCTYPE html>
<html>
<head>
    <title>Issue Return Report</title>

    <style>

        @page {
            size: A4 landscape;
        }


        body {
            /*font-size: 24px;*/
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
<h2 align="center" style="margin-top: -2%">Issue Return Report</h2>
<div class="row" style="margin-top: 10%;">
    <div class="col-md-12">
        <table id="table-detail" align="center">
            <!-- loop the product names here -->
            <thead>
            <tr style="background: #1f273b; color: white; font-size: 0.8em">
                <th>Code</th>
                <th>Product Name</th>
                <th>Issue Qty</th>
                <th>Returned Qty</th>
                <th>Return Value</th>
                <th>Returned By</th>
                <th>Issue No</th>
                <th>Issued To</th>
                <th>Issued Date</th>
                <th>Return Date</th>
                <th>Reason</th>
            </tr>
            </thead>
            @foreach($data as $item)
                <tr>
                    <td>{{$item->issue['currentStock']['product']['id']}}</td>
                    <td>{{$item->issue['currentStock']['product']['name']}}</td>
                    <td align="right">
                        <div style="margin-right: 50%">{{floatval($item->issue_qty)}}</div>
                    </td>
                    <td align="right">
                        <div style="margin-right: 50%">{{floatval($item->return_qty)}}</div>
                    </td>
                    <td align="right">{{$item->return_value}}</td>
                    <td align="">{{$item->user['name']}}</td>
                    <td align="">{{$item->issue['issue_no']}}</td>
                    <td align="">{{$item->issue['issueLocation']['name']}}</td>
                    <td align="" style="font-size: 0.7em">{{date('Y-m-d',strtotime($item->issed_at))}}</td>
                    <td align="" style="font-size: 0.7em">{{date('Y-m-d',strtotime($item->returned_at))}}</td>
                    <td align="">{{$item->Reason}}</td>
                </tr>
            @endforeach
        </table>
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

