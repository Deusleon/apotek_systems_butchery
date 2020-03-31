@extends("layouts.master")
@section('content-title')
    Payment History
@endsection
@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Payment History</a></li>
@endsection

@section("content")

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">

                    <div class="form-group row">
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-3" style="margin-left: 2.5%">
                            <label style="margin-left: 62%" for=""
                                   class="col-form-label text-md-right">Customer:</label>
                        </div>
                        <div class="col-md-3" style="margin-left: -3.2%;">
                            <select name="customer_id" id="customer_payment"
                                    class="js-example-basic-single form-control" onchange="filterPaymentHistory()">
                                <option value="" selected="true" disabled>Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{$customer->id}}">{{$customer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-3" style="margin-left: 1.4%">
                            <label style="margin-left: 80%" for=""
                                   class="col-form-label text-md-right">Date:</label>
                        </div>
                        <div class="col-md-3" style="margin-left: -3%;">
                            <input style="width: 107%;" type="text" name="date_of_sale" class="form-control"
                                   id="sales_date" value="" autocomplete="off"/>
                        </div>
                    </div>

                    <div class="table-responsive" id="main_table">
                        <table id="fixed-header-main" class="display table nowrap table-striped table-hover"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Payment Date</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $payment)
                                @if($payment->paid_amount != 0)
                                    <tr>
                                        <td>{{$payment->name}}</td>
                                        <td>{{date('d-m-Y', strtotime($payment->created_at))}}</td>
                                        <td>{{number_format($payment->paid_amount,2)}}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>


                    <div class="table-responsive" id="filter_history" style="display: none">
                        <table id="fixed-header-filter" class="display table nowrap table-striped table-hover"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Payment Date</th>
                                <th>Amount</th>
                            </tr>
                            </thead>

                        </table>
                    </div>

                    <input type="hidden" value="" id="category">
                    <input type="hidden" value="" id="customers">
                    <input type="hidden" value="" id="print">

                </div>
            </div>
        </div>


        @endsection


        @push("page_scripts")
            @include('partials.notification')

            <script>
                $('#fixed-header-main').DataTable({
                    aaSorting: [[1, "desc"]]
                });

                let payment_history_filter_table = $('#fixed-header-filter').DataTable({
                    columns: [
                        {'data': 'name'},
                        {
                            'data': 'created_at', render: function (date) {
                                return moment(date).format('D-M-YYYY');
                            }
                        },
                        {
                            'data': 'paid_amount', render: function (amount) {
                                return formatMoney(amount);
                            }
                        }
                    ], aaSorting: [[1, "desc"]]
                });

                function filterPaymentHistory() {
                    let customer_id = document.getElementById('customer_payment').value;
                    let date = document.getElementById('sales_date').value;

                    if (customer_id === '') {
                        customer_id = null;
                    }

                    if (date === '') {
                        date = null;
                    }

                    /*make ajax call for more*/
                    $.ajax({
                        url: '{{route('payment-history-filter')}}',
                        type: "get",
                        dataType: "json",
                        data: {
                            customer_id: customer_id,
                            date: date
                        },
                        success: function (data) {
                            document.getElementById('main_table').style.display = 'none';
                            document.getElementById('filter_history').style.display = 'block';

                            data = data.filter(function (el) {
                                return Number(el.paid_amount) !== Number(0);
                            });

                            payment_history_filter_table.clear();
                            payment_history_filter_table.rows.add(data);
                            payment_history_filter_table.draw();


                        }
                    });


                }

                $(function () {

                    $('#sales_date').daterangepicker({
                        startDate: moment().startOf('month'),
                        autoUpdateInput: false,
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                            'This Year': [moment().startOf('year'), moment()]
                        }
                    });

                    $('input[name="date_of_sale"]').on('apply.daterangepicker', function (ev, picker) {
                        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                        filterPaymentHistory();
                    });

                    $('input[name="date_of_sale"]').on('cancel.daterangepicker', function (ev, picker) {
                        $(this).val('');
                    });

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
                    }
                }

            </script>

    @endpush
