@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Daily Stock Count
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Daily Stock Count </a></li>
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

    </style>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="new_sale" role="tabpanel" aria-labelledby="new_sale-tab">
                    <form id="daily-stock" action="{{ route('daily-stock-count-pdf-gen') }}" method="post"
                          enctype="multipart/form-data">
                        @csrf()
                        <div class="form-group row">
                            <div class="col-md-6">

                            </div>
                            <label for="issued_date" style="margin-top: 1%; margin-left: 2%"
                                   class="col-md-3 form-label text-md-right">Date:</label>
                            <div class="col-md-3" style="margin-left: -2.35%">
                                <input type="text" name="sale_date" class="form-control"
                                       id="d_auto_8" value="{{$today}}" required>
                            </div>
                        </div>


                        <!-- ajax loading gif -->
                        <div id="loading">
                            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                        </div>


                        <div id="tbody2" class="table-responsive">
                            <table id="fixed-header2" class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Sold Quantity</th>
                                    <th>Quantity on Hand</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{$product['product_name']}}</td>
                                        <td>{{$product['quantity_sold']}}</td>
                                        <td>{{$product['quantity_on_hand']}}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>


                        <hr hidden>
                        <div class="row" hidden>
                            <div class="col-md-10">

                            </div>
                            <div class="col-md-2">
                                <div style="width: 99%">
                                    <label><b>Total Amount</b></label>
                                    <input type="text" id="total" name="sub_total_amount" class="form-control" readonly
                                           value="0" onchange="filterByDate()"/>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" id="order_cart" name="cart">
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group" style="float: right;">
                                    <button class="btn btn-primary" hidden>Transfer</button>
                                    <button class="btn btn-danger" id="deselect-all" hidden>Cancel</button>
                                    <button class="btn btn-secondary" type="submit">
                                        <span class="fa fa-print" aria-hidden="true"></span> Print
                                    </button>
                                </div>
                            </div>
                        </div>


                    </form>

                </div>
            </div>
        </div>


        @endsection


        @push("page_scripts")

            @include('partials.notification')
            <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
            <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
            <script type="text/javascript">
                var config = {
                    routes: {
                        filterShow: '{{ route('daily-stock-count-filter') }}'
                    }
                };

            </script>
            <script src="{{asset("assets/apotek/js/outgoing-stock.js")}}"></script>



    @endpush
