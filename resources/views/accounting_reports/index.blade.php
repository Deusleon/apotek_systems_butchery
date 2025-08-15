@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Accounting Reports
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Reports / Accounting Reports </a></li>
@endsection

@section("content")

    <style>
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        #select1 {
            z-index: 10050;
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

        input[type=button]:focus {
            background-color: #748892;
            border-color: #748892;
            color: white;
        }

    </style>

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form id="inventory_report_form" action="{{route('accounting-report-filter')}}" method="get"
                      target="_blank">
                    @csrf()
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="report_option">Select Accounting Report<font
                                            color="red">*</font></label>
                                    <select id="report_option" name="report_option" onchange="reportOption()"
                                            class="js-example-basic-single form-control drop" required>
                                        <option selected="true" value="" disabled="disabled">Select report</option>
                                        <option value="1">Current Stock</option>
                                        <option value="2">Gross Profit Detail</option>
                                        <option value="3">Gross Profit Summary</option>
                                        <option value="4">Expense Report</option>
                                        <option value="5">Income Statement Report</option>
                                        <option value="6">Cost of Expired Products</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="date-row">
                        <div class="row">
                            <div class="col-md-4">
                                <div id="range">
                                    <label for="filter">Date<font color="red">*</font></label>
                                    <input type="text" class="form-control" name="date_range" id="daterange" readonly/>
                                </div>

                            </div>
                        </div>
                    </div>
                    {{--Current Stock--}}
                    <div id="current-stock-value" style="display: none">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price-category">Price Category<font color="red">*</font></label>
                                    <select id="price-category" name="price_category_id" onchange=""
                                            class="js-example-basic-single form-control drop" required>
                                        <option value="" selected="true" disabled="disabled">Select Category...</option>
                                        @foreach($price_categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="store">Store<font color="red">*</font></label>
                                    <select id="store" name="store_id" onchange=""
                                            class="js-example-basic-single form-control drop" required>
                                        <option value="" selected="true" disabled="disabled">Select Store...</option>
                                        @foreach($stores as $store)
                                            <option value="{{$store->id}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--expired product cost--}}
                    <div id="expired-product-cost" style="display: none">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price-category">Price Category<font color="red">*</font></label>
                                    <select id="price-category-expire" name="price_category_id_expire" onchange=""
                                            class="js-example-basic-single form-control drop" required>
                                        <option value="" selected="true" disabled="disabled">Select Category...</option>
                                        @foreach($price_categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="filter">Date</label>
                                <input type="text" class="form-control" name="expire_date_range" id="expiredaterange"
                                       autocomplete="off" readonly/>
                            </div>
                        </div>
                    </div>


                    <hr>
                    <div class="row">
                        <div class="col-md-5">

                        </div>
                        <div class="col-md-2">
                            {{--<a href="" target="_blank">--}}
                            <button class="btn btn-secondary" style="width: 100%">
                                Show
                            </button>
                            {{--</a>--}}
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    @include('partials.notification')


    <script type="text/javascript">
        $(function () {

            var start = moment();
            var end = moment();

            $('#expiredaterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Next 2 Weeks': [start, moment().add(13, 'days')],
                    'Next 3 Weeks': [start, moment().add(20, 'days')],
                    'Next 4 Weeks': [start, moment().add(27, 'days')]
                }
            });
        });

        $('input[name="expire_date_range"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });


        $(function () {

            var start = moment().startOf('month');
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#daterange').daterangepicker({
                startDate: start,
                endDate: end,
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

        });


        function reportOption() {
            var report_option = document.getElementById("report_option");
            var report_option_index = report_option.options[report_option.selectedIndex].value;

            if (Number(report_option_index) === Number(1)) {
                $("#price-category").prop("required", true);
                $("#store").prop("required", true);
                document.getElementById('date-row').style.display = 'none';
                document.getElementById('current-stock-value').style.display = 'block';
            } else {
                $("#price-category").prop("required", false);
                $("#store").prop("required", false);
                document.getElementById('current-stock-value').style.display = 'none';
                document.getElementById('date-row').style.display = 'block';

            }

            if (Number(report_option_index) === Number(6)) {
                $("#price-category-expire").prop("required", true);
                document.getElementById('expired-product-cost').style.display = 'block';
                document.getElementById('date-row').style.display = 'none';
                document.getElementById('current-stock-value').style.display = 'none';
            } else {
                $("#price-category-expire").prop("required", false);
                document.getElementById('expired-product-cost').style.display = 'none';

            }


        }
    </script>


@endpush
