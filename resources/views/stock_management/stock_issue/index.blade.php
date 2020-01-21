@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Stock Issue
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Issue </a></li>
@endsection


@section("content")
    <style>
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

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

        .sortable-handler {
            touch-action: none;
        }

    </style>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="new_sale" role="tabpanel" aria-labelledby="new_sale-tab">
                    <form id="issue" action="{{ route('stock-issue.store') }}" method="post"
                          enctype="multipart/form-data">
                        @csrf()
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Products</label>
                                    <select id="select_id"
                                            class="js-example-basic-single form-control">
                                        <option value="">Select Product</option>
                                        @foreach($products as $stock)
                                            <option
                                                value="{{$stock['product_name'].','.$stock['unit_cost'].','.$stock['selling_price'].','.$stock['id'].','.$stock['quantity'].','.$stock['product_id']}}">{{$stock['product_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">

                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">Issued To</label>
                                    <select id="from_id" name="from_id" class="js-example-basic-single form-control">
                                        <option selected="true" disabled="disabled" value="0">Select location...
                                        </option>
                                        @foreach($locations as $location)
                                            <option value="{{$location->id}}">{{$location->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" hidden>
                                <div class="form-group">
                                    <label for="issued_date">Issue Date</label>
                                    <div id="date_border" style="border: 2px solid white; border-radius: 6px;">
                                        <input type="text" name="issued_date" class="form-control"
                                               id="d_auto_6">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ajax loading gif -->
                        <div id="loading">
                            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                        </div>


                        <div id="tbody" style="display: block;" class="table-responsive">
                            <table id="fixed-header1" class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <!-- <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Buying Price</th>
                                    <th>Selling Price</th>
                                    <th>Sub Total</th>
                                    <th>Issued Quantity</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody> -->

                            </table>
                        </div>


                        <hr>
                        <div class="row">
                            <div class="col-md-10">

                            </div>
                            <div class="col-md-2">
                                <div style="width: 99%">
                                    <label><b>Total Amount</b></label>
                                    <input type="text" id="total" name="sub_total_amount" class="form-control" readonly
                                           value="0"/>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" id="order_cart" name="cart">
                                <input type="hidden" id="product_option" value="{{json_encode($products)}}">

                            </div>
                            <div class="col-md-6">
                                <div class="btn-group" style="float: right;">
                                    <button class="btn btn-primary" hidden>Transfer</button>
                                    <a href="{{ route('stock-issue-history') }}">
                                        <button type="button" class="btn btn-danger">Back</button>
                                    </a>
                                    <button class="btn btn-warning" id="deselect-all">Clear</button>
                                    <button class="btn btn-secondary" type="submit">
                                        <span class="fa fa-print" aria-hidden="true"></span> Record
                                    </button>
                                </div>
                            </div>
                        </div>


                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection


@push("page_scripts")

    @include('partials.notification')
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    <script src="{{asset("assets/apotek/js/stock-issue.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>


@endpush
