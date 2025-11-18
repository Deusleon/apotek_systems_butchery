<!DOCTYPE html>
<html>
<head>
    <title>Income Statement Report</title>

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
            /*margin-top: -10%;*/
        }

        #table-detail-1 {
            width: 100%;
            margin-top: 3%;
        }

        #table-detail-2 {
            width: 100%;
            margin-top: 0%;
        }

        #table-detail-3 {
            width: 100%;
            margin-top: 3%;
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
            <h3 align="center" style="font-weight: bold; margin-top: -1%">Income Statement Report</h3>
            <h4 align="center" style="margin-top: -1%">From: <b>{{ date('Y-m-d', strtotime($data->first()->from)) }}</b> To:
                <b>{{ date('Y-m-d', strtotime($data->first()->to)) }}</b>
            </h4>
            <h4 align="center" style="margin-top: -1.5%">Printed On: {{now()->format('Y-m-d H:i:s')}}</h4>
        </div>
    </div>

    <div class="row" style="margin-top: 5px;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th style="text-align: left;">Sales</th>
                    <th></th>
                </tr>
                </thead>
                <tr>
                    <td align="center">Sale Amount</td>
                    <td align="right">{{number_format($data->last()->total_sell,2)}}</td>
                </tr>
            </table>
            <hr>

            <div class="full-row" style="padding-top: 1%">
                <div class="col-25">
                    <div class="full-row">
                    </div>

                </div>
                <div class="col-15"></div>
                <div class="col-25"></div>
                <div class="col-35">
                    <div class="full-row">
                        <div class="col-50" style="width: 40%;" align="left"><b>Total Sales: </b></div>
                        <div class="col-50" style="width: 60%;"
                             align="right">{{number_format($data->last()->total_sell,2)}}</div>
                    </div>
                </div>
            </div>

            <table id="table-detail-1" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th style="text-align: left;">Cost of Sales</th>
                    <th></th>
                </tr>
                </thead>
                <tr>
                    <td align="center">Buy Amount</td>
                    <td align="right">{{number_format($data->last()->total_buy,2)}}</td>
                </tr>
            </table>
            <hr>

            <div class="full-row" style="padding-top: 1%">
                <div class="col-25">
                    <div class="full-row">
                    </div>

                </div>
                <div class="col-15"></div>
                <div class="col-25"></div>
                <div class="col-35">
                    <div class="full-row">
                        <div class="col-50" style="width: 40%;" align="left"><b>Total Buy: </b></div>
                        <div class="col-50" style="width: 60%;"
                             align="right">{{number_format($data->last()->total_buy,2)}}</div>
                    </div>
                </div>
            </div>

            <table id="table-detail-3" align="center">
                <!-- loop the product names here -->
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th style="text-align: left;">Expenses</th>
                    <th></th>
                </tr>
                </thead>
                <tr>
                    <td align="center">Expense Amount</td>
                    <td align="right">{{number_format($data->last()->expense_amount,2)}}</td>
                </tr>
            </table>
            <hr>

            <div class="full-row" style="padding-top: 1%">
                <div class="col-25">
                    <div class="full-row">
                    </div>

                </div>
                <div class="col-15"></div>
                <div class="col-25"></div>
                <div class="col-35">
                    <div class="full-row">
                        <div class="col-50" style="width: 40%;" align="left"><b>Total Expenses: </b></div>
                        <div class="col-50" style="width: 60%;"
                             align="right">{{number_format($data->last()->expense_amount,2)}}</div>
                    </div>
                </div>
            </div>

            <hr style="margin-top: 3%">
            <table id="table-detail-2" align="center">
                <!-- loop the product names here -->
                <tr>
                    <td align="right">Net Income (Total Sell - Total Buy - Expenses)</td>
                    <td align="right">{{number_format(($data->last()->total_sell) - ($data->last()->total_buy) - ($data->last()->expense_amount),2)}}</td>
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