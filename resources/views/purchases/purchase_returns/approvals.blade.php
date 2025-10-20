@extends("layouts.master")

@section('content-title')
    Purchase Returns
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Purchase Returns / Approvals</a></li>
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

        #return_status,
        .select2-container {
            width: 200px !important;
        }
    </style>

    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            @if(Auth::user()->checkPermission('View Purchase Return'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="purchase-return-tablist"
                        href="{{ url('purchasing/purchase-returns') }}">Returns</a>
                </li>
            @endif
            @if(Auth::user()->checkPermission('View Purchase Returns Approval'))
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" id="purchase-approval-tablist"
                        href="{{ url('purchasing/purchase_returns/approvals') }}">Approvals</a>
                </li>
            @endif
        </ul>

        @if(Auth::user()->checkPermission('View Purchase Returns Approval'))
            <div class="tab-content" id="myTabContent">
                <div class="d-flex justify-content-end mb-3 align-items-center">
                    <label class="mr-2" for="">Status:</label>
                    <select id="return_status" class="js-example-basic-single form-control" onchange="getPurchaseReturns()">
                        <option value="2">Pending</option>
                        <option value="3">Approved</option>
                        <option value="4">Rejected</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end mb-3 align-items-center">
                    <label class="mr-2" for="">Date:</label>
                    <input type="text" id="return_date" onchange="getPurchaseReturns()" class="form-control w-auto">
                </div>

                <div class="table-responsive">
                    <table id="purchase_returns_table" class="display table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Receive Date</th>
                                <th>Qty Received</th>
                                <th>Return Date</th>
                                <th>Qty Returned</th>
                                <th>Refund Amount</th>
                                @if(Auth::user()->checkPermission('Approve Purchase Returns'))
                                    <th>Action</th>
                                @else
                                    <th style="display: none"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <!-- ajax loading gif -->
                    <div id="loading">
                        <img id="loading-image" src="{{asset('assets/images/spinner.gif')}}">
                    </div>

                    <input type="hidden" value="" id="category">
                    <input type="hidden" value="" id="customers">
                    <input type="hidden" value="" id="print">
                    <input type="hidden" value="" id="fixed_price">
                </div>
            </div>
        @endif

        @if(!Auth::user()->checkPermission('View Purchase Returns Approval'))
            <div class="card">
                <div class="card-body">
                    <div class="form-group row">
                        <p>You do not have permission to View Purchase Returns Approval</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection

@push("page_scripts")
    @include('partials.notification')

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {
            // Initialize date range picker
            var start = moment().startOf('month');
            var end = moment().endOf('month');

            function cb(start, end) {
                $('#return_date').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#return_date').daterangepicker({
                startDate: start,
                endDate: end,
                autoUpdateInput: true,
                locale: { format: 'YYYY/MM/DD' },
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

            // Initial load
            setTimeout(function () { getPurchaseReturns(); }, 100);
        });

        $('#purchase_returns_table tbody').on('click', '#approve', function () {
            var rowData = purchase_returns_table.row($(this).parents('tr')).data();
            getPurchaseReturns('approve', {
                id: rowData.goods_receiving.id,
                product_id: rowData.goods_receiving.product_id,
                quantity: rowData.goods_receiving.quantity
            });
        });

        $('#purchase_returns_table tbody').on('click', '#reject', function () {
            var rowData = purchase_returns_table.row($(this).parents('tr')).data();
            getPurchaseReturns('reject', {
                id: rowData.goods_receiving.id,
                product_id: rowData.goods_receiving.product_id,
                quantity: rowData.goods_receiving.quantity
            });
        });

        var purchase_returns_table = $('#purchase_returns_table').DataTable({
            bPaginate: true,
            bInfo: true,
            columns: [
                {
                    data: 'goods_receiving',
                    render: function (item) {
                        if (!item || !item.product) return '';
                        const product = item.product;
                        return `${product.name || ''} ${product.brand || ''} ${product.pack_size || ''}${product.sales_uom || ''}`;
                    }
                },
                {
                    data: 'goods_receiving.created_at',
                    render: function (date) { return moment(date).format('YYYY-MM-DD'); }
                },
                {
                    data: 'goods_receiving.quantity',
                    render: function (data) { return numberWithCommas(Math.floor(data)); }
                },
                {
                    data: 'date',
                    render: function (date) { return moment(date).format('YYYY-MM-DD'); }
                },
                {
                    data: 'quantity',
                    render: function (data) { return numberWithCommas(Math.floor(data)); }
                },
                {
                    data: 'goods_receiving',
                    render: function (item, type, row) {
                        const unit_cost = parseFloat(item.unit_cost) || 0;
                        const rtn_qty = parseFloat(row.quantity) || 0;
                        const total = unit_cost * rtn_qty;
                        return formatMoney(total);
                    }
                },
                @if(Auth::user()->checkPermission('Approve Purchase Returns'))
                    {
                        data: "action",
                        defaultContent: "<button type='button' id='approve' class='btn btn-sm btn-rounded btn-primary'>Approve</button> <button type='button' id='reject' class='btn btn-sm btn-rounded btn-danger'>Reject</button>"
                    }
                @else
                    {
                        data: "",
                        defaultContent: "", visible: false
                    }
                @endif
            ],
            aaSorting: [[1, "desc"]]
        });

        function getPurchaseReturns(action, goods_receiving) {
            var status = document.getElementById("return_status").value;
            var range = document.getElementById("return_date").value;
            var date = range ? range.split(' - ').map(d => d.trim()) : [];

            $('#loading').show();

            $.ajax({
                url: '{{route('getPurchaseReturns')}}',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "date": date,
                    "status": status,
                    "action": action,
                    "goods_receiving": goods_receiving
                },
                type: 'get',
                dataType: 'json',
                cache: false,
                success: function (data) {
                    if (action === 'approve' || action === 'reject') {
                        setTimeout(function () { getPurchaseReturns(); }, 100);
                        return;
                    } else {
                        if (status == 3 || status == 4) {
                            purchase_returns_table.column(6).visible(false);
                        } else {
                            purchase_returns_table.column(6).visible(true);
                        }
                        purchase_returns_table.clear();
                        purchase_returns_table.rows.add(data);
                        purchase_returns_table.draw();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', error, xhr.responseText);
                    alert('An error occurred while processing your request.');
                },
                complete: function () { $('#loading').hide(); }
            });
        }

        $('#searching_returns').on('keyup', function () {
            purchase_returns_table.search(this.value).draw();
        });

        function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

                const negativeSign = amount < 0 ? "-" : "";
                let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
                let j = (i.length > 3) ? i.length % 3 : 0;

                return negativeSign + (j ? i.substr(0, j) + thousands : '') +
                    i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) +
                    (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
            } catch (e) {
                console.log(e)
            }
        }

        function numberWithCommas(digit) {
            return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Tab redirects
        $(document).ready(function () {
            $('#purchase-return-tablist').on('click', function (e) {
                e.preventDefault();
                window.location.href = $(this).attr('href');
            });

            $('#purchase-approval-tablist').on('click', function (e) {
                e.preventDefault();
                window.location.href = $(this).attr('href');
            });
        });
    </script>

    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

@endpush