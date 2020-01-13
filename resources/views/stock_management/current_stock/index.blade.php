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
        <div class="card">
            <div class="card-body">

                <div class="form-group row">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-3">
                        <label for="stock_status" class="col-form-label text-md-right"
                               style="margin-left: 86%">Status:</label>
                    </div>
                    <div class="col-md-3" style="margin-left: -0.6%;">
                        <select name="stock_status" class="js-example-basic-single form-control"
                                id="stock_status_id">
                            <option name="store_name" value="1">In Stock</option>
                            <option name="store_name" value="0">Out Of Stock</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-3">
                        <label for="category" class="col-form-label text-md-right"
                               style="margin-left: 77%">Category:</label>
                    </div>
                    <div class="col-md-3" style="margin-left: -0.6%;">
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
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>


                <!-- ajax loading image -->
                <div id="loading">
                    <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                </div>

                <!-- filtering result table -->
                <div id="tbody" style="display: none;" class="table-responsive">
                    <table id="fixed-header1" class="display table nowrap table-striped table-hover"
                           style="width:100%">

                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>

                    </table>
                </div>

                <!-- table stock detail, show many batch -->
                <div id="tbody_stock_status" style="display: none;" class="table-responsive">
                    <table id="fixed-header-price" class="display table nowrap table-striped table-hover"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Expire Date</th>
                            <th>Batch No</th>
                            @can('Stock Adjustment')
                                <th>Actions</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button style="float: right; margin-right: 11.5%; margin-top: 2%"
                            class="btn btn-sm btn-danger btn-rounded"
                            onclick="returnMain()"
                            id="cancel">Back
                    </button>
                </div>

            </div>
        </div>
    </div>
    </div>
    @include('stock_management.current_stock.edit')
    @include('stock_management.current_stock.show')
    @include('stock_management.current_stock.create')
    @include('stock_management.current_stock.stock_detail')

@endsection


@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>

    @include('partials.notification')


    <script>

        var role = 0;

        $('#stock_status_id').on('change', function (e) {
            stockStatus();
        });

        $('#category_id').on('change', function () {
            category();
        });

        function loadInStock() {
            var e = document.getElementById("stock_status_id");
            var value = e.options[e.selectedIndex].value;

            var es = document.getElementById("category_id");
            var value_es = es.options[es.selectedIndex].value;

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
                        category: value_es
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
                    },
                    {
                        "data": "action",
                        defaultContent: "<div><button id='detail' class='btn btn-sm btn-rounded btn-success' type='button'>Details</button></div>"
                    }
                ]

            });
        }

        $(document).ready(function () {

            var e = document.getElementById("stock_status_id");
            var value = e.options[e.selectedIndex].value;

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
                        status: value
                    }
                },
                "columns": [
                    {"data": "name"},
                    {
                        "data": "quantity", render: function (data) {
                            return numberWithCommas(data);
                        }
                    },
                    {
                        "data": "action",
                        defaultContent: "<div><button id='details' class='btn btn-sm btn-rounded btn-success' type='button'>Details</button></div>"
                    }
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
                        @can('Stock Adjustment')
                    {
                        'data': 'action',
                        defaultContent: "<div><button id='edits' class='btn btn-sm btn-rounded btn-primary' type='button'>Edit</button><button id='adjust' class='btn btn-sm btn-rounded btn-secondary' type='button'>Adjust</button></div>"

                    }
                    @endcan

                ]
            })
        ;

        function stockStatus() {
            loadInStock();
        }

        function category() {
            loadInStock();
        }

        function priceCategory() {
            var category = document.getElementById("category");
            var category_id = category.options[category.selectedIndex].value;
            var stock_id = document.getElementById("stock_id").value;
            var product_id = document.getElementById("product_id").value;

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
                },
                complete: function () {
                    // $('#loading').hide();
                }
            });

        }

        $(document).ready(function () {
            var table_main = $('#fixed-header-main').DataTable();

            $('#tbody1').on('click', 'button', function () {
                var data = table_main.row($(this).parents('tr')).data();
                retriveStockDetail(data.product_id);
            });
        });

        $('#tbody').on('click', 'button', function () {
            var data = $('#fixed-header1').DataTable().row($(this).parents('tr')).data();
            retriveStockDetail(data.product_id);
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

            var ajaxurl = '{{route('current-stock-detail')}}';

            $('#loading').show();
            $.ajax({
                url: ajaxurl,
                type: "get",
                dataType: "json",
                data: {val: val},
                success: function (data) {
                    document.getElementById("tbody1").style.display = 'none';
                    document.getElementById("tbody").style.display = 'none';
                    document.getElementById("tbody_stock_status").style.display = 'block';
                    bindDetailData(data);

                },
                complete: function () {
                    $('#loading').hide();
                }
            });
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

        $('#unit_cost_edit').on('change', function () {
            var s_p = document.getElementById('unit_cost_edit').value;
            document.getElementById('unit_cost_edit').value = formatMoney(s_p);
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
            var type = document.getElementById('type');
            var type_value = type.options[type.selectedIndex].value;
            var reason = document.getElementById('reason');
            var reason_value = reason.options[reason.selectedIndex].value;

            /*check for less or high quantity*/
            var to_adjust = document.getElementById('quantity_edit_').value;
            var quantity_in = document.getElementById('quantity_in_edit').value;

            quantity_in = parseFloat(quantity_in.replace(/\,/g, ''), 10);

            if (type_value === 'Negative') {
                if (Number(to_adjust) > Number(quantity_in)) {
                    notify('Quantity exceeds available stock', 'top', 'right', 'warning');
                    document.getElementById('quantity_edit_').value = to_adjust;
                    document.getElementById('quantity_edit_').style.borderColor = 'red';
                    return false;
                }
                document.getElementById('quantity_edit_').style.borderColor = 'none';
            }

            document.getElementById('quantity_edit_').style.borderColor = 'none';

            /*check required fields*/
            if (Number(type_value) === 0 && Number(reason_value) === 0) {
                document.getElementById('reason_border').style.borderColor = 'red';
                document.getElementById('type_border').style.borderColor = 'red';
                return false;
            } else if (Number(type_value) !== 0 && Number(reason_value) === 0) {
                document.getElementById('reason_border').style.borderColor = 'red';
                document.getElementById('type_border').style.borderColor = 'white';
                return false;
            } else if (Number(type_value) === 0 && Number(reason_value) !== 0) {
                document.getElementById('reason_border').style.borderColor = 'white';
                document.getElementById('type_border').style.borderColor = 'red';
                return false;
            }
            document.getElementById('reason_border').style.borderColor = 'white';
            document.getElementById('type_border').style.borderColor = 'white';

        });

        $(document).ready(calculate());
        $(document).ready(formatMoney());

        function numberWithCommas(digit) {
            return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }


    </script>
@endpush
