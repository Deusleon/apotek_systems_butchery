@extends("layouts.master")

@section('page_css')
    <link rel="stylesheet" href="{{asset('assets/plugins/data-tables/css/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>        
        .text-center { text-align: center !important; }
        .align-right { justify-content: flex-end !important; }
        .dt-center { text-align: center !important; }
        .modal .datepicker { z-index: 9999 !important; }
    </style>
@endsection

@section('content-title')
    Production
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Production</a></li>
@endsection

@section("content")

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                @if (current_store_id() === 2)
                    <div class="d-flex justify-content-end align-items-end mb-3">
                        <div class="form-inline">
                            <button class="btn btn-secondary" data-toggle="modal" data-target="#productionModal">
                            <i class="fas fa-plus mr-1"></i> Add Production
                            </button>
                        </div>
                    </div>
                @endif
                <div class="d-flex justify-content-end align-items-end mb-3">
                    <div class="form-inline">
                        <label for="date_range" class="mr-2">Date:</label>
                        <input type="text" id="date_range" class="form-control" autocomplete="off" style="min-width:200px;">
                    </div>
                </div>
                <div id="production_table_container" class="table-responsive">
                    <table id="production_table" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-center">Cows Received</th>
                                <th class="text-center">Live Weight (kg)</th>
                                <th class="text-center">Meat Weight (kg)</th>
                                <th class="text-center">Production margin (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Production Modal -->
    <div class="modal fade" id="productionModal" tabindex="-1" role="dialog" aria-labelledby="productionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productionModalLabel">Record New
                        Production</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="production_form">
                        @csrf()
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="production_date">Production Date <span class="text-danger">*</span></label>
                            <input type="text" class="form-control mr-2" id="production_date" name="production_date"  value="{{ date('Y-m-d') }}" autocomplete="off" required>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="cows_received">Cows Received <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control mr-2" id="cows_received" name="cows_received" min="1"
                                placeholder="Enter number of cows" required>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="total_weight">Live Weight (kg) <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control mr-2" id="total_weight" name="total_weight"
                                min="0" placeholder="Enter live weight" required>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="meat_output">Meat Weight (kg) <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control mr-2" id="meat_output" name="meat_output"
                                min="0" placeholder="Enter meat weight" required>
                        </div>
                        <div class="alert alert-info" role="alert" hidden>
                            <i class="feather icon-info"></i> <strong>Note:</strong> Please ensure all weights are accurate.
                            The system will automatically calculate the yield percentage.
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push("page_scripts")
    <script src="{{asset('assets/plugins/data-tables/js/datatables.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/ac-datepicker.js')}}"></script>
    <script src="{{asset('assets/apotek/js/notification.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(document).ready(function () {
            // Set default date range to this month
            var start = moment().startOf('month');
            var end = moment().endOf('month');
            $('#date_range').daterangepicker({
                startDate: start,
                endDate: end,
                autoUpdateInput: true,
                locale: { format: 'YYYY-MM-DD', cancelLabel: 'Clear' }
            });
            $('#date_range').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));

            $('#production_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: true,
                maxDate: moment(), 
                locale: {
                    format: 'YYYY-MM-DD'
                },
            });

            // Initialize DataTable
            var table = $('#production_table').DataTable({
                "processing": true,
                "serverSide": true,
                "searching": true,
                "ajax": {
                    "url": "{{ route('production.data') }}",
                    "dataType": "json",
                    "type": "GET",
                    "data": function (d) {
                        d._token = "{{csrf_token()}}";
                        var dr = $('#date_range').val();
                        if (dr) {
                            var dates = dr.split(' - ');
                            d.start_date = dates[0];
                            d.end_date = dates[1];
                        }
                    },
                    "error": function (xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        notify('Error loading production data', 'top', 'right', 'danger');
                    }
                },
                "columns": [
                    { "data": "production_date"},
                    { "data": "cows_received", "className": "dt-center" },
                    { "data": "total_weight", "className": "dt-center" },
                    { "data": "meat_output", "className": "dt-center" },
                    { "data": "yield", "className": "dt-center" }
                ],
                "order": [[0, 'desc']],
                "language": {
                    "emptyTable": "No production records found",
                    "processing": '<img id="loading-image" style="width: 50px; height: 50px; opacity: 0.5;" src="{{asset('assets/images/spinner.gif')}}" />'
                }
            });

            // Date range filter events
            $('#date_range').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                table.ajax.reload();
            });
            $('#date_range').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });

            // Handle form submission (modal)
            $('#production_form').on('submit', function (e) {
                e.preventDefault();

                // Validate meat output is not greater than total weight
                var totalWeight = parseFloat($('#total_weight').val());
                var meatOutput = parseFloat($('#meat_output').val());

                if (meatOutput > totalWeight) {
                    notify('Meat weight cannot be greater than total live weight', 'top', 'right', 'warning');
                    return false;
                }

                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: "{{ route('production.store') }}",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            notify(response.message || 'Production recorded successfully', 'top', 'right', 'success');
                            $('#production_form')[0].reset();
                            $('#production_date').datepicker('update', moment().format('YYYY-MM-DD'));
                            $('#productionModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            notify('Error recording production', 'top', 'right', 'danger');
                        }
                    },
                    error: function (xhr) {
                        var errors = xhr.responseJSON?.errors;
                        if (errors) {
                            var errorMsg = Object.values(errors).join('<br>');
                            notify(errorMsg, 'top', 'right', 'danger');
                        } else {
                            notify('Error recording production', 'top', 'right', 'danger');
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).html('<i class="feather icon-save"></i> Record Production');
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete-btn', function () {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this production record?')) {
                    $.ajax({
                        url: "{{ url('production') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{csrf_token()}}"
                        },
                        success: function (response) {
                            if (response.success) {
                                notify(response.message || 'Production record deleted successfully', 'top', 'right', 'success');
                                table.ajax.reload();
                            } else {
                                notify('Error deleting production record', 'top', 'right', 'danger');
                            }
                        },
                        error: function () {
                            notify('Error deleting production record', 'top', 'right', 'danger');
                        }
                    });
                }
            });

            // Calculate yield percentage on input
            $('#total_weight, #meat_output').on('input', function () {
                var totalWeight = parseFloat($('#total_weight').val()) || 0;
                var meatOutput = parseFloat($('#meat_output').val()) || 0;

                if (totalWeight > 0) {
                    var yield = (meatOutput / totalWeight) * 100;
                    // Show yield in a tooltip or info text if needed
                }
            });
        });
    </script>
@endpush