@extends("layouts.master")

@section('page_css')
    <style>
        .badge {
            padding: 0.4em 0.8em;
            font-size: 85%;
            font-weight: 600;
            border-radius: 0.25rem;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
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

    <style>
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        #select1 {
            z-index: 10050;
        }

        #loading {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            display: none;
            opacity: 0.7;
            background-color: #fff;
            z-index: 99;
            text-align: center;
        }

        #loading-image {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 100;
        }

        .select2-container {
            width: 103% !important;
        }

    </style>

    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="current-stock-tablist" data-toggle="pill"
                   href="{{ url('inventory/stock-adjustment') }}" role="tab"
                   aria-controls="current-stock" aria-selected="true">Current Stock</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="old-stock-tablist" data-toggle="pill"
                   href="#old-stock" role="tab"
                   aria-controls="stock_list" aria-selected="false">Old Stock
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="all-stock-tablist" data-toggle="pill"
                   href="#all-stock" role="tab"
                   aria-controls="stock_list" aria-selected="false">All Stock
                </a>
            </li>
        </ul>
        <div class="card">
            <div class="card-body">

                <div class="form-group row d-flex">
                    <div class="col-md-4">
                        <label for="stock_status" class="col-form-label text-md-right"
                        >Store:</label>
                        <select name="stores_id" class="js-example-basic-single form-control"
                                id="stores_id">
                            @foreach($stores as $store)
                                <option
                                    value="{{$store->id}}" {{$default_store_id === $store->id  ? 'selected' : ''}}>{{$store->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4" >
                        <label for="stock_status" class="col-form-label text-md-right"
                        >Status:</label>
                        <select name="stock_status" class="js-example-basic-single form-group"
                                id="stock_status_id">
                            <option name="store_name" value="1">In Stock</option>
                            <option name="store_name" value="0">Out Of Stock</option>
                        </select>
                    </div>

                    <div class="col-md-4">

                       <label for="category" class="col-form-label text-md-left"
                                  >Category:</label>

                        <select name="category" class="js-example-basic-single form-control" id="category_id">
                            <option readonly value="0" id="store_name_edit" disabled
                                    selected>Select Category...
                            </option>
                            @foreach($categories as $category)
                                <option name="store_name" value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- main table -->
                <div id="tbody1" class="table-responsive">
                    <table id="fixed-header-main" class="display table nowrap table-striped table-hover"
                           style="width:100%">

                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Pack Size</th>
                            <th>Quantity</th>
                            <th>Stock Value</th>
                            <th>Expire Date</th>
                            <th>Batch Number</th>
                            <th>Stock Status</th>
                            <th>Category</th>
                            <th>Alert Status</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($stocks as $stock)
                            <tr>
                                <td>{{ $stock->product->name }}</td>
                                <td>{{ $stock->product->pack_size }}</td>
                                <td>{{ $stock->quantity }}</td>
                                <td>{{ number_format($stock->stock_value, 2) }}</td>
                                <td>{{ $stock->expire_date }}</td>
                                <td>{{ $stock->batch_number }}</td>
                                <td>{{ $stock->stock_status }}</td>
                                <td>{{ $stock->product->category->name }}</td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'critical' => 'badge badge-danger',
                                            'low' => 'badge badge-warning',
                                            'normal' => 'badge badge-success'
                                        ][$stock->status];
                                    @endphp
                                    <span class="{{ $statusClass }}">
                                        {{ ucfirst($stock->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $stocks->links() }}
                    </div>
                </div>


                <!-- ajax loading image -->
                <div id="loading">
                    <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                </div>


            </div>
        </div>
    </div>
    </div>

@endsection


@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>
    <script src="{{ mix('js/app.js') }}"></script>

    @include('partials.notification')

    <script>
        // Initialize real-time stock updates
        const storeId = document.getElementById('stores_id').value;
        const stockUpdates = new StockUpdates(storeId);

        // Listen for store changes
        document.getElementById('stores_id').addEventListener('change', function() {
            window.location.href = `${window.location.pathname}?store_id=${this.value}`;
        });

        var role = 0;
        let page_pricing_flag = 0;

        $('#stock_status_id').on('change', function (e) {
            stockStatus();
        });

        $('#category_id').on('change', function () {
            category();
        });

        $('#stores_id').on('change', function () {
            stores();
        });


        function loadInStock() {
            var e = document.getElementById("stock_status_id");
            var value = e.options[e.selectedIndex].value;

            var es = document.getElementById("category_id");
            var value_es = es.options[es.selectedIndex].value;

            var es_id = document.getElementById("stores_id");
            var value_es_id = es_id.options[es_id.selectedIndex].value;

            $("#fixed-header1").dataTable().fnDestroy();
            $('#fixed-header1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('all-in-stock') }}",
                    "dataType": "json",
                    "type": "post",
                    "cache": false,
                    "data": {
                        _token: "{{csrf_token()}}",
                        status: value,
                        category: value_es,
                        store_id: value_es_id
                    },
                    complete: function () {
                        document.getElementById("tbody1").style.display = 'none';
                        document.getElementById("tbody").style.display = 'block';
                        document.getElementById("tbody_stock_status").style.display = 'none';
                    }
                },
                "columns": [
                    {"data": "name"},
                    {
                        "data": "quantity", render: function (data) {
                            return numberWithCommas(data);
                        }
                    }
                ]

            });
        }

        $(document).ready(function () {

            var e = document.getElementById("stock_status_id");
            var value = e.options[e.selectedIndex].value;

            var es_id = document.getElementById("stores_id");
            var value_es_id = es_id.options[es_id.selectedIndex].value;

            $('#fixed-header-main').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('all-in-stock') }}",
                    "dataType": "json",
                    "type": "post",
                    "cache": false,
                    "data": {
                        _token: "{{csrf_token()}}",
                        status: value,
                        store_id: value_es_id
                    }
                },
                "columns": [
                    {"data": "name"},
                    {
                        "data": "quantity", render: function (data) {
                            return numberWithCommas(data);
                        }
                    },
                    {'data': 'expiry_date'},
                    {'data': 'batch_number'},
                ]
            });

        });

        $('#edit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            if (event.relatedTarget === undefined) return;

            modal.find('.modal-body #id').val(button.data('id'));
            modal.find('.modal-body #name_edit').val(button.data('product'));
            modal.find('.modal-body #d_auto_6').val(button.data('expiry'));
            modal.find('.modal-body #quantity_edit').val(button.data('quantity'));
            modal.find('.modal-body #unit_cost_edit').val(button.data('unit'));
            modal.find('.modal-body #batch_no').val(button.data('batch'));
            modal.find('.modal-body #store_name_edit').val(button.data('store'));
            modal.find('.modal-body #shelf_number_edit').val(button.data('shelf'));
            // modal.find('.modal-body #rack_number_edit').val(button.data('rack'))
            modal.find('.modal-body #store_id').val(button.data('store_id'));
            modal.find('.modal-body #product_id').val(button.data('product_id'))
        });

        $('#show').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body #id').text(button.data('id'));
            modal.find('.modal-body #name_edit').text(button.data('product'));
            modal.find('.modal-body #d_auto_6').text(button.data('expiry'));
            modal.find('.modal-body #quantity_edit').text(button.data('quantity'));
            modal.find('.modal-body #unit_cost_edit').text(button.data('unit'));
            modal.find('.modal-body #batch_no').text(button.data('batch'));
            modal.find('.modal-body #store_name_edit').text(button.data('store'));
            modal.find('.modal-body #shelf_number_edit').text(button.data('shelf'))
            // modal.find('.modal-body #rack_number_edit').text(button.data('rack'))
        });

        $('#create').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body #id').val(button.data('id'));
            modal.find('.modal-body #name_edit').val(button.data('product'));
            modal.find('.modal-body #product_id').val(button.data('product_id'));
            modal.find('.modal-body #quantity_in_edit').val(button.data('quantity'));
            modal.find('.modal-body #unit_cost_edit_').val(button.data('unit'))
        });

        var table_detail = $('#fixed-header-price').DataTable({
                bInfo: false,
                'columns': [
                    {'data': 'product.name'},
                    {
                        'data': 'quantity', render: function (data) {
                            return numberWithCommas(data);
                        }
                    },
                    {'data': 'expiry_date'},
                    {'data': 'batch_number'},
                        @if(auth()->user()->checkPermission('Stock Adjustment'))
                    {
                        'data': 'action',
                        defaultContent: "<div><button id='edits' class='btn btn-sm btn-rounded btn-primary' type='button'>Edit</button><button id='adjust' class='btn btn-sm btn-rounded btn-secondary' type='button'>Adjust</button></div>"

                    }
                    @endif

                ]
            })
        ;

        function stockStatus() {
            loadInStock();
        }

        function stores() {
            loadInStock();
        }

        function category() {
            loadInStock();
        }

        function priceCategory(page) {
            var category;
            var category_id;
            var stock_id;
            var product_id;
            if (Number(page) !== Number(0)) {
                page_pricing_flag = 1;
                category = document.getElementById("category_");
                category_id = category.options[category.selectedIndex].value;
                stock_id = document.getElementById("stock_id_").value;
                product_id = document.getElementById("product_id_").value;
            } else {
                page_pricing_flag = 0;
                category = document.getElementById("category");
                category_id = category.options[category.selectedIndex].value;
                stock_id = document.getElementById("stock_id").value;
                product_id = document.getElementById("product_id").value;
            }


            /*
             * make ajax call to get the price depending to the price category
             * */

            // $('#loading').show();
            $.ajax({
                url: '{{route('sale-price-category')}}',
                type: "get",
                dataType: "json",
                data: {
                    category_id: category_id,
                    stock_id: stock_id,
                    product_id: product_id
                },
                success: function (data) {
                    if (Number(page_pricing_flag) !== Number(0)) {
                        if (data.length === 0) {
                            $("#sell_price_edit_").val(formatMoney(0));
                        } else {
                            $("#sell_price_edit_").val(formatMoney(data[0]['price']));
                            $("#sales_id_").val(data[0]['id']);
                            if (data[0]['stock_id'] !== null) {
                                $("#stock_id_").val(data[0]['stock_id']);
                            } else {
                                console.log('null');
                            }
                        }
                    } else {
                        if (data.length === 0) {
                            $("#sell_price_edit").val(formatMoney(0));
                        } else {
                            $("#sell_price_edit").val(formatMoney(data[0]['price']));
                            $("#sales_id").val(data[0]['id']);
                            if (data[0]['stock_id'] !== null) {
                                $("#stock_id").val(data[0]['stock_id']);
                            } else {
                                console.log('null');
                            }
                        }
                    }

                },
                complete: function () {
                    // $('#loading').hide();
                }
            });

        }

        $(document).ready(function () {
            var table_main = $('#fixed-header-main').DataTable();

            $('#tbody1').on('click', '#detail', function () {
                var data = table_main.row($(this).parents('tr')).data();
                retriveStockDetail(data.product_id);
            });
            $('#tbody1').on('click', '#details', function () {
                var data = table_main.row($(this).parents('tr')).data();
                retriveStockDetail(data.product_id);
            });

            $('#tbody1').on('click', '#pricing_', function () {
                var data = table_main.row($(this).parents('tr')).data();
                retrivePricing(data.product_id);

            });

            $('#tbody1').on('click', '#bulk_adjust_', function () {
                var data = table_main.row($(this).parents('tr')).data();
                retrivePricing(data.product_id, 'bulk_adjust');

            });

        });

        $('#tbody').on('click', '#detail', function () {
            var data = $('#fixed-header1').DataTable().row($(this).parents('tr')).data();
            retriveStockDetail(data.product_id);
        });

        $('#tbody').on('click', '#pricing', function () {
            var data = $('#fixed-header1').DataTable().row($(this).parents('tr')).data();
            retrivePricing(data.product_id);
        });

        $('#tbody').on('click', '#bulk_adjust', function () {
            var data = $('#fixed-header1').DataTable().row($(this).parents('tr')).data();
            retrivePricing(data.product_id, 'bulk_adjust');
        });


        $('#tbody_stock_status').on('click', '#adjust', function () {
            var data = table_detail.row($(this).parents('tr')).data();
            $('#stock_detail').modal('hide');
            $('#create').modal('show');
            $('#create').find('.modal-body #id').val(data.id);
            $('#create').find('.modal-body #name_edit').val(data.product.name);
            $('#create').find('.modal-body #product_id').val(data.product_id);
            $('#create').find('.modal-body #quantity_in_edit').val(numberWithCommas(data.quantity));
            $('#create').find('.modal-body #unit_cost_edit_').val(formatMoney(data.unit_cost));

        });


        $('#tbody_stock_status').on('click', '#edits', function () {
            var data = table_detail.row($(this).parents('tr')).data();

            $('#stock_detail').modal('hide');
            /*
             * retrieve perspective stock id and the price
             * */
            $('#edit').modal('show');
            $('#edit').find('.modal-body #id').val(data.id);
            $('#edit').find('.modal-body #name_edit').val(data.product.name);
            $('#edit').find('.modal-body #d_auto_6').val(data.expiry_date);
            $('#edit').find('.modal-body #quantity_edit').val(numberWithCommas(data.quantity));
            $('#edit').find('.modal-body #unit_cost_edit').val(formatMoney(data.unit_cost));
            $('#edit').find('.modal-body #batch_no').val(data.batch_number);
            $('#edit').find('.modal-body #store_name_edit').val(data.price_category_id);
            $('#edit').find('.modal-body #shelf_number_edit').val(data.shelf_number);
            $('#edit').find('.modal-body #sell_price_edit').val(data.selling_price);
            $('#edit').find('.modal-body #store_id').val(data.store_id);
            $('#edit').find('.modal-body #sales_id').val(data.sales_id);
            $('#edit').find('.modal-body #product_id').val(data.product_id);
            $('#edit').find('.modal-body #stock_id').val(data.id);
        });

        function retriveStockDetail(data) {
            var val = data;

            var es_id = document.getElementById("stores_id");
            var value_es_id = es_id.options[es_id.selectedIndex].value;

            var ajaxurl = '{{route('current-stock-detail')}}';

            // $('#loading').show();
            $.ajax({
                url: ajaxurl,
                type: "get",
                dataType: "json",
                data: {
                    val: val,
                    store_id: value_es_id
                },
                success: function (data) {

                    document.getElementById("tbody1").style.display = 'none';
                    document.getElementById("tbody").style.display = 'none';
                    document.getElementById("tbody_stock_status").style.display = 'block';
                    bindDetailData(data);

                },
                complete: function () {
                    // $('#loading').hide();
                }
            });
        }

        function retrivePricing(data, bulk_adjust) {
            var val = data;

            var es_id = document.getElementById("stores_id");
            var value_es_id = es_id.options[es_id.selectedIndex].value;

            var ajaxurl = '{{route('current-pricing')}}';

            // $('#loading').show();
            $.ajax({
                url: ajaxurl,
                type: "get",
                dataType: "json",
                data: {
                    val: val,
                    store_id: value_es_id,
                    bulk_adjust: bulk_adjust
                },
                success: function (data) {
                    if (bulk_adjust) {
                        popAdjustModel(data);
                    } else {
                        popPricingModel(data);
                    }
                }
            });
        }

        function popPricingModel(data) {
            $('#price_modal').modal('show');
            $('#price_modal').find('.modal-body #name_edit').val(data.product.name);
            $('#price_modal').find('.modal-body #unit_cost_edit_1').val(formatMoney(data.unit_cost));
            $('#price_modal').find('.modal-body #sell_price_edit_').val('');
            $('#category_').val('');
            $('#price_modal').find('.modal-body #product_id_').val(data.product_id);
            $('#price_modal').find('.modal-body #stock_id_').val(data.id);
        }

        function popAdjustModel(data) {
            $('#bulk_adjust_modal').modal('show');
            $('#bulk_adjust_modal').find('.modal-body #name_edit_bulk').val(data[0].name);
            $('#bulk_adjust_modal').find('.modal-body #quantity_in_edit_bulk').val(numberWithCommas(data[0].quantity));
            $('#bulk_adjust_modal').find('.modal-body #unit_cost_edit_bulk').val(formatMoney(data[0].unit_cost));
            $('#bulk_adjust_modal').find('.modal-body #stock_id_bulk').val(data[0].stock_id);
            $('#bulk_adjust_modal').find('.modal-body #product_id_bulk').val(data[0].product_id);
        }

        function bindDetailData(data) {
            table_detail.clear();
            table_detail.rows.add(data);
            table_detail.draw();
            // $('#stock_detail').modal('show');
        }

        function change() {
        }


        function returnMain() {

            loadInStock();

        }

        function isNumberKey(evt, obj) {

            var charCode = (evt.which) ? evt.which : event.keyCode;
            var value = obj.value;
            var dotcontains = value.indexOf(".") !== -1;
            if (dotcontains)
                if (charCode === 46) return false;
            if (charCode === 46) return true;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

        $("#select1").select2({
            dropdownParent: $("#create")
        });

        $('#sell_price_edit').on('change', function () {
            var s_p = document.getElementById('sell_price_edit').value;
            document.getElementById('sell_price_edit').value = formatMoney(s_p);
        });
        $('#sell_price_edit_').on('change', function () {
            var s_p = document.getElementById('sell_price_edit_').value;
            document.getElementById('sell_price_edit_').value = formatMoney(s_p);
        });

        $('#unit_cost_edit').on('change', function () {
            var s_p = document.getElementById('unit_cost_edit').value;
            document.getElementById('unit_cost_edit').value = formatMoney(s_p);
        });
        $('#unit_cost_edit_1').on('change', function () {
            var s_p = document.getElementById('unit_cost_edit_1').value;
            document.getElementById('unit_cost_edit_1').value = formatMoney(s_p);
        });

        $('#update_stock').on('submit', function () {
            var b_p = document.getElementById('unit_cost_edit').value;
            var s_p = document.getElementById('sell_price_edit').value;
            var unit_cost = parseFloat(b_p.replace(/\,/g, ''), 10);
            var price = parseFloat(s_p.replace(/\,/g, ''), 10);

            if (Number(price) < Number(unit_cost)) {

                var r = confirm('Selling price is less than buying price?');
                if (r === true) {
                    /*continue*/
                } else {
                    /*return false*/
                    return false;
                }

            }

        });

        $('#adjust_form').on('submit', function () {
            // var type = document.getElementById('type').value;
            var type_value = document.getElementById('type').value;

            /*check for less or high quantity*/
            var to_adjust = document.getElementById('quantity_edit_').value;
            var quantity_in = document.getElementById('quantity_in_edit').value;

            quantity_in = parseFloat(quantity_in.replace(/\,/g, ''), 10);

            if (type_value === 'Negative') {
                if (Number(to_adjust) > Number(quantity_in)) {
                    notify('Quantity exceeds available stock', 'top', 'right', 'warning');
                    document.getElementById('quantity_edit_').value = to_adjust;
                    return false;
                }
            }

        });

        $('#adjust_form_').on('submit', function () {
            // var type = document.getElementById('type').value;
            var type_value = document.getElementById('type_bulk').value;

            /*check for less or high quantity*/
            var to_adjust = document.getElementById('quantity_edit_bulk').value;
            var quantity_in = document.getElementById('quantity_in_edit_bulk').value;

            quantity_in = parseFloat(quantity_in.replace(/\,/g, ''), 10);

            if (type_value === 'Negative') {
                if (Number(to_adjust) > Number(quantity_in)) {
                    notify('Quantity exceeds available stock', 'top', 'right', 'warning');
                    document.getElementById('quantity_edit_bulk').value = to_adjust;
                    return false;
                }
            }

        });

        $(document).ready(calculate());
        $(document).ready(formatMoney());

        function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
                const negativeSign = amount < 0 ? "-" : "";
                let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
                let j = (i.length > 3) ? i.length % 3 : 0;
                return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
            } catch (e) {

            }
        }

        function numberWithCommas(digit) {
            return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        $(document).ready(function() {
            var table = $('#fixed-header-main').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ url('inventory/current-stock-api') }}",
                    data: function(d) {
                        d.stores_id = $('#stores_id').val();
                        d.status = $('#stock_status_id').val();
                        d.category = $('#category_id').val();
                    }
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'pack_size', name: 'pack_size'},
                    {data: 'quantity', name: 'quantity'},
                    {
                        data: 'stock_value',
                        name: 'stock_value',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            });
                        }
                    },
                    {data: 'expiry_date', name: 'expiry_date'},
                    {data: 'batch_number', name: 'batch_number'},
                    {
                        data: 'stock_status',
                        name: 'stock_status',
                        render: function(data) {
                            var badge = '';
                            switch(data) {
                                case 'In Stock':
                                    badge = 'badge-success';
                                    break;
                                case 'Out of Stock':
                                    badge = 'badge-danger';
                                    break;
                                case 'Low Stock':
                                    badge = 'badge-warning';
                                    break;
                            }
                            return '<span class="badge ' + badge + '">' + data + '</span>';
                        }
                    },
                    {data: 'category_name', name: 'category_name'}
                ],
                order: [[0, 'asc']],
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#stores_id, #stock_status_id, #category_id').change(function() {
                table.ajax.reload();
            });
        });

    </script>
@endpush
