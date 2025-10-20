@extends("layouts.master")
@section('content-title')
    Purchase Order
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Purchase Order / Order List</a></li>
@endsection

@section("content")
    <style>
        .ms-container {
            background: transparent url('../assets/plugins/multi-select/img/switch.png') no-repeat 50% 50%;
            width: 100%;
        }

        .ms-selectable,
        .ms-selection {
            background: #fff;
            color: #555555;
            float: left;
            width: 45%;
        }
    </style>

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="purchase-order-tablist"
                            href="{{ route('purchase-order.index') }}" aria-controls="purchase_order"
                            aria-selected="false">New</a>
                    </li>
                    @if(Auth::user()->checkPermission('View Order List'))
                        <li class="nav-item">
                            <a class="nav-link active text-uppercase" id="order-list-tablist" href="#order-list" role="tab"
                                aria-controls="order_list" aria-selected="true">Order List
                            </a>
                        </li>
                    @endif
                </ul>

                <div class="tab-content" id="myTabContent">
                    {{-- Order List Start--}}
                    <div class="tab-pane fade show active" id="order-list" role="tabpanel" aria-labelledby="order_list-tab">
                        {{-- <div class="form-group row">
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-3" style="margin-left: 2.5%">
                                <label for="" class="col-form-label text-md-right" style="margin-left: 74.5%">Date:</label>
                            </div>
                            <div class="col-md-3" style="margin-left: -3.4%;">
                                <input style="width: 104%;" type="text" name="order_filter" class="form-control"
                                    onchange="getOrderHistory()" id="date_filter">
                            </div>
                        </div> --}}
                        <div class="d-flex justify-content-end mb-3 align-items-center">
                            <label class="mr-2" for="date_filter">Date:</label>
                            <input type="text" name="order_filter" id="date_filter" onchange="getOrderHistory()" class="form-control w-auto">
                        </div>
                        <div id="purchases">
                            <table id="order_history_datatable" class="display table nowrap table-striped table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th hidden>ID</th>
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
                        @include('purchases.purchase_order_list.delete')
                        @include('purchases.purchase_order_list.details')
                        @include('purchases.purchase_order_list.approve')
                    </div>
                    {{-- Order List End--}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push("page_scripts")
    @include('partials.notification')
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    <script>
        var config2 = {
            managePurchaseHistory: '{{auth()->user()->hasPermissionTo('View Purchase Order')}}',
            routes: {
                getOrderHistory: '{{route('getOrderHistory')}}',
                approveOrder: '{{route('orders.approve', ['id' => ':id'])}}'
            },
            csrfToken: '{{ csrf_token() }}', // ← ADD THIS LINE
            showPurchaseOrder: '{{auth()->user()->checkPermission('Show Purchase Order')}}',
            printPurchaseOrder: '{{auth()->user()->checkPermission('Print Purchase Order')}}'
        };

        // IMPORTANT: Allow printing ONLY when approved
        $('#order_history_datatable tbody').on('click', '#print_btn', function () {
            var data = order_history_datatable.row($(this).parents('tr')).data();

            // If button is disabled in DOM, block immediately (safety)
            if ($(this).is(':disabled')) {
                alert('Order must be approved before printing.');
                return;
            }

            // Extra safety guard: respect approval in data
            var isApprovedByClient = !!data.clientApproved; // set by our Approve in modal
            var isApprovedByBackend = (data.status === '2' || data.status === '3' || data.status === 'Approved');


            // ORIGINAL LOGIC (unchanged)
            let url = '{{route('printOrder', 'id', 'Purchase Order')}}';
            url = url.replace('id', data.details[0].order_id);
            let a = document.getElementById('order_no');
            a.href = url;
            a.click();
        });
    </script>

    {{-- Order List JS --}}
    <script src="{{asset("assets/apotek/js/orderlist.js")}}"></script>
@endpush