@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Price List
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Price List </a></li>
@endsection

@section("content")

    <style>
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
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
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="form-group row">
                        <div class="col-md-6" style="margin-left: 2%">
                        </div>
                        <div class="col-md-3">
                            <label for="price_category" class="col-form-label text-md-right"
                                   style="margin-left: 68%">Category:</label>
                        </div>
                        <div class="col-md-3" style="margin-left: -2.6%;">
                            <select name="price_category" class="js-example-basic-single form-control"
                                    id="price_category" onchange="option()">
                                @foreach($price_categories as $price_category)
                                    <option value="{{$price_category->id}}">{{ $price_category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="tbody1" class="table-responsive">
                        <table id="fixed-header2" class="display table nowrap table-striped table-hover"
                               style="width:100%">

                            <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Buying Price</th>
                                <th>Selling Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>
                    </div>
                </div>


                <div id="loading">
                    <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                </div>
                <div id="tbody" style="display: none;" class="table-responsive">
                    <table id="fixed-header1" class="display table nowrap table-striped table-hover"
                           style="width:100%">

                        <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Buying Price</th>
                            <th>Selling Price</th>
                            <th>code</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>


    @include('stock_management.price_list.edit')
    @include('stock_management.price_list.show')
    @include('stock_management.price_list.history')
@endsection




@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    <script src="{{asset("assets/apotek/js/stock-transfer.js")}}"></script>

    @include('partials.notification')

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            loadPriceList();
        });

        function loadPriceList() {
            var e = document.getElementById("price_category");
            var value = e.options[e.selectedIndex].value;

            $("#fixed-header2").dataTable().fnDestroy();
            var table_main = $('#fixed-header2').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('all-price-list') }}",
                    "dataType": "json",
                    "type": "post",
                    "cache": false,
                    "data": {
                        _token: "{{csrf_token()}}",
                        price_category: value
                    }
                },
                "columns": [
                    {"data": "name"},
                    {
                        "data": "unit_cost", render: function (unit_cost) {
                            return formatMoney(unit_cost);
                        }
                    },
                    {
                        "data": "price", render: function (price) {
                            return formatMoney(price);
                        }
                    },
                    {
                        "data": "action",
                        defaultContent: "<div><button id='detail' data-target='#test' class='btn btn-sm btn-rounded btn-success' type='button'>Price History</button></div>"
                    }
                ]

            });

            $('#tbody1').on('click', '#detail', function () {
                var data = table_main.row($(this).parents('tr')).data();

                var e = document.getElementById("price_category");
                var value = e.options[e.selectedIndex].value;

                retrivePriceHistory(value, data.product_id);
            });
        }


        $('#tbody1').on('click', '#detail', function () {
            var data = $('#fixed-header2').DataTable().row($(this).parents('tr')).data();

            var e = document.getElementById("price_category");
            var value = e.options[e.selectedIndex].value;

            retrivePriceHistory(value, data.product_id);
        });

        $('#edit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body #id').val(button.data('id'));
            modal.find('.modal-body #product_id').val(button.data('product_id'));
            modal.find('.modal-body #stock_id').val(button.data('stock_id'));
            modal.find('.modal-body #name_edit').val(button.data('name'));
            modal.find('.modal-body #unit_cost_edit').val(button.data('unit'));
            modal.find('.modal-body #sell_price_edit').val(button.data('sell'));
            modal.find('.modal-body #price_category_edit').val(button.data('category'));
            modal.find('.modal-body #d_auto_4').val(button.data('batch'));
            modal.find('.modal-body #d_auto_5').val(button.data('expiry'));

        });

        $('#show').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body #id').text(button.data('id'));
            modal.find('.modal-body #product_id').text(button.data('product_id'));
            modal.find('.modal-body #stock_id').text(button.data('stock_id'));
            modal.find('.modal-body #name_edit').text(button.data('name'));
            modal.find('.modal-body #unit_cost_edit').text(button.data('unit'));
            modal.find('.modal-body #sell_price_edit').text(button.data('sell'));
            modal.find('.modal-body #price_category_edit').text(button.data('category'));
            modal.find('.modal-body #d_auto_4').text(button.data('batch'));
            modal.find('.modal-body #d_auto_5').text(button.data('expiry'));


        });

        function option() {

            loadPriceList();

        }

        function displayVals(data) {
            var val = data;
            var ajaxurl = '{{route('myitems')}}';
            $('#loading').show();
            $.ajax({
                url: ajaxurl,
                type: "get",
                dataType: "json",
                data: {val: val},
                success: function (data) {
                    document.getElementById("tbody1").style.display = 'none';
                    document.getElementById("tbody").style.display = 'block';
                    bindData(data);

                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        }


        // var tables = $('#fixed-header1').DataTable({
        //     'columns': [
        //         {'data': 'name'},
        //         {
        //             'data': 'unit_cost', render: function (unit_cost) {
        //                 return formatMoney(unit_cost);
        //             }
        //         },
        //         {
        //             'data': 'price', render: function (price) {
        //                 return formatMoney(price);
        //             }
        //         },
        //         {'data': 'product_id'},
        //         {
        //             'data': 'action',
        //             defaultContent: "<div><button id='detail4' class='btn btn-sm btn-rounded btn-success' type='button'>Price History</button></div>"
        //         }
        //     ]
        // });

        var table = $('#fixed-header-price').DataTable({
            'columns': [
                {'data': 'product_name'},
                {
                    'data': 'unit_cost', render: function (unit_cost) {
                        return formatMoney(unit_cost);
                    }
                },
                {
                    'data': 'selling_price', render: function (unit_cost) {
                        return formatMoney(unit_cost);
                    }
                },
                {'data': 'price_category_name'},
                {'data': 'batch_number'}
            ]
        });

        function bindData(data) {
            tables.clear();
            tables.rows.add(data);
            tables.column(3).visible(false);
            tables.draw();

            $('#tbody').on('click', '#detail4', function () {
                var datas = tables.row($(this).parents('tr')).data();
                var e = document.getElementById("price_category");
                var value = e.options[e.selectedIndex].value;
                retrivePriceHistory(value, datas.product_id);
            });

        }


        // $(document).ready(function () {
        // var table = $('#fixed-header').DataTable();
        // $('#tbody1').on('click', '#detail', function () {
        //     var data = table.row($(this).parents('tr')).data();
        //
        //     var values = $(this).val();
        //     var selected_fields = values.split(',');
        //     var product_id = selected_fields[1];
        //
        //     var e = document.getElementById("price_category");
        //     var value = e.options[e.selectedIndex].value;
        //
        //     retrivePriceHistory(value, product_id);

        // });
        // });

        function change() {
            $('.price-list').text('Price List');
            document.getElementById("tbody1").style.display = 'block';
            document.getElementById("tbody").style.display = 'none';

            $('input[type="search"]').val('').keyup();

        }

        function retrivePriceHistory(data, data1) {

            var ajaxurl = '{{route('price-history')}}';
            $('#loading').show();
            $.ajax({
                url: ajaxurl,
                type: "get",
                dataType: "json",
                data: {price_category_id: data, product_id: data1},
                success: function (data) {
                    bindPriceData(data);

                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        }


        function bindPriceData(data) {
            table.clear();
            table.rows.add(data);
            table.draw();
            $('#test').modal('show');
        }


    </script>

@endpush
