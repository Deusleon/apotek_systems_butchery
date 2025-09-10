@extends("layouts.master")

@section('content-title')
    Sales History
@endsection
@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Sales History</a></li>
@endsection

@section("content")

    <style>
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

    <div class="col-md-12">
        <div class="card-block">
            <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" id="sales-history-tablist" data-toggle="pill"
                        href="{{ route('sale-histories.SalesHistory') }}" role="tab" aria-controls="sales_history"
                        aria-selected="true">Sales History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="sales-return-tablist" data-toggle="pill"
                        href="{{ route('sale-returns.index') }}" role="tab" aria-controls="sales_returns"
                        aria-selected="false">Returns
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="sales-approval-tablist" data-toggle="pill"
                        href="{{ route('sale-returns-approval.getSalesReturn') }}" role="tab" aria-controls="sales_returns"
                        aria-selected="false">Approval
                    </a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">

                {{-- Sales History Start --}}
                <div class="tab-pane fade show active" id="sales-history" role="tabpanel"
                    aria-labelledby="sales_history-tab">
                    <input type="hidden" value="{{$vat}}" id="vat">
                    <div class="form-group row">
                        <div class="col-md-6"></div>
                        <div class="col-md-3" style="margin-left: 2.5%">
                            <label style="margin-left: 80%" for="filter" class="col-form-label text-md-right">Date:</label>
                        </div>
                        <div class="col-md-3" style="margin-left: -3.4%">
                            <input style="width: 103.4%;" type="text" class="form-control" id="daterange" />
                        </div>

                    </div>
                    <form id="sale_receipt_reprint_form" action="{{route('sale-reprint-receipt')}}" method="post"
                        enctype="multipart/form-data" target="_blank">
                        @csrf()

                        <div class="table-responsive" id="sales">

                            <table id="sale_history_dataTable"
                                class="display table nowrap table-striped table-hover dataTable no-footer"
                                style="width:100%">

                                <thead>
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Sub Total</th>
                                        <th>VAT</th>
                                        <th>Discount</th>
                                        <th>Amount</th>
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

                        <input type="hidden" value="" id="category">
                        <input type="hidden" value="" id="customers">
                        <input type="hidden" value="" id="print" name="reprint_receipt">
                        <input type="hidden" value="" id="fixed_price">

                    </form>
                </div>
                {{-- Sales History End --}}

                {{-- Sales Returns Starts--}}
                <div class="tab-pane fade" id="sales-returns" role="tabpanel" aria-labelledby="sales_returns-tab">
                    <div class="col-sm-12">
                        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active text-uppercase" id="sales-return-tablist" data-toggle="pill"
                                    href="#sales-return" role="tab" aria-controls="sales_return"
                                    aria-selected="true">Return</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-uppercase" id="returns-approval-tablist" data-toggle="pill"
                                    href="#sales-return-approval" role="tab" aria-controls="sales_return_approval"
                                    aria-selected="false">Approval
                                </a>
                            </li>
                        </ul>
                        <div class="card-block">
                            <div class="tab-content" id="myTabContent">
                                {{-- Sales Return Start--}}
                                {{-- <div class="tab-pane fade show active" id="sales-return" role="tabpanel"
                                    aria-labelledby="sales_return-tab">
                                    <div class="table-responsive" id="items" style="display: none;">
                                        <table id="items_table" class="table nowrap table-striped table-hover" width="100%">
                                        </table>
                                        <div class="btn-group" style="float: right; margin-right: 4%; margin-top: 2%">
                                            <button class="btn btn-sm btn-rounded btn-danger" onclick="return false"
                                                id="cancel">Back
                                            </button>
                                        </div>
                                    </div>
                                    <div id="sales">
                                        <div class="form-group row">
                                            <div class="col-md-6">

                                            </div>
                                            <div class="col-md-3" style="margin-left: 2.5%">
                                                <label style="margin-left: 80%" for="issued_date"
                                                    class="col-form-label text-md-right">Date:</label>
                                            </div>
                                            <div class="col-md-3" style="margin-left: -3.1%">
                                                <input style="width: 103.4%;" type="text" name="expire_date"
                                                    class="form-control" id="sold_date" onchange="getSales()">
                                            </div>
                                            <div class="col-md-6" hidden>

                                            </div>
                                            <div class="form-group col-md-6" hidden>
                                                <label for="Seach">Search</label>
                                                <input type="text" class="form-control" id="searching_sales"
                                                    placeholder="Search" />
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="sale_list_return_table"
                                                class="display table nowrap table-striped table-hover" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Receipt #</th>
                                                        <th>Date</th>
                                                        <th>Customer</th>
                                                        <th>Sub Total</th>
                                                        <th>VAT</th>
                                                        <th>Discount</th>
                                                        <th>Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>


                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div> --}}
                                {{-- Sales Return End--}}

                                {{-- Sales Return Approval Start--}}
                                {{-- <div class="tab-pane fade" id="sales-return-approval" role="tabpanel"
                                    aria-labelledby="sales_return_approval-tab">
                                    <div class="form-group row">
                                        <div class="col-md-6">

                                        </div>
                                        <div class="col-md-3" style="margin-left: 2.5%">
                                            <label style="margin-left: 74%" for=""
                                                class="col-form-label text-md-right">Status:</label>
                                        </div>
                                        <div class="col-md-3" style="margin-left: -3.2%;">
                                            <select id="retun_status" class="js-example-basic-single form-control"
                                                onchange="getRetunedProducts()">
                                                <option value="2">Pending</option>
                                                <option value="3">Approved</option>
                                                <option value="4">Rejected</option>
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
                                            <input style="width: 104%;" type="text" class="form-control" id="returned_date"
                                                onchange="getRetunedProducts()">
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="return_table" class="display table nowrap table-striped table-hover"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th>Buy Date</th>
                                                    <th>Qty Bought</th>
                                                    <th>Return Date</th>
                                                    <th>Qty Returned</th>
                                                    <th>Refund</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>

                                        </table>

                                    </div>
                                </div> --}}
                                {{-- Sales Return Approval End--}}

                            </div>

                        </div>


                    </div>
                    @include('sales.sale_returns.return')
                </div>
                {{-- Sales Returns End --}}


            </div>

        </div>


    </div>
    </div>
    @include('sales.sales_history.details')

