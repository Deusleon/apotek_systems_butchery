@extends("layouts.master")
@section('content-title')
    Purchase Returns
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Purchase Returns / Returns</a></li>
@endsection

@section("content")

    <style>
        .select2-container {
            width: 100% !important;
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

        .filter-controls {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-bottom: 20px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-control label {
            margin-bottom: 0;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .filter-controls {
                justify-content: flex-start;
            }

            .filter-control {
                flex: 1 0 100%;
            }
        }
    </style>

    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab">
            @if (auth()->user()->checkPermission('View Purchase Returns'))
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" href="{{ url('purchasing/purchase-returns') }}">Returns
                    </a>
                </li>
            @endif
            @if (auth()->user()->checkPermission('View Purchase Returns Approvals'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" href="{{ url('purchasing/purchase_returns/approvals') }}">Approvals
                    </a>
                </li>
            @endif
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="filter-controls">
                    <div class="filter-control" style="min-width: 260px;" hidden>
                        <label for="supplier" class="col-form-label text-md-right">Supplier:</label>
                        <select class="js-example-basic-single form-control" id="supplier" onchange="getMaterialsReceived()">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        <label class="mr-2" for="receive_date">Date:</label>
                        <input type="text" name="expire_date" id="receive_date"
                            class="form-control w-auto">
                    </div>

                    {{-- <div class="filter-control">
                        <label for="receive_date" class="col-form-label text-md-right">Date:</label>
                        <input type="text" name="expire_date" class="form-control" id="receive_date"
                            style="min-width: 250px;">
                    </div> --}}
                </div>

                <div class="col-md-4" hidden>
                    <label for="code">Product</label>
                    <select id="received_product" class="js-example-basic-single form-control"
                        onchange="getMaterialsReceived()">
                        <option value="">Select Product</option>
                        @foreach($products as $stock)
                            <option value="{{$stock->id}}">{{$stock->name}}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" id="expire_date_enabler" value="{{$expire_date}}">
                <div id="tbody1" class="table-responsive">
                    <table id="received_material_table" class="display table nowrap table-striped table-hover"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Product Name</th>
                                <th>Supplier</th>
                                <th class="d-none">Ordered</th>
                                <th>Quantity</th>
                                <th class="d-none">Remaining</th>
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Receive Date</th>
                                <th>Received By</th>
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
    @include('purchases.purchase_returns.return')
    @include('purchases.purchase_returns.edit')
    @include('purchases.purchase_returns.returns')

@endsection
@push("page_scripts")
@include('partials.notification')
<script type="text/javascript">
    var hasPurchaseReturnPermission = @json(auth()->user()->checkPermission('Purchase Return'));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {
            var start = moment().subtract(29, 'days');
            var end = moment();

            function cb(start, end) {
                $('#receive_date').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#receive_date').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(
                    picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD')
                );
                getMaterialsReceived();
            });


            $('#receive_date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY/MM/DD'   // 👈 Force consistent format
                },
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
                    format: 'YYYY-MM-DD'
                }
            });
        });

        $('input[name="expire_date_edit"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        $('input[name="expire_date_edit"]').on('cancel.daterangepicker', function (ev, picker) {
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
                    format: 'YYYY-MM-DD'  // Changed format to Year-Month-Day
                }
            });
        });

        $('input[name="receive_date_edit"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        $('input[name="receive_date_edit"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

    </script>
    <script type="text/javascript">

        $(document).ready(function () {
            getMaterialsReceived();
        });

        function getMaterialsReceived() {
            var product_id = document.getElementById("received_product").value;
            var supplier_id = document.getElementById("supplier").value;
            var range = document.getElementById("receive_date").value;
            if (range) {
                var date = range.split(" - ").map(function (d) {
                    return d.trim();
                });
            } else {
                var date = [];
            }
            if (product_id || date || supplier_id) {
                // $('#loading').show();

                $("#received_material_table").dataTable().fnDestroy();

                var received_material_table = $('#received_material_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": '{{route('getMaterialsReceived')}}',
                        "dataType": "json",
                        "type": "post",
                        "cache": false,
                        "data": {
                            "_token": '{{ csrf_token() }}',
                            "product_id": product_id,
                            "supplier_id": supplier_id,
                            "date": date
                        }
                    },
                    "columns": [
                        { data: 'id' },
                        {
                            data: 'product',
                            render: function (data) {
                                return (data.name || '') + ' ' + (data.brand || '') + ' ' + (data.pack_size || '') + (data.sales_uom || '');
                            }
                        },
                        {
                            data: 'supplier',
                            render: function (data) {
                                return data ? data.name : 'N/A';
                            }
                        },
                        {
                            data: 'ordered_qty', render: function (data) {
                                return numberWithCommas(parseFloat(data));
                            }
                        },
                        {
                            data: 'quantity', render: function (data, type, row) {
                                // Show current remaining quantity after approved returns
                                var currentQty = parseFloat(data || 0);
                                return numberWithCommas(currentQty);
                            }
                        },
                        {
                            data: 'remaining_qty', render: function (data) {
                                return numberWithCommas(parseFloat(data));
                            }
                        },
                        {
                            data: 'unit_cost', render: function (unit_cost) {
                                return formatMoney(unit_cost)
                            }
                        },
                        {
                            data: 'total_cost', render: function (total_cost) {
                                return formatMoney(total_cost)
                            }
                        },
                        {
                            data: 'created_at', render: function (date) {
                                return moment(date).format('Y-MM-DD');
                            }
                        },
                        { data: 'user.name' },
                        {
                            data: 'action',
                            render: function (data, type, row) {
                                // Only show action column if user has permission
                                if (!hasPurchaseReturnPermission) {
                                    return '';
                                }

                                // Disable the return button if the item has a return process ongoing or completed
                                // Status: 2=pending return, 3=fully returned, 4=rejected, 5=partially returned
                                var hasReturn = false;
                                if (row.status == '2' || row.status == '3' || row.status == '4' || row.status == '5') {
                                    hasReturn = true;
                                }

                                if (hasReturn) {
                                    return `<input type='button' value='Return' id='return_btn' class='btn btn-success btn-rounded btn-sm' disabled/>`;
                                } else {
                                    return `<input type='button' value='Return' id='return_btn' class='btn btn-primary btn-rounded btn-sm'/>`;
                                }
                            }
                        }

                    ], "columnDefs": [
                        {
                            "targets": [0, 3, 5],
                            "visible": false
                        },
                        {
                            "targets": [10], // Action column
                            "visible": hasPurchaseReturnPermission
                        }
                    ],
                    "order": [[0, "desc"]]

                });

                var expire_date_enabler = document.getElementById("expire_date_enabler").value;
                console.log(expire_date_enabler);
                if (expire_date_enabler === "NO") {
                    received_material_table.column(5).visible(false);
                }
            }
        }

        $('#expire_date_edit').keydown(function (event) {
            return false;
        });

        $('#received_material_table tbody').on('click', '#edit_btn', function () {
            var row_data = $('#received_material_table').DataTable().row($(this).parents('tr')).data();

            // Check if there's a purchase return for this goods_receiving_id
            $.ajax({
                url: '{{route('getPurchaseReturns')}}',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "action": "check_return",
                    "goods_receiving_id": row_data.id
                },
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    if (response.has_return) {
                        // Open purchase return edit modal
                        $('#edit-purchase-return').find('.modal-body #edit_product_name').val(
                            (row_data.product.name || '') + ' ' +
                            (row_data.product.brand || '') + ' ' +
                            (row_data.product.pack_size || '') +
                            (row_data.product.sales_uom || '')
                        );
                        $('#edit-purchase-return').find('.modal-body #edit_rtn_qty_to_show').val(numberWithCommas(response.return_quantity));
                        $('#edit-purchase-return').find('.modal-body #edit_rtn_qty').val(response.return_quantity);
                        $('#edit-purchase-return').find('.modal-body #edit_reason').val(response.reason);
                        $('#edit-purchase-return').find('.modal-body #edit_goods_receiving_id').val(row_data.id);
                        $('#edit-purchase-return').find('.modal-body #edit_return_id').val(response.return_id);

                        // Set form action dynamically
                        var updateUrl = '{{ url("purchase-returns") }}/' + response.return_id;
                        $('#edit-purchase-return-form').attr('action', updateUrl);

                        $('#edit-purchase-return').modal('show');
                    } else {
                        // Check status before allowing edit
                        $.ajax({
                            url: '{{route('getPurchaseReturns')}}',
                            data: {
                                "_token": '{{ csrf_token() }}',
                                "action": "check_status",
                                "goods_receiving_id": row_data.id
                            },
                            type: 'get',
                            dataType: 'json',
                            success: function (statusResponse) {
                                if (statusResponse.has_pending_return) {
                                    alert('This item has a pending return and cannot be edited.');
                                    return;
                                }

                                // Open material received edit modal
                                $('#edit').find('.modal-body #name_edit').val(
                                    (row_data.product.name || '') + ' ' +
                                    (row_data.product.brand || '') + ' ' +
                                    (row_data.product.pack_size || '') +
                                    (row_data.product.sales_uom || '')
                                );
                                $('#edit').find('.modal-body #quantity_edit').val(numberWithCommas(row_data.quantity));
                                $('#edit').find('.modal-body #price_edit').val(formatMoney(row_data.unit_cost));
                                if (row_data.expire_date) {
                                    $('#edit').find('.modal-body #expire_date_edit').val(moment(row_data.expire_date).format('YYYY-MM-DD'));
                                } else {
                                    $('#edit').find('.modal-body #expire_date_edit').val('');
                                }
                                $('#edit').find('.modal-body #receive_date_edit').val(moment(row_data.created_at).format('YYYY-MM-DD'));
                                $('#edit').find('.modal-body #id').val(row_data.id);

                                // ✅ Preselect supplier
                                if (row_data.supplier && row_data.supplier.id) {
                                    $('#supplier_id_edit').val(row_data.supplier.id).trigger('change');
                                } else {
                                    $('#supplier_id_edit').val('').trigger('change');
                                }

                                $('#edit').modal('show');
                            },
                            error: function () {
                                alert('Error checking item status');
                            }
                        });
                    }
                },
                error: function () {
                    alert('Error checking return status');
                }
            });

        });

        $('#received_material_table tbody').on('click', '#delete_btn', function () {
            var row_data = $('#received_material_table').DataTable().row($(this).parents('tr')).data();

            // Check status before allowing delete
            $.ajax({
                url: '{{route('getPurchaseReturns')}}',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "action": "check_status",
                    "goods_receiving_id": row_data.id
                },
                type: 'get',
                dataType: 'json',
                success: function (statusResponse) {
                    if (statusResponse.has_pending_return) {
                        alert('This item has a pending return and cannot be deleted.');
                        return;
                    }

                    var message = "Are you sure you want to delete '".concat(row_data.product.name, "'?");
                    $('#delete').find('.modal-body #message').text(message);
                    $('#delete').find('.modal-body #id').val(row_data.id);
                    $('#delete').modal('show');
                },
                error: function () {
                    alert('Error checking item status');
                }
            });
        });

        $('#received_material_table tbody').on('click', '#return_btn', function () {
            var row_data = $('#received_material_table').DataTable().row($(this).parents('tr')).data();
            $("#purchase-return").modal("show");
            $("#purchase-return").find(".modal-body #product_name").val(
                (row_data.product.name || '') + ' ' +
                (row_data.product.brand || '') + ' ' +
                (row_data.product.pack_size || '') +
                (row_data.product.sales_uom || '')
            );
            $("#purchase-return").find(".modal-body #goods_receiving_id").val(row_data.id);
            $("#purchase-return").find(".modal-body #original_qty").val(row_data.quantity);
            document.getElementById("save_btn").style.display = "block";
            $("#purchase-return").on("change", "#rtn_qty_to_show", function () {
                var quantity = document.getElementById("rtn_qty").value;
                if (Number(quantity) > Number(row_data.quantity) || Number(quantity) < 0) {
                    document.getElementById("save_btn").disabled = "true";
                    document.getElementById("qty_error").style.display = "block";
                    $("#purchase-return")
                        .find(".modal-body #qty_error")
                        .text("Maximum quantity is " + Math.floor(row_data.quantity));
                } else {
                    document.getElementById("qty_error").style.display = "none";
                    $("#save_btn").prop("disabled", false);
                }
            });
        });

        $('#price_edit').on('change', function () {
            var price = document.getElementById('price_edit').value;
            document.getElementById('price_edit').value = formatMoney(price);
        });

        $('#quantity_edit').on('change', function () {
            var quantity = document.getElementById('quantity_edit').value;
            if (quantity === '') {
                document.getElementById('quantity_edit').value = '';
            } else {
                document.getElementById('quantity_edit').value = numberWithCommas(quantity);
            }
        });

        $('#rtn_qty_to_show').on('keyup', function () {
            var newValue = document.getElementById('rtn_qty_to_show').value;
            if (newValue !== '') {
                document.getElementById('rtn_qty_to_show').value =
                    numberWithCommas(parseFloat(newValue.replace(/\,/g, ''), 10));
                document.getElementById('rtn_qty').value = parseFloat(newValue.replace(/\,/g, ''), 10);
            } else {
                document.getElementById('rtn_qty_to_show').value = '';
                document.getElementById('rtn_qty').value = '';
            }
        });

        $('#edit_rtn_qty_to_show').on('keyup', function () {
            var newValue = document.getElementById('edit_rtn_qty_to_show').value;
            if (newValue !== '') {
                document.getElementById('edit_rtn_qty_to_show').value =
                    numberWithCommas(parseFloat(newValue.replace(/\,/g, ''), 10));
                document.getElementById('edit_rtn_qty').value = parseFloat(newValue.replace(/\,/g, ''), 10);
            } else {
                document.getElementById('edit_rtn_qty_to_show').value = '';
                document.getElementById('edit_rtn_qty').value = '';
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

    <script>
        $(document).ready(function () {
            // Listen for the click event on the Transfer History tab
            // Listen for the click event on the Transfer History tab
            $('#material-received').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#order-received').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#invoice-received').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

        });
    </script>

@endpush