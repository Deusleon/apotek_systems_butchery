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

                <!-- Price Section for Summary Calculation -->
                <div class="price-section">
                    <h6><i class="feather icon-tag"></i> Prices per kg (for summary calculation - optional)</h6>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="meat_price">Meat Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="meat_price" id="meat_price" placeholder="0.00" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="steak_price">Steak Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="steak_price" id="steak_price" placeholder="0.00" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="beef_fillet_price">Beef Fillet Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="beef_fillet_price" id="beef_fillet_price" placeholder="0.00" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="beef_liver_price">Beef Liver Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="beef_liver_price" id="beef_liver_price" placeholder="0.00" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="tripe_price">Tripe Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="tripe_price" id="tripe_price" placeholder="0.00" />
                            </div>
                        </div>
                    </div>
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
