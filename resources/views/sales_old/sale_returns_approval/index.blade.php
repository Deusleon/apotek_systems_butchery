@extends("layouts.master")

@section('content-title')
    Sale Returns Approval
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Sale Returns Approval</a></li>
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
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="sales-history-tablist" data-toggle="pill"
                   href="{{ route('sale-histories.SalesHistory') }}" role="tab"
                   aria-controls="sales_history" aria-selected="true">Sales History</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="sales-return-tablist" data-toggle="pill"
                   href="{{ route('sale-returns.index') }}" role="tab"
                   aria-controls="sales_returns" aria-selected="false">Returns
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="sales-approval-tablist" data-toggle="pill"
                   href="{{ route('sale-returns-approval.getSalesReturn') }}" role="tab"
                   aria-controls="sales_returns" aria-selected="false">Approval
                </a>
            </li>
        </ul>

        @if(Auth::user()->checkPermission('View Sales Return Approval'))
        <div class="tab-content" id="myTabContent">

            <div class="form-group row">
                <div class="col-md-6">

                </div>
                <div class="col-md-3" style="margin-left: 2.5%">
                    <label style="margin-left: 74%" for=""
                           class="col-form-label text-md-right">Status:</label>
                </div>
                <div class="col-md-3" style="margin-left: -3.2%;">
                    <select id="retun_status"
                            class="js-example-basic-single form-control" onchange="getRetunedProducts()">
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
                <table id="return_table" class="display table nowrap table-striped table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Sales Date</th>
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
        @endif

        @if(!Auth::user()->checkPermission('View Sales Return Approval'))
            <div class="card">
                <div class="card-body">
                    <div class="form-group row">

                        <p>You do not have permission to View Sale Return Approval</p>

                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@push("page_scripts")
    @include('partials.notification')

    <script type="text/javascript">

        $(function () {
            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#returned_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#returned_date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: end,
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
                {data: 'item_returned.name'},
                {
                    data: 'item_returned.b_date', render: function (date) {
                        return moment(date).format('D-MM-YYYY');
                    }
                },
                {
                    data: 'item_returned.bought_qty',
                    render: function (data)
                    {
                        return Math.floor(data);
                    }
                },
                {
                    data: 'date', render: function (date) {
                        return moment(date).format('D-MM-YYYY');
                    }
                },
                {
                    data: 'item_returned.rtn_qty',
                    render: function (data)
                    {
                        return Math.floor(data);
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

    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    <script src="{{asset("assets/js/pages/sales.js")}}"></script>


    <script>
        $(document).ready(function() {
            // Listen for the click event on the Transfer History tab
            $('#sales-history-tablist').on('click', function(e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#sales-return-tablist').on('click', function(e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#sales-approval-tablist').on('click', function(e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });
        });
    </script>





@endpush
{{--@if($count>0)
<tr>
@foreach($sales_returns as $s_return)
<td>{{$s_return->item_returned->name}}</td>
<td>{{date('j M, Y', strtotime($s_return->item_returned->b_date))}}</td>
@if($s_return->item_returned->status == 3||$s_return->item_returned->status == 5)

<td>{{$s_return->item_returned->bought_qty+$s_return->quantity}}</td>
@else
<td>{{$s_return->item_returned->bought_qty}}</td>
@endif


<td>{{date('j M, Y', strtotime($s_return->date))}}</td>
<td>{{$s_return->quantity}}</td>
@if($s_return->item_returned->bought_qty != 0)
<td>{{number_format((($s_return->item_returned->amount-$s_return->item_returned->discount)/$s_return->item_returned->bought_qty)*$s_return->quantity,2)}}</td>
@else
<td>{{($s_return->item_returned->amount/1)*$s_return->quantity}}</td>
@endif
<td>
@if($s_return->item_returned->status == 2)
<button class="btn btn-sm btn-rounded btn-success"
data-r_qty="{{$s_return->quantity}}"
data-stock-id="{{$s_return->item_returned->stock_id}}"
data-qty="{{$s_return->item_returned->bought_qty}}"
data-item_detail_id="{{$s_return->item_returned->item_detail_id}}"
data-reason="{{$s_return->reason}}"
type="button"data-toggle="modal"
data-target="#approve">Approve
</button>
<button class="btn btn-sm btn-rounded btn-danger"
data-id="{{$s_return->id}}"
data-item_detail_id="{{$s_return->item_returned->item_detail_id}}"
data-reason="{{$s_return->reason}}"
type="button"data-toggle="modal"
data-target="#reject">Reject
</button>
@elseif($s_return->item_returned->status == 4)
<h4><span class="badge badge-danger">Rejected !</span></h4>
@else
<h4><span class="badge badge-success">Approved</span></h4>

@endif

</td>
</tr>

@endforeach
@endif--}}
