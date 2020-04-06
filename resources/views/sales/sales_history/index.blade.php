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
            <div class="tab-content" id="myTabContent">
                <input type="hidden" value="{{$vat}}" id="vat">

                <div class="form-group row">
                    <div class="col-md-6"></div>
                    <div class="col-md-3" style="margin-left: 2.5%">
                        <label style="margin-left: 80%" for="filter" class="col-form-label text-md-right">Date:</label>
                    </div>
                    <div class="col-md-3" style="margin-left: -3.4%">
                        <input style="width: 103.4%;" type="text" class="form-control" id="daterange"/>
                    </div>

                </div>
                <form id="sale_receipt_reprint" action="{{route('sale-reprint-receipt')}}" method="post"
                      enctype="multipart/form-data" target="_blank">
                    @csrf()

                    <div class="table-responsive" id="sales">

                        <table id="sale_history_table" class="display table nowrap table-striped table-hover"
                               style="width:100%">

                            <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Sale Type</th>
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

        </div>


    </div>
    </div>
    @include('sales.sales_history.details')

@endsection

@push("page_scripts")
    <script src="{{asset("assets/apotek/js/sales.js")}}"></script>

    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function getHistory() {
            var range = document.getElementById('daterange').value;
            range = range.split('-');

            $("#sale_history_table").dataTable().fnDestroy();

            $('#sale_history_table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": config.routes.getSalesHistory,
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
                    {'data': 'cost.name'},
                    {
                        'data': 'cost', render: function (cost) {
                            return formatMoney(((cost.amount - cost.discount) / (1 + (cost.vat / cost.sub_total))));
                        }
                    },

                    {
                        'data': 'cost', render: function (cost) {
                            return formatMoney(((cost.amount - cost.discount) * (cost.vat / cost.sub_total)));
                        }
                    },
                    {
                        'data': 'cost.discount', render: function (discount) {
                            return formatMoney(discount);
                        }
                    },
                    {
                        'data': 'cost', render: function (cost) {
                            return formatMoney(((cost.amount - cost.discount)));
                        }
                    },
                    {
                        'data': "action",
                        defaultContent: "<button type='button' id='sale_details' class='btn btn-sm btn-rounded btn-success'>Show</button><button type='submit' id='sale_receipt_reprint' class='btn btn-sm btn-rounded btn-secondary'><span class='fa fa-print' aria-hidden='true'></span>Print</button>"
                    }
                ], aaSorting: [[1, 'desc']],
                "columnDefs": [
                    {"orderable": false, "targets": [3, 4, 5, 6, 7]}
                ]

            });


        }

        $('#sale_history_table tbody').on('click', '#sale_details', function () {
            var row_data = $('#sale_history_table').DataTable().row($(this).parents('tr')).data();
            $('#sale-details').modal('show');
            var items = row_data.details;
            sold = " <span class='badge badge-success'>Sold</span>";
            pending = " <span class='badge badge-secondary'>Pending</span>";
            returned = " <span class='badge badge-danger'>Returned</span>";
            sale_items = [];
            items.forEach(function (item) {
                var item_data = [];
                item_data.push(item.id);
                item_data.push(item.name);
                item_data.push(item.quantity);
                item_data.push((item.price / item.quantity));
                item_data.push(item.vat);
                item_data.push(item.discount);
                item_data.push(item.amount);
                if (item.status == 2) {
                    item_data.push(pending)
                } else if (item.status == 3) {
                    item_data.push(returned)
                } else {
                    item_data.push(sold)
                }
                sale_items.push(item_data);
            });
            items_table.clear();
            items_table.rows.add(sale_items);
            items_table.columns([0]).visible(false);
            items_table.draw();
        });

        $('#sale_history_table tbody').on('click', '#sale_receipt_reprint', function () {
            var row_data = $('#sale_history_table').DataTable().row($(this).parents('tr')).data();
            document.getElementById("print").value = row_data.receipt_number;
        });


        var config = {
            token: '{{ csrf_token() }}',
            routes: {
                getSalesHistory: '{{route('getSalesHistory')}}'

            }
        };

    </script>
    <script type="text/javascript">
        $(function () {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#daterange').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: end,
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
