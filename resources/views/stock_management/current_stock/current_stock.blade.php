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
                {{--Summary--}}
                <div class="table-responsive" id="summary">
                    {{--Summary--}}
                    <table id="current_stock" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th hidden>Brand</th>
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
                                        {{ $stock->pack_size ? ' ' . $stock->pack_size : '' }}
                                    </td>
                                    <td id="brand_{{ $stock->product_id }}" hidden>{{ $stock->brand }}</td>
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

                {{--Detailed--}}
                <div class="table-responsive" id="detailed" style="display: none;">
                    {{--Detailed--}}
                    <table id="current_stock_detailed" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%;">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th hidden>Brand</th>
                                <th hidden>Pack Size</th>
                                <th>Quantity</th>
                                <th>Stock Value</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($detailed as $data)
                                <tr>
                                    <td id="d_name_{{ $data->product_id }}">
                                        {{ $data->name }}
                                        {{ $data->brand ? ' ' . $data->brand : '' }}
                                        {{ $data->pack_size ? ' ' . $data->pack_size : '' }}
                                    </td>
                                    <td id="d_brand_{{ $data->product_id }}" hidden>{{ $data->brand ?? '' }}</td>
                                    <td id="d_pack_size_{{ $data->product_id }}" hidden>{{ $data->pack_size ?? '' }}</td>
                                    <td id="d_quantity_{{ $data->product_id }}">{{ floor($data->quantity) == $data->quantity ? number_format($data->quantity,0) : number_format($data->quantity,1) }}
                                    </td>
                                    <td id="d_stock_value_{{ $data->product_id }}">
                                        {{ number_format($data->stock_value ?? 0, 2) ?? '' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                {{--Outstock--}}
                <div class="table-responsive" id="outstock" style="display: none;">
                    {{--Outstock--}}
                    <table id="current_stock_out" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%;">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Expire Date</th>
                                <th>Batch Number</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($outstock as $out)
                                <tr>
                                    <td id="o_name_{{ $out->product_id }}">
                                        {{ $out->name }}
                                        {{ $out->brand ? ' ' . $out->brand : '' }}
                                        {{ $out->pack_size ? ' ' . $out->pack_size : '' }}
                                    </td>
                                    <td id="o_quantity_{{ $out->product_id }}">{{ floor($out->quantity) == $out->quantity ? number_format($out->quantity,0) : number_format($out->quantity,1) }}</td>
                                    <td id="o_batch_{{ $out->product_id }}">{{ $out->batch_number ?? '' }}</td>
                                    <td id="o_expiry_{{ $out->product_id }}">{{ $out->expiry_date ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>


            </div>
        </div>
    </div>
    </div>

    {{-- Edit Stock Modal --}}
    <div class="modal fade" id="editStockModal" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStockModalLabel">Edit Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editStockForm">
                        @csrf
                        <input type="hidden" id="edit_product_id" name="product_id">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Brand</label>
                                <input type="text" class="form-control" id="edit_brand" name="brand">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Pack Size</label>
                                <input type="text" class="form-control" id="edit_pack_size" name="pack_size">
                            </div>
                        </div>
                        <hr>
                        <h5>Stock Items</h5>
                        <div id="stock-items-container"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="updateStock()">Save changes</button>
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

        $('#stock_status_id').on('change', function (e) {
            console.log("Status Changed")
            var stat = document.getElementById("stock_status_id");
            var status = stat.options[stat.selectedIndex].value;

            if (status === "0") {
                $('#current_stock').hide();
                $('#current_stock_detailed').hide();
                document.getElementById("summary").style.display = "none";
                document.getElementById("detailed").style.display = "none";
                document.getElementById("outstock").style.display = "block";
            } else {
                $('#current_stock').show();
                document.getElementById("summary").style.display = "block";
                document.getElementById("detailed").style.display = "none";
                document.getElementById("outstock").style.display = "none";
            }
        });

        $('#category_id').on('change', function () {
            console.log("Category Changed")


            var cat = document.getElementById("category_id");
            var category = cat.options[cat.selectedIndex].value;

            if (category === "1") {
                $('#current_stock').show();
                $('#current_stock_detailed').hide();
                document.getElementById("summary").style.display = "block";
                document.getElementById("detailed").style.display = "none";
                document.getElementById("outstock").style.display = "none";

            }

            if (category === "0") {
                $('#current_stock').hide();
                $('#current_stock_detailed').show();
                document.getElementById("summary").style.display = "none";
                document.getElementById("outstock").style.display = "none";
                document.getElementById("detailed").style.display = "block";
            }

        });



        function loadInStockxx() {
            var cat = document.getElementById("category_id");
            var category = cat.options[cat.selectedIndex].value;
            $(document).ready(function () {
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    url: 'https://bensagrostar.net/inventory/api/current-stocks',
                    type: 'POST',
                    data: {
                        _token: "{{csrf_token()}}",
                        category: category
                    },
                    success: function (response) {

                        console.log('Current Stock loading...', response)

                        for (var i = 0; i < response.length; i++) {

                            var data_returned = response[i];

                            $('#name_' + data_returned.id).text(data_returned.name)
                            $('#batch_' + data_returned.id).text(data_returned.batch_number)
                            $('#quantity_' + data_returned.id).text(data_returned.quantity)
                            $('#expiry_' + data_returned.id).text(data_returned.expiry_date)
                        }


                    },
                    error: function (error) {
                        console.error('Error fetching users:', error)
                    }
                });
            });
        }

        function editStock(productId) {
            $.ajax({
                url: '/inventory/current-stocks/edit/' + productId,
                type: 'GET',
                success: function (response) {
                    $('#edit_product_id').val(productId);
                    $('#edit_brand').val(response.product.brand);
                    $('#edit_pack_size').val(response.product.pack_size);

                    var container = $('#stock-items-container');
                    container.empty(); // Clear previous items

                    if (response.stocks.length > 0) {
                        response.stocks.forEach(function (stock, index) {
                            var itemHtml = `
                                    <div class="stock-item card mb-2">
                                        <div class="card-body">
                                            <input type="hidden" name="items[${index}][id]" value="${stock.id}">
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Batch Number</label>
                                                    <input type="text" class="form-control" name="items[${index}][batch_number]" value="${stock.batch_number}">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Expiry Date</label>
                                                    <input type="date" class="form-control" name="items[${index}][expiry_date]" value="${stock.expiry_date}">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Quantity</label>
                                                    <input type="number" class="form-control" name="items[${index}][quantity]" value="${stock.quantity}">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Unit Cost</label>
                                                    <input type="text" class="form-control" name="items[${index}][unit_cost]" value="${formatNumber(stock.unit_cost)}">
                                                </div>
                                                 <div class="form-group col-md-4">
                                                    <label>Selling Price</label>
                                                    <input type="text" class="form-control" name="items[${index}][sell_price]" value="${formatNumber(stock.price || '')}">
                                                    <input type="hidden" name="items[${index}][sales_id]" value="${stock.sales_id || ''}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                            container.append(itemHtml);
                        });
                    } else {
                        container.html('<p>No stock details found for this product.</p>');
                    }

                    $('#editStockModal').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching stock details:', error);
                    alert('Could not fetch stock details. Please try again.');
                }
            });
        }

        function updateStock() {
            var form = $('#editStockForm');

            // Clone the form to modify values for submission without affecting the UI
            var formClone = form.clone();

            // Find and un-format currency fields in the clone
            formClone.find('input[name*="[unit_cost]"], input[name*="[sell_price]"]').each(function () {
                var unformattedValue = $(this).val().replace(/,/g, '');
                $(this).val(unformattedValue);
            });

            var formData = formClone.serialize();

            $.ajax({
                url: '{{ route("current-stock.update") }}',
                type: 'POST',
                data: formData,
                success: function (response) {
                    $('#editStockModal').modal('hide');
                    // Using a more modern notification is better than alert()
                    alert(response.message);
                    location.reload();
                },
                error: function (xhr) {
                    var response = xhr.responseJSON;
                    var errorMessage = response.message || 'An error occurred.';
                    if (response.errors) {
                        errorMessage += '\n';
                        for (var key in response.errors) {
                            if (response.errors.hasOwnProperty(key)) {
                                // The key might be like 'items.0.quantity', clean it up for display.
                                var cleanKey = key.replace(/items\.\d+\./, '').replace(/_/, ' ');
                                errorMessage += `\n- ${cleanKey.charAt(0).toUpperCase() + cleanKey.slice(1)}: ${response.errors[key].join(', ')}`;
                            }
                        }
                    }
                    alert(errorMessage);
                }
            });
        }

        function formatNumber(num) {
            if (num === null || num === undefined || num === '') return '';
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function loadInStock() {

            var es = document.getElementById("category_id");
            var value_es = es.options[es.selectedIndex].value;

            $(document).ready(function () {

                var table = $('#current_stock').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('current-stocks-filter') }}",
                        "dataType": "json",
                        "type": "post",
                        "cache": false,
                        "data": {
                            _token: "{{csrf_token()}}",
                            category: value_es
                        },
                        success: function (response) {

                            console.log('Current Stock loading...', response)

                            for (var i = 0; i < response.length; i++) {

                                var data_returned = response[i];

                                $('#name_' + data_returned.id).text(data_returned.name)
                                $('#batch_' + data_returned.id).text(data_returned.batch_number)
                                $('#quantity_' + data_returned.id).text(data_returned.quantity)
                                $('#expiry_' + data_returned.id).text(data_returned.expiry_date)
                            }


                        },
                        error: function (error) {
                            console.error('Error fetching users:', error)
                        }
                    }

                });


                $('#category_id').on('change', function () {
                    // Reload DataTable on category change
                    table.ajax.reload(null, false); // false means the table will not reset pagination
                });


            });
        }
    </script>
@endpush