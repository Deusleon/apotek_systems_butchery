@extends("layouts.master")

@section('page_css')
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
    Production Reports
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Reports / Production Reports </a></li>
@endsection

@section("content")
<div class="col-sm-12">
    <div class="card">
        <div class="card-body">
            <form id="production_report_form" action="{{route('production-report-filter')}}" method="get" target="_blank">
                @csrf()
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date_range">Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="date_range" id="date_range" readonly />
                        </div>
                    </div>
                </div>

                <!-- Price Section - Auto-populated from Selling Prices -->
                <div class="price-section" hidden>
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
                    <small class="text-muted"><i class="feather icon-info"></i> Prices are fetched automatically from the product selling prices. Update prices in Price List to change these values.</small>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
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
</script>
@endpush
