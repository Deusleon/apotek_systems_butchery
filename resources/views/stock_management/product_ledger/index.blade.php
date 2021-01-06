@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Product Ledger
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Product Ledger </a></li>
@endsection

@section("content")

    <style>
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
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
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">


                    <div class="form-group row">
                        <label for="code" class="col-md-2 col-form-label text-md-right" style="margin-left: -6.7%">Products:</label>
                        <div class="col-md-4" style="margin-left: -2.5%;">
                            <select onchange="productLedgerFilter()" id="select_id"
                                    class="js-example-basic-single form-control">
                                <option value="0" selected="true" disabled="disabled">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{$product->product_id}}">{{$product->product_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <label for="issued_date" class="col-md-3 col-form-label text-md-right"
                               style="margin-left: 11.3%;">Date:</label>
                        <div class="col-md-3" style="margin-left: -2.5%;">
                            <input type="text" name="issued_date" class="form-control"
                                   id="d_auto_7">
                        </div>

                    </div>

                    <div id="tbodyRePrintFilter" class="table-responsive">
                        <table id="fixed-header-ledger" class="display table nowrap table-striped table-hover"
                               style="width:100%">

                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Balance</th>
                                <th>Movement</th>
                                <th>User</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>

                    </div>


                    <div id="loading">
                        <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                    </div>

                </div>
            </div>
        </div>

        @endsection




        @push("page_scripts")
            <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
            <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

            @include('partials.notification')


            <script type="text/javascript">
                var config = {
                    routes: {
                        ledgerShow: '{{ route('product-ledger-show') }}'
                    }
                };

            </script>

            <script src="{{asset("assets/apotek/js/product-ledger.js")}}"></script>


    @endpush
