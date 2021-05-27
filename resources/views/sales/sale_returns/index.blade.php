@extends("layouts.master")

@section('content-title')
    Sales Return
@endsection
@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Sales Return</a></li>
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

    <div class="col-sm-12">
        <div class="card-block">
            <div class="tab-content" id="myTabContent">
                <input type="hidden" value="{{$vat}}" id="vat">
                <div class="table-responsive" id="items" style="display: none;">
                    {{--                    <h4>Sale Items List</h4>--}}
                    <table id="items_table" class="table nowrap table-striped table-hover" width="100%"></table>
                    <div class="btn-group" style="float: right; margin-right: 4%; margin-top: 2%">
                        <button class="btn btn-sm btn-rounded btn-danger" onclick="return false" id="cancel">Back
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
                            <input style="width: 103.4%;" type="text" name="expire_date" class="form-control"
                                   id="sold_date"
                                   onchange="getSales()">
                        </div>
                        <div class="col-md-6" hidden>

                        </div>
                        <div class="form-group col-md-6" hidden>
                            <label for="Seach">Search</label>
                            <input type="text" class="form-control" id="searching_sales" placeholder="Search"/>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="sale_list_return_table" class="display table nowrap table-striped table-hover"
                               style="width:100%">
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

                <!-- ajax loading gif -->
                <div id="loading">
                    <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                </div>

                <input type="hidden" value="" id="category">
                <input type="hidden" value="" id="customers">
                <input type="hidden" value="" id="print">
                <input type="hidden" value="" id="fixed_price">

            </div>

        </div>


    </div>
    </div>
    @include('sales.sale_returns.return')

@endsection
@push("page_scripts")
    @include('partials.notification')
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    <script src="{{asset("assets/apotek/js/sales.js")}}"></script>

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
                $('#sold_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#sold_date').daterangepicker({
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

    </script>

    <script type="text/javascript">

        // function getSales() {
        //     var range = document.getElementById('sold_date').value;
        //     range = range.split('-');

        //     $("#sale_list_return_table").dataTable().fnDestroy();

        //     $('#sale_list_return_table').DataTable({
        //         "processing": true,
        //         "serverSide": true,
        //         "ajax": {
        //             "url": '{{route('getSales')}}',
        //             "dataType": "json",
        //             "type": "post",
        //             "cache": false,
        //             "data": {
        //                 _token: "{{csrf_token()}}",
        //                 range: range
        //             }
        //         },
        //         "columns": [
        //             {'data': 'receipt_number'},
        //             {
        //                 'data': 'date', render: function (date) {
        //                     return moment(date).format('D-M-YYYY');
        //                 }
        //             },
        //             {'data': 'customer.name'},
        //             {
        //                 'data': 'cost', render: function (cost) {
        //                     return formatMoney(((cost.amount - cost.discount) / (1 + (cost.vat / cost.sub_total))));
        //                 }
        //             },

        //             {
        //                 'data': 'cost', render: function (cost) {
        //                     return formatMoney(((cost.amount - cost.discount) * (cost.vat / cost.sub_total)));
        //                 }
        //             },
        //             {
        //                 'data': 'cost.discount', render: function (discount) {
        //                     return formatMoney(discount);
        //                 }
        //             },
        //             {
        //                 'data': 'cost', render: function (cost) {
        //                     return formatMoney(((cost.amount - cost.discount)));
        //                 }
        //             },
        //             {
        //                 'data': "action",
        //                 defaultContent: "<button type='button' id='open_btn' class='btn btn-sm btn-rounded btn-success'>Open</button>"
        //             }
        //         ], aaSorting: [[1, 'desc']],
        //         "columnDefs": [
        //             {"orderable": false, "targets": [3, 4, 5, 6, 7]}
        //         ]

        //     });


        // }
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
                    {'data': 'receipt_number'},
                    {
                        'data': 'date', render: function (date) {
                            return moment(date).format('D-M-YYYY');
                        }
                    },
                    {'data': 'customer', render: function (customer) {
                            if(customer) {
                                return customer.name
                            }
                            return '';
                        }
                    },
                    {
                        'data': 'cost', render: function (cost) {
                            if(cost) {
                                return formatMoney(((cost.amount - cost.discount) / (1 + (cost.vat / cost.sub_total))));
                            }
                                return '';
                        }
                    },

                    {
                        'data': 'cost', render: function (cost) {
                            if(cost) {
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
                            if(cost) {
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
                    {"orderable": false, "targets": [3, 4, 5, 6, 7]}
                ]

            });


        }

        $('#sale_list_return_table tbody').on('click', '#open_btn', function () {
            var row_data = $('#sale_list_return_table').DataTable().row($(this).parents('tr')).data();
            saleReturn(row_data.details);
        });

    </script>


@endpush
