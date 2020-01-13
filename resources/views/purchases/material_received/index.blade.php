@extends("layouts.master")
@section('content-title')
    Material Received
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchases /Material Received</a></li>
@endsection


@section("content")

    <style>
        .select2-container {
            width: 103% !important;
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

    </style>

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-3" style="margin-left: 2.5%">
                        <label style="margin-left: 67%" for=""
                               class="col-form-label text-md-right">Supplier:</label>
                    </div>
                    <div class="col-md-3" style="margin-left: -3.2%;">
                        <select class="js-example-basic-single form-control" id="supplier"
                                onchange="getMaterialsReceived()">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-3" style="margin-left: 2.5%">
                        <label style="margin-left: 78%" for=""
                               class="col-form-label text-md-right">Date:</label>
                    </div>
                    <div class="col-md-3" style="margin-left: -3.4%;">
                        <input style="width: 104%;" type="text" name="expire_date" class="form-control"
                               id="receive_date"
                               onchange="getMaterialsReceived()">
                    </div>
                </div>
                {{--                <div class="row">--}}

                <div class="col-md-4" hidden>
                    <label for="code">Product</label>
                    <select id="received_product"
                            class="js-example-basic-single form-control" onchange="getMaterialsReceived()">
                        <option value="">Select Product</option>
                        @foreach($products as $stock)
                            <option value="{{$stock->id}}">{{$stock->name}}</option>
                        @endforeach
                    </select>
                </div>

                {{--                    <div class="col-md-4">--}}

                {{--                        <label>Date</label>--}}
                {{--                        <input type="text" name="expire_date" class="form-control" id="receive_date"--}}
                {{--                               onchange="getMaterialsReceived()">--}}
                {{--                    </div>--}}

                {{--                    <div class="col-md-4">--}}
                {{--                        <div class="form-group">--}}
                {{--                            <label for="code">Supplier Name</label>--}}
                {{--                            <select class="js-example-basic-single form-control" id="supplier"--}}
                {{--                                    onchange="getMaterialsReceived()">--}}
                {{--                                <option value="">Select Supplier</option>--}}
                {{--                                @foreach($suppliers as $supplier)--}}
                {{--                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>--}}
                {{--                                @endforeach--}}
                {{--                            </select>--}}

                {{--                        </div>--}}
                {{--                    </div>--}}

                {{--                </div>--}}
                <div id="tbody1" class="table-responsive">
                    <table id="received_material_table" class="display table nowrap table-striped table-hover"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Expire Date</th>
                            <th>Amount</th>
                            <th>Receive Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- ajax loading gif -->
                <div id="loading">
                    <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                </div>


            </div>
        </div>
    </div>

    @include('purchases.material_received.edit')
    @include('purchases.material_received.delete')

@endsection
@push("page_scripts")
    @include('partials.notification')
    <script type="text/javascript">
        $(function () {
            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#receive_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#receive_date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
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

        //expire date
        $(function () {
            var start = moment();
            var end = moment();

            $('#expire_date_edit').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-M-YYYY'
                }
            });
        });

        $('input[name="expire_date_edit"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('input[name="expire_date_edit"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        /*receive date*/
        $(function () {
            var start = moment();
            var end = moment();

            $('#receive_date_edit').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-M-YYYY'
                }
            });
        });

        $('input[name="receive_date_edit"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('input[name="receive_date_edit"]').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

    </script>
    <script type="text/javascript">

        // $(document).ready(function () {
        //    getMaterialsReceived();
        // });

        function getMaterialsReceived() {
            var product_id = document.getElementById("received_product").value;
            var supplier_id = document.getElementById("supplier").value;
            var range = document.getElementById("receive_date").value;
            var date = range.split('-');
            if (product_id || date || supplier_id) {
                $('#loading').show();
                $.ajax({
                    url: '{{route('getMaterialsReceived')}}',
                    data: {
                        "_token": '{{ csrf_token() }}',
                        "product_id": product_id,
                        "supplier_id": supplier_id,
                        "date": date
                    },
                    type: 'get',
                    dataType: 'json',
                    success: function (data) {
                        received_material_table.clear();
                        received_material_table.rows.add(data[0][0]);
                        received_material_table.draw();
                    },
                    complete: function () {
                        $('#loading').hide();
                    }
                });
            }
        }

        var received_material_table = $('#received_material_table').DataTable({
            searching: true,
            bPaginate: true,
            bInfo: false,
            columns: [
                {data: 'id'},
                {data: 'product.name'},
                {
                    data: 'quantity', render: function (data) {
                        return numberWithCommas(parseFloat(data));
                    }
                },
                {
                    data: 'unit_cost', render: function (unit_cost) {
                        return formatMoney(unit_cost)
                    }
                },
                {
                    data: 'expire_date', render: function (expire_date) {
                        var date = moment(expire_date).format('DD-MM-Y');
                        if (date === 'Invalid date') {
                            return '-';
                        }
                        return date;
                    }
                },
                {
                    data: 'total_cost', render: function (total_cost) {
                        return formatMoney(total_cost)
                    }
                },
                {
                    data: 'created_at', render: function (date) {
                        return moment(date).format('DD-MM-Y');
                    }
                },
                { data: 'action', defaultContent: "<div><input type='button' value='Edit' id='edit_btn' class='btn btn-info btn-rounded btn-sm'/><input type='button' value='Delete' id='delete_btn' class='btn btn-danger btn-rounded btn-sm'/></div>"}
            ],
            "columnDefs": [
                {
                    "targets": [0],
                    "visible": false
                }
            ],
            "order": [[0, "desc"]]
        });

        $('#received_material_table tbody').on('click', '#edit_btn', function () {
            var row_data = $('#received_material_table').DataTable().row($(this).parents('tr')).data();
            $('#edit').find('.modal-body #name_edit').val(row_data.product.name);
            $('#edit').find('.modal-body #quantity_edit').val(numberWithCommas(row_data.quantity));
            $('#edit').find('.modal-body #price_edit').val(formatMoney(row_data.unit_cost));
            $('#edit').find('.modal-body #expire_date_edit').val(moment(row_data.expire_date).format('DD-MM-Y'));
            $('#edit').find('.modal-body #receive_date_edit').val(moment(row_data.created_at).format('D-M-Y'));
            $('#edit').find('.modal-body #id').val(row_data.id);
            $('#edit').modal('show');

        });

        $('#received_material_table tbody').on('click', '#delete_btn', function () {
            var row_data = $('#received_material_table').DataTable().row($(this).parents('tr')).data();
            var message = "Are you sure you want to delete '".concat(row_data.product.name, "'?");
            $('#delete').find('.modal-body #message').text(message);
            $('#delete').find('.modal-body #id').val(row_data.id);
            $('#delete').modal('show');
        });

        $('#price_edit').on('change', function () {
           var price = document.getElementById('price_edit').value;
            document.getElementById('price_edit').value = formatMoney(price);
        });

        $('#quantity_edit').on('change',function () {
            var quantity = document.getElementById('quantity_edit').value;
            if (quantity === ''){
                document.getElementById('quantity_edit').value = '';
            }else{
                document.getElementById('quantity_edit').value = numberWithCommas(quantity);
            }
        });

        function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

                const negativeSign = amount < 0 ? "-" : "";

                let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
                let j = (i.length > 3) ? i.length % 3 : 0;

                return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
            } catch (e) {
                console.log(e)
            }
        }

        function numberWithCommas(digit) {
            return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function isNumberKey(evt, obj) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            var value = obj.value;
            var dotcontains = value.indexOf(".") !== -1;
            if (dotcontains)
                if (charCode === 46) return false;
            if (charCode === 46) return true;
            return !(charCode > 31 && (charCode < 48 || charCode > 57));

        }

    </script>

@endpush
