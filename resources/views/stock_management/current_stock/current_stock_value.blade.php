@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Current Stock
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Current Stock / Current Stock Value</a></li>
@endsection

@section("content")


    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="current-stock-tablist" data-toggle="pill"
                    href="{{ url('inventory/current-stocks') }}" role="tab" aria-controls="current-stock"
                    aria-selected="true">Current Stock</a>
            </li>
            @if (auth()->user()->checkPermission('View Current Stock Value'))
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" id="all-stock-tablist" data-toggle="pill"
                        href="{{ url('inventory/current-stock-value') }}" role="tab" aria-controls="stock_list"
                        aria-selected="false">Current Stock Value
                    </a>
                </li>
            @endif
            @if (auth()->user()->checkPermission('View OLd Stock Value'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="old-stock-tablist" data-toggle="pill"
                        href="{{ url('inventory/old-stocks') }}" role="tab" aria-controls="stock_list" aria-selected="false">Old
                        Stock Value
                    </a>
                </li>
            @endif
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-3">
                    <form method="POST" action="{{ route('current_stock_value') }}" class="d-flex align-items-center"
                        style="width: 281px;">
                        @csrf
                        <label for="price_category" class="form-label mb-0"
                            style="white-space: nowrap; margin-right: 8px;">Price Type:</label>
                        <select name="price_category" id="price_category" class="form-control"
                            onchange="this.form.submit()">
                            @foreach($price_categories as $price_category)
                                <option value="{{ $price_category->id }}" {{ $price_category->id == request('price_category', 1) ? 'selected' : '' }}>
                                    {{ $price_category->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="table-responsive" id="summary">
                    {{--Summary--}}
                    <table id="current_stock" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%">

                        <thead>
                            <tr>
                                <th>Product Name</th> 
                                <th style="text-align: center">Quantity</th>
                                <th style="text-align: right">Buy Price</th>
                                <th style="text-align: right">Sell Price</th>
                                <th style="text-align: right">Total Buy</th>
                                <th style="text-align: right">Total Sell</th>
                                <th style="text-align: right" hidden>Total Profit</th>
                                <th style="text-align: right" hidden>%Profit</th>

                            </tr>
                        </thead>

                        <tbody>
                            {{-- @dd($stocks) --}}
                            @foreach ($stocks as $stock)
                                                    {{-- @if($stock->buying_price > 0) --}}
                                                    <tr>
                                                        <td style="width: 40%; white-space: pre-line;" id="name_{{ $stock->product_id }}">
                                                            {{ trim(
                                    ($stock->name ?? '') .
                                    (!empty($stock->brand) ? ' ' . $stock->brand : '') .
                                    (!empty($stock->pack_size) ? ' ' . $stock->pack_size : '') .
                                    (!empty($stock->sales_uom) ? '' . $stock->sales_uom : '')
                                ) }}
                                                        </td>
                                                        <td style="text-align: center;" id="quantity_{{ $stock->product_id }}">
                                                            {{ floor($stock->quantity) == $stock->quantity ? number_format($stock->quantity, 0) : number_format($stock->quantity, 1) }}
                                                        </td>
                                                        <td style="text-align: right" id="unitcost_{{ $stock->product_id }}">{{ number_format($stock->unit_cost, 2) ?? '' }}
                                                        </td>
                                                        <td style="text-align: right" id="price_{{ $stock->product_id }}">{{ number_format($stock->price, 2) ?? '' }}</td>
                                                        <td style="text-align: right" id="buy_{{ $stock->product_id }}">{{ number_format($stock->buying_price, 2) ?? '' }}
                                                        </td>
                                                        <td style="text-align: right" id="sell_{{ $stock->product_id }}">{{ number_format($stock->selling_price, 2) ?? '' }}
                                                        </td>
                                                        <td style="text-align: right" id="profit_{{ $stock->product_id }}" hidden>{{ number_format($stock->profit, 2) ?? '' }}
                                                        </td>
                                                        <td style="text-align: right" id="percent_profit{{ $stock->product_id }}" hidden>
                                                            @if($stock->buying_price > 0)
                                                                {{  number_format($stock->profit / $stock->buying_price * 100, 0) ?? '' }}
                                                            @else
                                                                {{  number_format($stock->profit) ?? '' }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    {{-- @endif --}}
                            @endforeach
                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>
    </div>

@endsection

@push("page_scripts")
    <script>
        //Datatable and Tabs managed here
        $(document).ready(function () {

            $('#current_stock').DataTable({
                responsive: false,
                order: [[0, 'asc']]
            });

            $('#current-stock-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#old-stock-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#all-stock-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });
        });

        //Table Data managed here
        function loadStockValues() {
            var dates = document.querySelector('input[name=adjustment-date]').value;
            dates = dates.split('-');

            $(document).ready(function () {

                var table = $('#current_stock').DataTable();

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    url: 'https://bensagrostar.net/inventory/filtered_values',
                    type: 'POST',
                    data: {
                        _token: "{{csrf_token()}}",
                        date_from: dates[0],
                        date_to: dates[1]
                    },
                    success: function (response) {

                        console.log('Current Stock loading...', response)

                        $('#current_stock tbody tr').hide();
                        $('#current_stock tbody').empty();

                        table.clear().draw();

                        response.forEach(function (data_returned) {
                            var roundedQuantity = Math.round(data_returned.quantity);

                            // Construct the new row as an array for DataTable
                            var newRow = [
                                data_returned.name || '',
                                roundedQuantity.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
                                data_returned.unit_cost.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '',
                                data_returned.price.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '',
                                data_returned.buying_price.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '',
                                data_returned.selling_price.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '',
                                data_returned.profit.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || ''
                            ];

                            // Append the new row to the DataTable
                            table.row.add(newRow).draw();
                        });

                    },
                    error: function (error) {
                        console.error('Error fetching users:', error)
                    }
                });
            });
        }

        //Calender managed here
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#adjustment-date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#adjustment-date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                maxDate: end,
                autoUpdateInput: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);

        });

    </script>
@endpush