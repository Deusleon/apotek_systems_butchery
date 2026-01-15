@extends("layouts.master")

@section('page_css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .datepicker>.datepicker-days { display: block; }
        .text-center { text-align: center; }
        .price-section {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
        }
        .price-section h6 {
            margin-bottom: 15px;
            color: #1f273b;
            font-weight: bold;
        }
    </style>
@endsection

@section('content-title')
    Production & Distribution Reports
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Reports / Production & Distribution Reports </a></li>
@endsection

@section("content")
<div class="col-sm-12">
    <div class="card">
        <div class="card-body">
            <form id="report_form" action="" method="get" target="_blank">
                @csrf()
                
                <!-- Report Type Selection -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="report_option">Select Report Type <span class="text-danger">*</span></label>
                            <select id="report_option" name="report_option" class="form-control" onchange="reportOption()">
                                <option value="" selected disabled>Select report</option>
                                <option value="1">Production Report</option>
                                <option value="2">Distribution Report</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Date Range - Common for both reports -->
                <div class="row mb-3" id="date_section" style="display: none;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date_range">Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="date_range" id="date_range" readonly />
                        </div>
                    </div>
                </div>

                <!-- Distribution Report Options -->
                <div class="row mb-3" id="distribution_options" style="display: none;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="store_id">Branch (Optional)</label>
                            <select name="store_id" id="store_id" class="form-control">
                                <option value="">All Branches</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Price Section - Production Report Only (Hidden) -->
                <div class="price-section" id="price_section" style="display: none;" hidden>
                    <h6><i class="feather icon-tag"></i> Selling Prices per kg (auto-populated from system)</h6>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="meat_price">Meat (Nyama)</label>
                                <input type="text" class="form-control bg-light" id="meat_price" value="{{ number_format($meatPrices['meat'] ?? 0, 2) }}" readonly />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="steak_price">Steak</label>
                                <input type="text" class="form-control bg-light" id="steak_price" value="{{ number_format($meatPrices['steak'] ?? 0, 2) }}" readonly />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="beef_fillet_price">Beef Fillet</label>
                                <input type="text" class="form-control bg-light" id="beef_fillet_price" value="{{ number_format($meatPrices['beef_fillet'] ?? 0, 2) }}" readonly />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="beef_liver_price">Beef Liver</label>
                                <input type="text" class="form-control bg-light" id="beef_liver_price" value="{{ number_format($meatPrices['beef_liver'] ?? 0, 2) }}" readonly />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="tripe_price">Tripe</label>
                                <input type="text" class="form-control bg-light" id="tripe_price" value="{{ number_format($meatPrices['tripe'] ?? 0, 2) }}" readonly />
                            </div>
                        </div>
                    </div>
                    <small class="text-muted"><i class="feather icon-info"></i> Prices are fetched automatically from the product selling prices.</small>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-5"></div>
                    <div class="col-md-2">
                        <button class="btn btn-secondary" style="width: 100%">Show</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push("page_scripts")
<script src="{{asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function () {
        $('#date_range').daterangepicker({
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            autoUpdateInput: true,
            locale: { cancelLabel: 'Clear', format: 'YYYY/MM/DD' }
        });
        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        });
        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });

    function reportOption() {
        var reportType = $('#report_option').val();
        
        // Hide all optional sections first
        $('#date_section').hide();
        $('#distribution_options').hide();
        $('#price_section').hide();
        
        if (reportType == '1') {
            // Production Report
            $('#report_form').attr('action', '{{ route("production-report-filter") }}');
            $('#date_section').show();
            // $('#price_section').show(); // Uncomment if you want to show prices
        } else if (reportType == '2') {
            // Distribution Report
            $('#report_form').attr('action', '{{ route("distribution-report-filter") }}');
            $('#date_section').show();
            $('#distribution_options').show();
        }
    }
</script>
@endpush
