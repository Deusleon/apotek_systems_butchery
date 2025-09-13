@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Outgoing Stock
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Outgoing Stock </a></li>
@endsection

@section("content")

    <style>
        .datepicker>.datepicker-days {
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
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="daily-stock-tablist" data-toggle="pill"
                    href="{{ url('inventory/daily-stock-count') }}" role="tab" aria-controls="stock_adjustment"
                    aria-selected="true">Daily Stock Count</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="outgoing-stock-tablist" data-toggle="pill"
                    href="{{ url('inventory/out-going-stock') }}" role="tab" aria-controls="stock_list"
                    aria-selected="false">Outgoing Stock
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="count-sheet-tablist"
                    href="{{ url('inventory/inventory-count-sheet/Inventory Count Sheet') }}" role="tab"
                    aria-controls="stock_list" aria-selected="false" target="_blank">Inventory Count Sheet
                </a>
            </li>
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                    <div class="form-group row">
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-3" style="margin-left: 2.5%">
                            <label style="margin-left: 80%" for="issued_date"
                                class="col-form-label text-md-right">Date:</label>
                        </div>
                        <div class="col-md-3" style="margin-left: -3.1%">
                            <input style="width: 103.4%;" type="text" name="outgoing-date"
                                class="form-control" id="outgoing-date" value="" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-3">
                            <div class="row justify-content-end">
                                <label for="category" class="col-form-label text-md-right">Type:</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="js-example-basic-single form-control" id="category_id">
                                <option name="store_name" value="1">Summary</option>
                                <option name="store_name" value="0">Detailed</option>
                            </select>
                        </div>
                    </div>
                    {{-- Summary --}}
                    <div id="tbody" class="table-responsive">
                        <table id="fixedHeader" class="display table nowrap table-striped table-hover" style="width:100%">

                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>QoH</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>

                    </div>
                    {{-- Detailed --}}
                    <div id="detailedTable" style="display: none;" class="table-responsive">
                        <table id="fixedHeader2" class="display table nowrap table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Out Mode</th>
                                    <th>Quantity</th>
                                    <th>Batch #</th>
                                    <th>Date</th>
                                    <th>QoH</th>
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
                    ledgerShow: '{{ route('outgoing-stock-show') }}'
                }
            };

        </script>

        <script src="{{asset("assets/apotek/js/outgoing-stock.js")}}"></script>

        <script>
            $(document).ready(function () {
                // Listen for the click event on the Transfer History tab
                $('#daily-stock-tablist').on('click', function (e) {
                    e.preventDefault(); // Prevent default tab switching behavior
                    var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                    window.location.href = redirectUrl; // Redirect to the URL
                });

            });
        </script>


    @endpush