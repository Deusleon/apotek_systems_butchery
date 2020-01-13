<!DOCTYPE html>
<html>
<head>
    <title>Expense Report</title>

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
            /*margin-top: -10%;*/
        }

        #table-detail-main {
            width: 103%;
            margin-top: -10%;
            margin-bottom: 1%;
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
<h2 align="center" style="margin-top: -2%">Expense Report</h2>
<div class="row" style="margin-top: 10%;">
    <div class="col-md-12">
        <table id="table-detail-main">
            <tr>
                <td style="background: #1f273b; color: white"><b>From
                        Date:</b> {{date('d-m-Y',strtotime($data->first()->from))}}</td>
                <td style="background: #1f273b; color: white"><b>To
                        Date:</b> {{date('d-m-Y',strtotime($data->first()->to))}}</td>
            </tr>
        </table>
        <table id="table-detail" align="center">
            <!-- loop the product names here -->
            <thead>
            <tr style="background: #1f273b; color: white; font-size: 0.9em">
                <th>Expense Date</th>
                <th>Expense Category</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Payment Method</th>
            </tr>
            </thead>
            @foreach($data as $item)
                <tr>
                    <td>{{date('d-m-Y',strtotime($item->created_at))}}</td>
                    <td>{{$item->accExpenseCategory['name']}}</td>
                    <td align="">{{$item->expense_description}}</td>
                    <td align="right">{{number_format($item->amount,2)}}</td>
                    @if($item->payment_method_id == 1)
                        <td align="">CASH</td>
                    @else
                        <td align="">BILL</td>
                    @endif

                </tr>
            @endforeach
        </table>
    </div>

    <hr>
    <div style="margin-left: 70%;width: 29.6%;background: #f2f2f2;margin-top: 2%; padding: 1%"><b>Total: </b>
    </div>
    <div align="right"
         style="margin-top: -10%; padding-top: 1%; padding-left: 1%">
        {{number_format($data->last()->total,2)}}</div>

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