@endsection

@push("page_scripts")
    {{-- Show Sales History in details --}}
    <script>
        //Functionalities Below will be able to show Sale Product List
        //Endpoints
        var config = {
            token: '{{ csrf_token() }}',
            routes: {
                salesDetails: '{{route('sale_detail')}}',
                getSalesHistory: '{{route('getSalesHistory')}}',
                getSalesHistoryData: '{{ route('getSalesHistoryData') }}',
                receiptBaseUrl: "{{ route('sale-reprint-receipt-get', ['receipt' => ':receipt']) }}"
            }
        };

    </script>

    <script src="{{asset("assets/apotek/js/sales_history.js")}}"></script>
    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // $('#sale_history_table tbody').on('click', '#sale_receipt_reprint', function () {
        //     var row_data = $('#sale_history_table').DataTable().row($(this).parents('tr')).data();
        //     document.getElementById("print").value = row_data.receipt_number;
        // });

    </script>
    <script type="text/javascript">
        $(function () {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                // Display format tu
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#daterange').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: end,
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY/MM/DD' 
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);

        });

    </script>

    {{-- Return Sale --}}
    @include('partials.notification')
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>


    {{-- Date Functions --}}
    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#sold_date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY/MM/DD' 
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);


        });

    </script>

    <script type="text/javascript">
        function getSales() {
            var range = document.getElementById('sold_date').value;
            range = range.split('-');

            $("#sale_list_return_table").dataTable().fnDestroy();

            $('#sale_list_return_table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": '{{route('getSales')}}',
                    "dataType": "json",
                    "type": "post",
                    "cache": false,
                    "data": {
                        _token: "{{csrf_token()}}",
                        range: range
                    }
                },
                "columns": [
                    { 'data': 'receipt_number' },
                    {
                        'data': 'date', render: function (date) {
                            return moment(date).format('D-M-YYYY');
                        }
                    },
                    {
                        'data': 'customer', render: function (customer) {
                            if (customer) {
                                return customer.name
                            }
                            return '';
                        }
                    },
                    {
                        'data': 'cost', render: function (cost) {
                            if (cost) {
                                return formatMoney(((cost.amount - cost.discount) / (1 + (cost.vat / cost.sub_total))));
                            }
                            return '';
                        }
                    },

                    {
                        'data': 'cost', render: function (cost) {
                            if (cost) {
                                return formatMoney(((cost.amount - cost.discount) * (cost.vat / cost.sub_total)));
                            }
                            return '';
                        }
                    },
                    {
                        'data': 'cost.discount', render: function (discount) {
                            return formatMoney(discount);
                        }
                    },
                    {
                        'data': 'cost', render: function (cost) {
                            if (cost) {
                                return formatMoney(((cost.amount - cost.discount)));
                            }
                            return '';
                        }
                    },
                    {
                        'data': "action",
                        defaultContent: "<button type='button' id='open_btn' class='btn btn-sm btn-rounded btn-success'>Open</button>"
                    }
                ], aaSorting: [[1, 'desc']],
                "columnDefs": [
                    { "orderable": false, "targets": [3, 4, 5, 6, 7] }
                ]

            });


        }

        $('#sale_list_return_table tbody').on('click', '#open_btn', function () {
            var row_data = $('#sale_list_return_table').DataTable().row($(this).parents('tr')).data();
            saleReturn(row_data.details);
        });

    </script>


    {{-- Return Sale Approval --}}
    <script type="text/javascript">

        $(function () {
            var start = moment();
            var end = moment();

            function cb(start, end) {
                // Display format tu
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#returned_date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: end,
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY/MM/DD' 
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


        $('#return_table tbody').on('click', '#approve', function () {
            var product = return_table.row($(this).parents('tr')).data();
            getRetunedProducts('approve', product.item_returned)
        });

        $('#return_table tbody').on('click', '#reject', function () {
            var product = return_table.row($(this).parents('tr')).data();
            getRetunedProducts('reject', product.item_returned)
        });


        function getRetunedProducts(action, product) {
            var status = document.getElementById("retun_status").value;
            var range = document.getElementById("returned_date").value;
            var date = range.split('-');
            if (date) {
                $('#loading').show();
                $.ajax({
                    url: '{{route('getRetunedProducts')}}',
                    data: {
                        "_token": '{{ csrf_token() }}',
                        "date": date,
                        "status": status,
                        "action": action,
                        "product": product
                    },
                    type: 'get',
                    dataType: 'json',
                    cache: false,
                    success: function (data) {
                        if (status == 3) {

                            return_table.column(6).visible(false);
                            data.forEach(function (data) {
                                if (data.status == 5) {
                                    data.item_returned.bought_qty += data.item_returned.rtn_qty;//This calculate the original bought qty
                                    data.item_returned.amount = (data.item_returned.amount / data.item_returned.rtn_qty) * data.item_returned.bought_qty;
                                }

                            });
                        } else if (status == 4) {
                            return_table.column(6).visible(false);
                        } else {
                            return_table.column(6).visible(true);
                        }
                        return_table.clear();
                        return_table.rows.add(data);
                        return_table.draw();

                    },
                    complete: function () {
                        $('#loading').hide();
                    }
                });
            }
        }

        var return_table = $('#return_table').DataTable({
            bPaginate: true,
            bInfo: true,
            // dom: 't',
            columns: [
                { data: 'item_returned.name' },
                {
                    data: 'item_returned.b_date', render: function (date) {
                        return moment(date).format('YYYY-MM-DD');
                    }
                },
                {
                    data: 'item_returned',
                    render: function (item_returned) {
                        return Math.floor(item_returned.bought_qty).toLocaleString();
                    }
                },
                {
                    data: 'date', render: function (date) {
                        return moment(date).format('YYYY-MM-DD');
                    }
                },
                {
                    data: 'item_returned',
                    render: function (item_returned) {
                        return Math.floor(item_returned.rtn_qty).toLocaleString();
                    }
                },
                {
                    data: 'item_returned', render: function (item_returned) {
                        return formatMoney((item_returned.rtn_qty / item_returned.bought_qty) * (item_returned.amount - item_returned.discount));
                    }
                },
                {
                    data: "action",
                    defaultContent: "<button type='button' id='approve' class='btn btn-sm btn-rounded btn-primary'>Approve</button><button type='button' id='reject' class='btn btn-sm btn-rounded btn-danger'>Reject</button>"
                }

            ], aaSorting: [[1, "desc"]]
        });

        $('#searching_returns').on('keyup', function () {
            return_table.search(this.value).draw();
        });

    </script>


    {{-- Tabs Control--}}
    <script>
        $(document).ready(function () {
            // Listen for the click event on the Transfer History tab
            $('#sales-history-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#sales-return-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#sales-approval-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });
        });


    </script>

@endpush