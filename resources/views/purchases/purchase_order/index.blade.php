@extends("layouts.master")
@section('content-title')
    Purchase Order

@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Purchase Order</a></li>
@endsection


@section("content")
    <style>
        .ms-container {
            background: transparent url('../assets/plugins/multi-select/img/switch.png') no-repeat 50% 50%;
            width: 100%;
        }

        .ms-selectable, .ms-selection {
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
                        <a class="nav-link active text-uppercase" id="purchase-order-tablist" data-toggle="pill"
                           href="#purchase-order" role="tab"
                           aria-controls="purchase_order" aria-selected="true">New</a>
                    </li>
                    @if(Auth::user()->checkPermission('View Order List'))
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="order-list-tablist" data-toggle="pill"
                           href="#order-list" role="tab"
                           aria-controls="order_list" aria-selected="false">Order List
                        </a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content" id="myTabContent">
                    {{-- Purchase Order Start--}}
                    <div class="tab-pane fade show active" id="purchase-order" role="tabpanel" aria-labelledby="purchase_order-tab">
                    <form action="{{ route('purchase-order.store') }}" method="post" enctype="multipart/form-data"
                          id="order_form">
                        @csrf()
                        <div class="row">
                            <div class="col-md-2" hidden>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">Supplier Name <font color="red">*</font></label>
                                    <select name="supplier" class="js-example-basic-single form-control" id="supplier"
                                            required="true" onchange="filterSupplierProduct()">
                                        <option selected="true" disabled="disabled" value="">Select Supplier...</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label for="code">Products <font color="red">*</font></label>
                                    <select id="select_id" class="form-control">
                                        <option selected="true" disabled="disabled" value="">Select Product...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="detail">
                            <hr>
                            <div class="table responsive" style="width: 100%;">
                                <table id="cart_table" class="table nowrap table-striped table-hover"
                                       width="100%"></table>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group" style="padding-top: 10px">
                                    <div style="width: 99%">
                                        <label> <b> Remarks</b></label>
                                        <textarea class="form-control" id="note" name="note" rows="2"
                                                  placeholder="Enter Remarks Here.."></textarea>
                                    </div>
                                </div>
                            </div>
                            <!--   <div class="col-md-4">
                                  <div style="width: 99%">
                                  <label>Discount</label> -->
                            <input type="hidden" id="purchase_discount" name="discount_amount" class="form-control"
                                   value="0"/>
                            <!--       </div>
                              </div> -->
                            <div class="col-md-4">
                                <div class="row">
                                    <label class="col-md-6 col-form-label text-md-right"><b>Sub Total:</b></label>
                                    <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                        <input type="text" id="sub_total" name="sub_total_amount"
                                               class="form-control-plaintext text-md-right" readonly value="0.00"/>
                                    </div>
                                </div>
                                <div class="row">
                                    <label
                                        class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                    <div class="col-md-6"
                                         style="float: right; display: flex; justify-content: flex-end">
                                        <input type="text" id="vat" name="vat_total_amount"
                                               class="form-control-plaintext text-md-right" readonly value="0.00"/>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-md-6 col-form-label text-md-right"><b>Total
                                            Amount:</b></label>
                                    <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                        <input type="text" id="total" name="total_amount"
                                               class="form-control-plaintext text-md-right"
                                               readonly value="0.00"/>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <hr>
                        <input type="hidden" id="order_cart" name="cart">
                        <input type="hidden" id="id_vat" name="vat">
                        <input type="hidden" id="total_price" name="total_amount">
                        <input type="hidden" id="sub_total_price" name="sub_total_amount"/>
                        <input type="hidden" value="{{$vat}}" id="vats">
                        <input type="hidden" id="supplier_ids" name="supplier_ids">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group" style="float: right;">
                                    <button type="button" class="btn btn-danger" id="deselect-all"
                                            onclick="return false">Cancel
                                    </button>
                                    <button class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>

                    {{-- Purchase Order End--}}

                    {{-- Order List Start--}}
                    <div class="tab-pane fade" id="order-list" role="tabpanel" aria-labelledby="order_list-tab">
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
                        @include('purchases.purchase_order_list.delete')
                        @include('purchases.purchase_order_list.details')
                    </div>
                    {{-- Order List End--}}

                </div>

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
        var config = {
            routes: {
                filterSupplierProduct: '{{route('filter-product')}}',
                filterSupplierProductInput: '{{route('filter-product-input')}}'
            }
        };
    </script>



    <script src="{{asset("assets/apotek/js/purchases.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>

    {{--  Order List  --}}
    <script src="{{asset("assets/apotek/js/orderlist.js")}}"></script>

    <script type="text/javascript">

        var config2 = {
            managePurchaseHistory : '{{auth()->user()->hasPermissionTo('View Purchase Order')}}',
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
