@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Adjustment History
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Adjustment History </a></li>
@endsection

@section("content")

    <div class="col-sm-12">
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
                            <input style="width: 103.4%;" type="text" name="adjustment-date"
                                   onchange="getAdjustmentByDate()"
                                   class="form-control" id="adjustment-date" value=""/>
                        </div>
                    </div>
                    <div id="tbody" class="table-responsive">
                        <table id="fixed-header1" class="display table nowrap table-striped table-hover"
                               style="width:100%">

                            <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('stock_management.stock_adjustment.show')

@endsection



@push("page_scripts")

    @include('partials.notification')

    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#adjustment-date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#adjustment-date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                maxDate: end,
                autoUpdateInput: true,
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

        // $(document).ready(function () {
        //     loadAdjustments();
        // });

        function loadAdjustments() {

            var dates = document.querySelector('input[name=adjustment-date]').value;
            dates = dates.split('-');

            $("#fixed-header1").dataTable().fnDestroy();
            var table_main = $('#fixed-header1').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('all-adjustments') }}",
                    "dataType": "json",
                    "type": "post",
                    "cache": false,
                    "data": {
                        _token: "{{csrf_token()}}",
                        from_date: dates[0],
                        to_date: dates[1]
                    }
                },
                "columns": [
                    {"data": "name"},
                    {"data": "type"},
                    {
                        "data": "quantity_adjusted", render: function (quantity_adjusted) {
                            return numberWithCommas(quantity_adjusted);
                        }
                    },
                    {"data": "date"},
                    {"data": "reason"},
                    {
                        "data": "action",
                        defaultContent: "<button type='button' id='shows' class='btn btn-sm btn-rounded btn-success'>Show</button>"
                    }
                ], aaSorting: [[3, "desc"]]

            });
        }

        $('#tbody').on('click', '#shows', function () {
            var row_data = $('#fixed-header1').DataTable().row($(this).parents('tr')).data();
            $('#show').find('.modal-body #name_edit').val(row_data.name);
            $('#show').find('.modal-body #quantity_edit').val(numberWithCommas(row_data.quantity_adjusted));
            $('#show').find('.modal-body #reason_edit').val(row_data.reason);
            $('#show').find('.modal-body #type').val(row_data.type);
            $('#show').find('.modal-body #description_edit').val(row_data.description);
            $('#show').modal('show');

        });

        function getAdjustmentByDate() {

            loadAdjustments();

        }

        function numberWithCommas(digit) {
            return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

    </script>

@endpush
