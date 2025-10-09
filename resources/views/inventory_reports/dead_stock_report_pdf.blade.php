<!DOCTYPE html>
<html>

<head>
    <title>Stock Stock Report</title>

    <style>
        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }


        table,
        th,
        td {
            /*border: 1px solid black;*/
            border-collapse: collapse;
            padding: 10px;
        }

        th {
            text-align: left;
        }

        table {
            page-break-inside: auto
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto
        }

        thead {
            display: table-header-group;
            background: #1f273b;
            color: white;
            font-size: 12px;
        }

        tfoot {
            display: table-footer-group
        }

        #table-detail {
            width: 100%;
            margin-top: -10%;
            border-collapse: collapse;
        }

        #table-detail tr {
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
                    <img src="{{public_path('fileStore/logo/' . $pharmacy['logo'])}}" />
                @endif
            </div>
        </div>
    </div>
    <div class="row" style="padding-top: -2%">
        <h1 align="center">{{$pharmacy['name']}}</h1>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['address']}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['phone']}}</h3>
        <h3 align="center" style="margin-top: -1%">{{$pharmacy['email'] . ' | ' . $pharmacy['website']}}</h3>
        <h2 align="center" style="margin-top: -1%">Dead Stock Report</h2>
        <h3 align="center" style="margin-top: -1%">Branch: {{$store}}</h3>

        <div class="row" style="margin-top: 8%;">
            <div class="col-md-12">
                <table id="table-detail" align="center">
                    <thead>
                        <tr>
                            <th>#</th>
                            {{-- <th>Code</th> --}}
                            <th>Product Name</th>
                            {{-- <th>Category</th> --}}
                            {{-- <th>Batch #</th> --}}
                            {{-- <th style="text-align: center">Expiry Date</th> --}}
                            <th style="text-align: center">Quantity</th>
                        </tr>
                    </thead>
                    {{-- @dd($data) --}}
                    @foreach($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}.</td>
                            {{-- <td>{{ $item['product_id'] }}</td> --}}
                            <td>{{$item->name}} {{$item->brand ?? ''}}
                                {{$item->pack_size ?? ''}}{{$item->sales_uom ?? ''}}
                            </td>
                            {{-- <td align="">{{$item['category']}}</td> --}}
                            {{-- <td align="">{{$item['batch_number']}}</td> --}}
                            {{-- @if($item['expiry_date'] === null)
                            <td align="center"></td>
                            @else
                            <td align="center">{{date('Y-m-d', strtotime($item['expiry_date']))}}</td>
                            @endif --}}
                            <td align="center">
                                {{number_format($item->quantity, 0)}}
                            </td>
                        </tr>
                    @endforeach
                    {{-- @endforeach--}}
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