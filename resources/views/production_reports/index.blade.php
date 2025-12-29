@extends("layouts.master")

@section('page_css')
    <style>
        .datepicker>.datepicker-days { display: block; }
        .text-center { text-align: center; }
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
                <div class="row">
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
