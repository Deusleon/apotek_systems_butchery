@extends("layouts.master")

@section('page_css')
    <style>

    </style>
@endsection

@section('content-title')
    Stock Count
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Count / Outgoing Stock</a></li>
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
            @if(auth()->user()->checkPermission('View Stock Count'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="daily-stock-tablist" data-toggle="pill"
                        href="{{ url('inventory/daily-stock-count') }}" role="tab" aria-controls="stock_adjustment"
                        aria-selected="true">Daily Stock Count</a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Outgoing Stock'))
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" id="outgoing-stock-tablist" data-toggle="pill"
                        href="{{ url('inventory/out-going-stock') }}" role="tab" aria-controls="stock_list"
                        aria-selected="false">Outgoing Stock
                    </a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Inv. Count Sheet'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="count-sheet-tablist"
                        href="{{ url('inventory/inventory-count-sheet/Inventory Count Sheet') }}" role="tab"
                        aria-controls="stock_list" aria-selected="false" target="_blank">Inventory Count Sheet
                    </a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Taking'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="count-sheet-tablist"
                        href="{{ route('stock-taking') }}" role="tab"
                        aria-selected="false">Stock Taking
                    </a>
                </li>
            @endif
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="d-flex justify-content-end mb-3 align-items-center">
                        <label class="mr-2" for="">Date:</label>
                        <input type="text" name="outgoing-date" id="outgoing-date" class="form-control w-auto">
                    </div>
                    <div class="d-flex justify-content-end mb-3">
                        <div class="d-flex align-items-center" style="width: 248px;">
                            <label for="price_category" class="form-label mb-0"
                                style="white-space: nowrap; margin-right: 10px;">Type:</label>
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
                                    {{-- <th>QoH</th> --}}
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
                                    <th>Date</th>
                                    <th>Created By</th>
                                    {{-- <th>QoH</th> --}}
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

        <!-- Modal -->
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Inventory Count Sheet</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Do you want to show Quantity on Hand (QoH) on the printout?
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> --}}
                        <button type="button" id="confirmNo" class="btn btn-secondary">No </button>
                        <button type="button" id="confirmYes" class="btn btn-primary">Yes</button>
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
                    window.location.href = redirectUrl;
                });

            });

            $(document).ready(function () {
                var baseUrl = $('#count-sheet-tablist').attr('href');

                $('#count-sheet-tablist').on('click', function (e) {
                    e.preventDefault();
                    $('#confirmModal').modal('show');
                });

                $('#confirmYes').on('click', function () {
                    $('#confirmModal').modal('hide');
                    window.open(baseUrl + '?showQoH=1', '_blank');
                });

                $('#confirmNo').on('click', function () {
                    $('#confirmModal').modal('hide');
                    window.open(baseUrl + '?showQoH=0', '_blank');
                });
            });
        </script>
    @endpush