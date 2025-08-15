@extends("layouts.master")



@section('content-title')
Transport Reports
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Reports</a></li>
    <li class="breadcrumb-item active">Transport Reports</li>
@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card">
            <!-- <div class="card-header bg-white">
                <h5 class="card-title mb-0">Generate Transport Report</h5>
            </div> -->
            <div class="card-body">
                    <form id="transport_report_form" method="POST" action="{{ route('transport-reports.generate') }}" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="report_option">Select Report Type</label>
                                    <select id="report_option" name="report_option" onchange="reportOption()"
                                            class="form-control select2" required>
                                        <option value="" selected disabled>Select report type</option>
                                        <option value="4">Payment Report</option>
                                        <option value="1">Transport Order Report</option>
                                        <option value="2">Transporter Report</option>
                                        <option value="3">Vehicle Report</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Report Options -->
                        <div id="payment_options" class="report-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="order_id">Order Number</label>
                                        <select name="transport_order_id" class="form-control select2" id="order_id">
                                            <option value="" selected>All Orders</option>
                                            @foreach($orders as $order)
                                                <option value="{{ $order->id }}">{{ $order->order_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment Date</label>
                                        <input type="date" name="payment_date" class="form-control" id="payment_date">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transport Order Report Options -->
                        <div id="transport_order_options" class="report-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="transport_order_id">Order Number</label>
                                        <select name="transport_order_id" class="form-control select2" id="transport_order_id">
                                            <option value="" selected>All Orders</option>
                                            @foreach($orders as $order)
                                                <option value="{{ $order->id }}">{{ $order->order_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date Range</label>
                                        <input type="text" name="order_date_range" class="form-control" id="order_date_range" placeholder="Select date range">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transporter Report Options -->
                        <div id="transporter_options" class="report-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="transporter_id">Transporter</label>
                                        <select name="transporter_id" class="form-control select2" id="transporter_id">
                                            <option value="" selected>All Transporters</option>
                                            @foreach($transporters as $transporter)
                                                <option value="{{ $transporter->id }}">{{ $transporter->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date Range</label>
                                        <input type="text" name="transporter_date_range" class="form-control" id="transporter_date_range" placeholder="Select date range">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Report Options -->
                        <div id="vehicle_options" class="report-options" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vehicle_id">Vehicle</label>
                                        <select name="vehicle_id" class="form-control select2" id="vehicle_id">
                                            <option value="" selected>All Vehicles</option>
                                            @foreach($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}">{{ $vehicle->registration_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date Range</label>
                                        <input type="text" name="vehicle_date_range" class="form-control" id="vehicle_date_range" placeholder="Select date range">
                                    </div>
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
        <!-- Loading Overlay -->
        <!-- <div id="loading-overlay">
            <div class="loading-spinner"></div>
            <div class="mt-3 text-muted">Generating report...</div>
        </div> -->
@endsection

@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/plugins/daterangepicker/daterangepicker.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    <script>
        // Initialize when DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize select2
            $('.select2').select2({
                width: '100%',
                placeholder: "Select an option",
                allowClear: true
            });

            // Initialize date range pickers
            const dateRangeOptions = {
                opens: 'left',
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Apply',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom',
                    daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    firstDay: 1
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            };

            // Apply to all date range inputs
            $('#order_date_range, #transporter_date_range, #vehicle_date_range').daterangepicker(dateRangeOptions)
                .on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                })
                .on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                });

            // Form submission handler
            $('#transport_report_form').on('submit', function(e) {
                // Show loading overlay
                $('#loading-overlay').fadeIn();
                
                // Hide after 3 seconds if still visible (fallback)
                setTimeout(function() {
                    $('#loading-overlay').fadeOut();
                }, 3000);
            });

            // Handle browser back button more gracefully
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        });

        // Toggle report options based on selection
        function reportOption() {
            const reportOptionValue = $('#report_option').val();
            
            // Hide all options first
            $('.report-options').hide();
            
            // Show selected option
            switch(reportOptionValue) {
                case '4':
                    $('#payment_options').show();
                    break;
                case '1':
                    $('#transport_order_options').show();
                    break;
                case '2':
                    $('#transporter_options').show();
                    break;
                case '3':
                    $('#vehicle_options').show();
                    break;
            }
            
            // Reinitialize select2 for visible selects
            $('.report-options:visible .select2').select2({
                width: '100%',
                placeholder: "Select an option",
                allowClear: true
            });
        }
    </script>
@endpush