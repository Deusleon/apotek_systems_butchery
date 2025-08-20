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
    <li class="breadcrumb-item"><a href="#"> Inventory / Current Stock </a></li>
@endsection

@section("content")


    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="current-stock-tablist" data-toggle="pill"
                    href="{{ url('inventory/current-stocks') }}" role="tab" aria-controls="current-stock"
                    aria-selected="true">Current Stock</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="all-stock-tablist" data-toggle="pill"
                    href="{{ url('inventory/current-stock-value') }}" role="tab" aria-controls="stock_list"
                    aria-selected="false">Current Stock Value
                </a>
            </li>
            {{-- <li class="nav-item">--}}
                {{-- <a class="nav-link text-uppercase" id="old-stock-tablist" data-toggle="pill" --}} {{--
                    href="{{ url('inventory/old-stocks') }}" role="tab" --}} {{-- aria-controls="stock_list"
                    aria-selected="false">Old Value--}}
                    {{-- </a>--}}
                {{-- </li>--}}
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="form-group row d-flex">
                    <div class="col-md-6">
                        <label for="stock_status" class="col-form-label text-md-right">Status:</label>
                        <select name="stock_status" class="js-example-basic-single form-group" id="stock_status_id">
                            <option name="store_name" value="all">All</option>
                            <option name="store_name" value="1">In Stock</option>
                            <option name="store_name" value="0">Out Of Stock</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="col-form-label text-md-left">Type:</label>

                        <select name="category" class="js-example-basic-single form-control" id="category_id">
                            <option name="store_name" value="1">Summary</option>
                            <option name="store_name" value="0">Detailed</option>
                        </select>
                    </div>
                </div>
                <!-- main table -->
                {{--All Summary--}}
                <div class="table-responsive" id="all_summary_stocks">
                    {{--Summary--}}
                    <table id="all_summary" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%; font-size: 14px;">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th hidden>Pack Size</th>
                                <th>Quantity</th>
                                <th hidden>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($allStocks as $allstock)
                                <tr>
                                    <td id="name_{{ $allstock->product_id }}">
                                        {{ $allstock->name }}
                                        {{ $allstock->brand ? ' ' . $allstock->brand : '' }}
                                        {{ $allstock->pack_size ?? '' }}{{ $allstock->sales_uom ?? '' }}
                                    </td>
                                    <td id="category_{{ $allstock->product_id }}">{{ $allstock->cat_name }}</td>
                                    <td id="pack_size_{{ $allstock->product_id }}" hidden>{{ $allstock->pack_size }}</td>
                                    <td id="quantity_{{ $allstock->product_id }}">{{ number_format($allstock->quantity) }}</td>
                                    <td id="actions_{{ $allstock->product_id }}" hidden>
                                        @if(auth()->user()->checkPermission('Manage Current Stock'))
                                            <button type="button" class="btn btn-primary btn-rounded btn-sm"
                                                onclick="editStock({{ $allstock->product_id }})">
                                                Edit
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>

                {{--Detailed--}}
                <div class="table-responsive" id="all_detailed_stock" style="display: none;">
                    {{--Detailed--}}
                    <table id="all_detailed" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%; font-size: 14px;">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Batch Number</th>
                                <th>Expire Date</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($allDetailed as $allDet)
                                <tr>
                                    <td id="d_name_{{ $allDet->product_id }}">
                                        {{ $allDet->name }}
                                        {{ $allDet->brand ? ' ' . $allDet->brand : '' }}
                                        {{ $allDet->pack_size ?? '' }}{{ $allDet->sales_uom ?? '' }}
                                    </td>
                                    <td id="d_stock_value_{{ $allDet->product_id }}">
                                        {{ $allDet->cat_name }}
                                    </td>
                                    <td id="d_batch_{{ $allDet->product_id }}">{{ $allDet->batch_number ?? '' }}</td>
                                    <td id="d_expiry_{{ $allDet->product_id }}">{{ $allDet->expiry_date ?? '' }}</td>
                                    <td id="d_quantity_{{ $allDet->product_id }}">
                                        {{ floor($allDet->quantity) == $allDet->quantity ? number_format($allDet->quantity, 0) : number_format($allDet->quantity, 1) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                {{--In stock Summary--}}
                <div class="table-responsive" id="summary" style="display: none;">
                    {{--Summary--}}
                    <table id="current_stock" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%; font-size: 14px;">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th hidden>Pack Size</th>
                                <th>Quantity</th>
                                <th hidden>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($stocks as $stock)
                                <tr>
                                    <td id="name_{{ $stock->product_id }}">
                                        {{ $stock->name }}
                                        {{ $stock->brand ? ' ' . $stock->brand : '' }}
                                        {{ $stock->pack_size ?? '' }}{{ $stock->sales_uom ?? '' }}
                                    </td>
                                    <td id="category_{{ $stock->product_id }}">{{ $stock->cat_name }}</td>
                                    <td id="pack_size_{{ $stock->product_id }}" hidden>{{ $stock->pack_size }}</td>
                                    <td id="quantity_{{ $stock->product_id }}">{{ number_format($stock->quantity) }}</td>
                                    <td id="actions_{{ $stock->product_id }}" hidden>
                                        @if(auth()->user()->checkPermission('Manage Current Stock'))
                                            <button type="button" class="btn btn-primary btn-rounded btn-sm"
                                                onclick="editStock({{ $stock->product_id }})">
                                                Edit
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>

                {{--Instock--}}
                <div class="table-responsive" id="detailed" style="display: none;">
                    {{--Detailed--}}
                    <table id="current_stock_detailed" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%; font-size: 14px;">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Batch Number</th>
                                <th>Expire Date</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($detailed as $data)
                                <tr>
                                    <td id="d_name_{{ $data->product_id }}">
                                        {{ $data->name }}
                                        {{ $data->brand ? ' ' . $data->brand : '' }}
                                        {{ $data->pack_size ?? '' }}{{ $data->sales_uom ?? '' }}
                                    </td>
                                    <td id="d_stock_value_{{ $data->product_id }}">
                                        {{ $data->cat_name }}
                                    </td>
                                    <td id="d_batch_{{ $data->product_id }}">{{ $data->batch_number ?? '' }}</td>
                                    <td id="d_expiry_{{ $data->product_id }}">{{ $data->expiry_date ?? '' }}</td>
                                    <td id="d_quantity_{{ $data->product_id }}">
                                        {{ floor($data->quantity) == $data->quantity ? number_format($data->quantity, 0) : number_format($data->quantity, 1) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                {{--Outstock --}}
                <div class="table-responsive" id="outstock" style="display: none;">
                    {{--Outstock Summary--}}
                    <table id="current_stock_out" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%; font-size: 14px;">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($outstock as $out)
                                <tr>
                                    <td id="o_name_{{ $out->product_id }}">
                                        {{ $out->name }}
                                        {{ $out->brand ? ' ' . $out->brand : '' }}
                                        {{ $out->pack_size ?? '' }}{{ $out->sales_uom ?? '' }}
                                    </td>
                                    <td id="o_name_{{ $out->product_id }}">
                                        {{ $out->cat_name }}
                                    </td>

                                    <td id="o_quantity_{{ $out->product_id }}">
                                        {{ floor($out->quantity) == $out->quantity ? number_format($out->quantity, 0) : number_format($out->quantity, 1) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="table-responsive" id="outstock_detailed" style="display: none;">
                    {{--Outstock Detailed--}}
                    <table id="current_stock_out_detailed" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%; font-size: 14px;">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Batch Number</th>
                                <th>Expire Date</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($outstock as $out)
                                <tr>
                                    <td id="o_detal_name_{{ $out->product_id }}">
                                        {{ $out->name }}
                                        {{ $out->brand ? ' ' . $out->brand : '' }}
                                        {{ $out->pack_size ?? ''}}{{ $out->sales_uom ?? '' }}
                                    </td>
                                    <td id="o_name_{{ $out->product_id }}">
                                        {{ $out->cat_name }}
                                    </td>
                                    <td id="o_detal_batch_{{ $out->product_id }}">{{ $out->batch_number ?? '' }}</td>
                                    <td id="o_detal_expiry_{{ $out->product_id }}">{{ $out->expiry_date ?? '' }}</td>
                                    <td id="o_detal_quantity_{{ $out->product_id }}">
                                        {{ floor($out->quantity) == $out->quantity ? number_format($out->quantity, 0) : number_format($out->quantity, 1) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>


            </div>
        </div>
    </div>
    </div>

@endsection

@include('partials.notification')

@push("page_scripts")
    <script>
        $(document).ready(function () {

            document.getElementById("detailed").style.display = "none";
            document.getElementById("outstock").style.display = "none";
            document.getElementById("outstock_detailed").style.display = "none";

            $('#all_summary').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ]
            });

            $('#all_detailed').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ]
            });

            $('#current_stock').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ]
            });

            $('#current_stock_detailed').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ]
            });

            $('#current_stock_out').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ]
            });

            $('#current_stock_out_detailed').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ]
            });

            if (!$.fn.DataTable.isDataTable('#current_stock')) {
                $('#current_stock').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('current-stocks-filter') }}",
                        "dataType": "json",
                        "type": "post",
                        "cache": false,
                        "data": function (d) {
                            // Use dynamic data here
                            var es = document.getElementById("category_id");
                            var value_es = es.options[es.selectedIndex].value;
                            d._token = "{{csrf_token()}}";
                            d.category = value_es;
                        },
                        success: function (response) {
                            console.log('Current Stock loading...', response);
                            for (var i = 0; i < response.length; i++) {
                                var data_returned = response[i];
                                $('#name_' + data_returned.id).text(data_returned.name);
                                $('#brand_' + data_returned.id).text(data_returned.brand);
                                $('#pack_size_' + data_returned.id).text(data_returned.pack_size);
                                $('#quantity_' + data_returned.id).text(data_returned.quantity);
                            }
                        },
                        error: function (error) {
                            console.error('Error fetching users:', error);
                        }
                    }
                });
            }

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

        const $stockStatus = $('#stock_status_id');
        const $category = $('#category_id');

        function showStockView(status, type) {
            $('#all_summary, #all_detailed, #current_stock, #current_stock_detailed, #current_stock_out, #current_stock_out_detailed').hide();
            $('#all_summary_stocks, #all_detailed_stock, #summary, #detailed, #outstock, #outstock_detailed').hide();

            if (status === "all" && type == 1) {
                $('#all_summary_stocks').show();
                $('#all_summary').show();
            } else if (status === "all" && type == 0) {
                $('#all_detailed_stock').show();
                $('#all_detailed').show();
            } else if (status == 1 && type == 1) {
                $('#summary').show();
                $('#current_stock').show();
            } else if (status == 1 && type == 0) {
                $('#detailed').show();
                $('#current_stock_detailed').show();
            } else if (status == 0 && type == 1) {
                $('#outstock').show();
                $('#current_stock_out').show();
            } else if (status == 0 && type == 0) {
                $('#outstock_detailed').show();
                $('#current_stock_out_detailed').show();
            }
        }

        $(document).on('change', '#stock_status_id, #category_id', function () {
            showStockView($stockStatus.val(), $category.val());
        });

        showStockView($stockStatus.val(), $category.val());

        function formatNumber(num) {
            if (num === null || num === undefined || num === '') return '';
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }


    </script>
@endpush