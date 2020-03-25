@extends("layouts.master")
@section('content-title')

    Order History

@endsection
@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchases / Purchase Order History</a></li>
@endsection

@section("content")

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">

                <div class="form-group row">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-3" style="margin-left: 2.5%">
                        <label for="" class="col-form-label text-md-right"
                               style="margin-left: 74.5%">Date:</label>
                    </div>
                    <div class="col-md-3" style="margin-left: -3.4%;">
                        <input style="width: 104%;" type="text" name="order_filter" class="form-control"
                               onchange="getOrderHistory()"
                               id="date_filter">
                    </div>
                </div>
                <div id="purchases">

                    <table id="order_history_datatable" class="display table nowrap table-striped table-hover"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <div hidden>
                        <a target="_blank" id="order_no"></a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('purchases.purchase_order_list.delete')
    @include('purchases.purchase_order_list.details')

@endsection
@push("page_scripts")
    <script src="{{asset("assets/apotek/js/orderlist.js")}}"></script>

    <script type="text/javascript">

        var config = {
            routes: {
                getOrderHistory: '{{route('getOrderHistory')}}'

            }
        };

        $('#order_history_datatable tbody').on('click', '#print_btn', function () {
            var data = order_history_datatable.row($(this).parents('tr')).data();

            let url = '{{route('printOrder','id','Purchase Order')}}';
            url = url.replace('id', data.details[0].order_id);
            let a = document.getElementById('order_no');
            a.href = url;
            a.click();
        });

    </script>
@endpush
