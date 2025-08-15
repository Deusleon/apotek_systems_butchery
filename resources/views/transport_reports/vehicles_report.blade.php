<!DOCTYPE html>
<html>
<head>
    <title>Vehicles Report</title>
    <style>
        body {
            font-size: 12px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table, th, td {
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
            width: 100%;
            margin-top: -10%;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        h3 {
            font-weight: normal;
        }

        h4 {
            font-weight: normal;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="row" style="padding-top: -2%">
    @if($pharmacy['logo'])
        <div style="text-align: center; margin-bottom: 10px;">
            <img src="{{ $pharmacy['logo'] }}" style="height: 60px;">
        </div>
    @endif
    <h1 align="center">{{ $pharmacy['name'] }}</h1>
    <h3 align="center" style="margin-top: -1%">{{ $pharmacy['address'] }}</h3>
    <h3 align="center" style="margin-top: -1%">{{ $pharmacy['phone'] }}</h3>
    @if($pharmacy['email'] || $pharmacy['website'])
    <h3 align="center" style="margin-top: -1%">
        {{ $pharmacy['email'] }}
        @if($pharmacy['email'] && $pharmacy['website']) | @endif
        {{ $pharmacy['website'] }}
    </h3>
    @endif
    @if($pharmacy['tin_number'])
    <h3 align="center" style="margin-top: -1%">TIN: {{ $pharmacy['tin_number'] }}</h3>
    @endif
    <h2 align="center" style="margin-top: -1%">VEHICLES REPORT</h2>
    
    @if($filter_vehicle || $filter_date_range)
    <h4 align="center" style="margin-top: -1%">
        @if($filter_vehicle) Vehicle: {{ $filter_vehicle }} @endif
        @if($filter_date_range) | Date Range: {{ $filter_date_range }} @endif
    </h4>
    @endif
    
    <div class="row" style="margin-top: 5%;">
        <div class="col-md-12">
            <table id="table-detail" align="center">
                <thead>
                <tr style="background: #1f273b; color: white;">
                    <th>Plate No.</th>
                    <th>Transporter</th>
                    <th>Type</th>
                    <th>Make/Model</th>
                    <th class="text-right">Capacity</th>
                    <th>Total Orders</th>
                    <th>Completed</th>
                    <th class="text-right">Total Revenue</th>
                    <!-- <th>Status</th> -->
                </tr>
                </thead>
                <tbody>
                @foreach($vehicles as $vehicle)
                    <tr>
                        <td>{{ $vehicle->plate_number }}</td>
                        <td>{{ $vehicle->transporter->name ?? 'N/A' }}</td>
                        <td>{{ $vehicle->vehicle_type }}</td>
                        <td>{{ $vehicle->make }} {{ $vehicle->model }}</td>
                        <td class="text-right">{{ $vehicle->capacity }}</td>
                        <td>{{ $vehicle->total_orders }}</td>
                        <td>{{ $vehicle->completed_orders }}</td>
                        <td class="text-right">{{ number_format($vehicle->total_revenue, 2) }}</td>
                        <!-- <td>{{ ucfirst($vehicle->status) }}</td> -->
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #1f273b; color: white;">
                        <td colspan="5"><strong>Total</strong></td>
                        <td><strong>{{ $vehicles->sum('total_orders') }}</strong></td>
                        <td><strong>{{ $vehicles->sum('completed_orders') }}</strong></td>
                        <td class="text-right"><strong>{{ number_format($vehicles->sum('total_revenue'), 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <!-- Recent Orders Section for each vehicle -->
            <!-- @foreach($vehicles as $vehicle)
                @if($vehicle->transportOrders->count() > 0)
                <div style="margin-top: 20px; page-break-inside: avoid;">
                    <h4>Recent Orders for {{ $vehicle->plate_number }}</h4>
                    <table style="width: 100%; margin-top: 10px;">
                        <thead>
                            <tr style="background: #1f273b; color: white;">
                                <th>Order ID</th>
                                <th>Pickup Date</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Status</th>
                                <th class="text-right">Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicle->transportOrders as $order)
                                <tr>
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->pickup_date }}</td>
                                    <td>{{ $order->from_location }}</td>
                                    <td>{{ $order->to_location }}</td>
                                    <td>{{ ucfirst($order->status) }}</td>
                                    <td class="text-right">{{ number_format($order->transport_rate, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            @endforeach -->
        </div>
    </div>
</div>

<div style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px;">
    <p>Generated on: {{ date('F j, Y H:i:s') }}</p>
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