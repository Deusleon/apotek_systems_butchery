<!DOCTYPE html>
<html>
<head>
    <title>Issue Return Report</title>

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

<div style="width: 100%; text-align: center; align-items: center; margin-bottom: -1%;">
    @if($pharmacy['logo'])
        <img style="max-width: 90px; max-height: 90px;" src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
    @endif
    <div style="font-weight: bold; font-size: 16px;">{{$pharmacy['name']}}</div>
    <div style="justify-content: center; font-size: 12px; line-height: 1.2;">
        {{$pharmacy['address']}}<br>
        {{$pharmacy['phone']}}<br>
        {{$pharmacy['email'] . ' | ' . $pharmacy['website']}}
    </div><br>
    <div>
        <h3 align="center" style="font-weight: bold; margin-top: -1%">Issue Return Report</h3>
        <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
    </div>
</div>

    <div class="row" style="margin-top: 5.5%;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th align="left" style="width: 1%;">#</th>
                    <th align="left">Code</th>
                    <th align="left">Product Name</th>
                    <th align="center">Return Qty</th>
                    <th align="left">Return No</th>
                    <th align="left">Returned By</th>
                    <th align="left">Return Date</th>
                    <th align="left">Returned To</th>
                </tr>
                </thead>
                @foreach($data as $item)
                    <tr>
                        <td align="left">{{$loop->iteration}}.</td>
                        <td align="left">{{$item['product_id']}}</td>
                        <td align="left">{{$item['name']}}</td>
                        <td align="center">{{number_format($item['issue_qty'],0)}}</td>
                        <td align="left">{{$item['issue_no']}}</td>
                        <td align="left">{{$item['issued_by']}}</td>
                        <td align="left">{{$item['issued_date']}}</td>
                        <td align="left">{{$item['issued_to']}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
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

