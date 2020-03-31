<!DOCTYPE html>
<html>
<head>
    <title>Customer Payment Statement</title>
    <style>

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
            border: 1px solid black;
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


        #table-detail {
            border-spacing: 6%;
            width: 96%;
            margin-top: -13%;
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


    </style>
</head>
<body>
<div class="row">
    <div id="container">
        <div class="logo-container">
            @if($pharmacy['logo'])
                <img src="{{public_path('fileStore/logo/'.$pharmacy['logo'])}}"/>
            @endif
        </div>
    </div>
</div>
<div class="row" style="padding-top: -2%">
    <h4 align="center">{{$pharmacy['name']}}</h4>
    <h3 align="center" style="margin-top: -2%">{{$pharmacy['address']}}</h3>
    <h2 align="center" style="margin-top: -2%">{{ucfirst($customer).' '.'Credit Payment Statement'}}</h2>
    <h5 align="center" style="margin-top: -2%">Phone: {{$pharmacy['phone']}}</h5>
    <h4 align="center" style="margin-top: -2%">{{$pharmacy['date_range']}}</h4>

    @foreach($data as $datas => $dat)

        <div class="full-row" style="margin-top: 4%;">
            <div class="col-35">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Receipt Number:</b></div>
                    <div class="col-50" align="right">{{$datas}}</div>
                </div>
            </div>
        </div>
        <div class="full-row">
            <div class="col-35">
                <div class="full-row">
                    <div class="col-50" align="left"><b>Date of Sale:</b></div>
                    <div class="col-50" align="right">{{date('j M, Y', strtotime($dat[0]['date']))}}</div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 13%">
            <table class="table table-sm" id="table-detail" align="center">
                <tr>
                    <th align="left">SN</th>
                    <th align="right">Payment Date</th>
                    <th align="right">Paid Amount</th>
                    <th align="right">Balance</th>
                </tr>

                @foreach($dat as $payment)
                    <tr>
                        <td align="left">{{$loop->iteration}}</td>
                        <td align="right">{{date('Y-m-d H:i:s', strtotime($payment['created_at']))}}</td>
                        <td align="right">{{number_format($payment['paid_amount'],2)}}</td>
                        <td align="right">{{number_format($payment['balance'],2)}}</td>
                    </tr>
                @endforeach

            </table>
        </div>
        {{--



        <div class="full-row" style="padding-top: 1%">
        <div class="col-35">
         <div class="full-row">
        <div class="col-50" align="left"><b>Sold By: </b></div>
        <div class="col-50" align="right">{{$dat[0]['sold_by']}}</div>
        </div>

        </div>
        <div class="col-15"></div>
        <div class="col-25"></div>
        <div class="col-25">
        <div class="full-row">
        <div class="col-50" align="left"><b>Sub Total: </b></div>
        <div class="col-50" align="right">{{number_format(($dat[0]['grand_total']-$dat[0]['total_vat']),2)}}</div>
        </div>
        </div>
        </div>
        <div class="full-row">
        <div class="col-35">
        <div class="full-row">
        <div class="col-50" align="left"><b>Sale Date:</b></div>
        <div class="col-50" align="right">{{date('j M, Y', strtotime($dat[0]['created_at']))}}</div>
        </div>
        </div>
        <div class="col-15"></div>
        <div class="col-25"></div>
        <div class="col-25">
        <div class="full-row">
        <div class="col-50" align="left"><b>Discount: </b></div>
        <div class="col-50" align="right">{{number_format($dat[0]['total_discount'],2)}}</div>
        </div>
        </div>
        </div>
        <div class="full-row">
        <div class="col-35">
        <div class="full-row">
        <div class="col-50" align="left"><b>Recept #: </b></div>
        <div class="col-50" align="right">{{$datas}}</div>
        </div>
        </div>
        <div class="col-15"></div>
        <div class="col-25"></div>
        <div class="col-25">
        <div class="full-row">
        <div class="col-50" align="left"><b>VAT: </b></div>
        <div class="col-50" align="right">{{number_format($dat[0]['total_vat'],2)}}</div>
        </div>
        </div>
        </div>
        <div class="full-row">
        <div class="col-35">
        <div class="full-row">
        <div class="col-50" align="left"><b>TIN #: </b></div>
        <div class="col-50" align="right">{{$pharmacy['tin_number']}}</div>
        </div>
        </div>
        <div class="col-15"></div>
        <div class="col-25"></div>
        <div class="col-25">
        <div class="full-row">
        <div class="col-50" align="left"><b>Total:</b></div>
        <div class="col-50" align="right">{{number_format(($dat[0]['grand_total']),2)}}</div>
        </div>
        </div>
        </div>
        <div class="full-row">
        <div class="col-35">
        <div class="full-row">
        <div class="col-50" align="left"><b>Customer: </b></div>
        <div class="col-50" align="right">{{$dat[0]['customer']}}</div>
        </div>
        </div>
        <div class="col-15"></div>
        <div class="col-25"></div>
        <div class="col-25">
        </div>
        </div>

            --}}

        <hr>
    @endforeach

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

