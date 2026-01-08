@extends("layouts.master")

@section('page_css')
    <link rel="stylesheet" href="{{asset('assets/plugins/data-tables/css/datatables.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/bootstrap-datetimepicker/css/bootstrap-datepicker.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .text-center {
            text-align: center !important;
        }

        .align-right {
            justify-content: flex-end !important;
        }

        .dt-center {
            text-align: center !important;
        }

        .modal .datepicker {
            z-index: 9999 !important;
        }

        .distribution-summary {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }

        .distribution-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .distribution-summary-item:last-child {
            border-bottom: none;
        }

        .branch-step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 15px;
        }

        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #dee2e6;
            margin: 0 5px;
        }

        .step-dot.active {
            background: #007bff;
        }

        .step-dot.completed {
            background: #28a745;
        }
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
                                <th class="text-center">Production Margin (%)</th>
                                <th class="text-center">Action</th>
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
                            <label class="col-4" for="production_date">Production Date <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control mr-2" id="production_date" name="production_date"
                                value="{{ date('Y-m-d') }}" autocomplete="off" required>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="cows_received">Cows Received <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control mr-2" id="cows_received" name="cows_received" min="1"
                                placeholder="Enter number of cows" required>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="total_weight">Live Weight (kg) <span
                                    class="text-danger">*</span></label>
                            <input type="text" step="0.01" class="form-control mr-2" id="total_weight" name="total_weight"
                                min="0" placeholder="Enter live weight" required>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="meat_output">Meat Weight (kg) <span
                                    class="text-danger">*</span></label>
                            <input type="text" step="0.01" class="form-control mr-2" id="meat_output" name="meat_output"
                                min="0" placeholder="Enter meat weight" onkeypress="return isNumberKey(event,this)"
                                required>
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

    <!-- Edit Production Modal -->
    <div class="modal fade" id="editProductionModal" tabindex="-1" role="dialog" aria-labelledby="editProductionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductionModalLabel">Edit Production</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit_production_form">
                        @csrf()
                        <input type="hidden" id="edit_production_id" name="id">
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="edit_production_date">Production Date <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control mr-2" id="edit_production_date" name="production_date"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="edit_cows_received">Cows Received <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control mr-2" id="edit_cows_received" name="cows_received" min="1"
                                placeholder="Enter number of cows" required>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="edit_total_weight">Live Weight (kg) <span
                                    class="text-danger">*</span></label>
                            <input type="text" step="0.01" class="form-control mr-2" id="edit_total_weight" name="total_weight"
                                min="0" placeholder="Enter live weight" required>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="edit_meat_output">Meat Weight (kg) <span
                                    class="text-danger">*</span></label>
                            <input type="text" step="0.01" class="form-control mr-2" id="edit_meat_output" name="meat_output"
                                min="0" placeholder="Enter meat weight" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Modal -->
    <div class="modal fade" id="distributionModal" tabindex="-1" role="dialog" aria-labelledby="distributionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="distributionModalLabel">Distribution to Branches</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Production Date:</strong> <span id="dist_production_date"></span> |
                        <strong>Total Meat Weight:</strong> <span id="dist_total_weight"></span> kg |
                        <strong>Remaining:</strong> <span id="dist_remaining_weight">0.00</span> kg
                    </div>
                    
                    <div class="branch-step-indicator" id="stepIndicator">
                        <!-- Step dots will be dynamically added -->
                    </div>
                    
                    <div id="distributionFormContainer">
                        <input type="hidden" id="dist_production_id">
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="dist_store_id">Branch Name <span class="text-danger">*</span></label>
                            <select class="form-control" id="dist_store_id" name="store_id" required>
                                <option value="">Select Branch</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="dist_meat_type">Meat Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="dist_meat_type" name="meat_type" required>
                                <option value="">Select Meat Type</option>
                                <option value="Beef">Beef</option>
                                <option value="Offal">Offal</option>
                                <option value="Bones">Bones</option>
                                <option value="Mixed">Mixed</option>
                            </select>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="dist_weight">Weight Distributed (kg) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="dist_weight" name="weight_distributed" 
                                placeholder="Enter weight" required>
                        </div>
                    </div>

                    <div class="distribution-summary" id="distributionSummary" style="display: none;">
                        <h6><strong>Distribution Summary</strong></h6>
                        <div id="summaryContent"></div>
                        <div class="mt-2">
                            <strong>Total Distributed:</strong> <span id="totalDistributed">0.00</span> kg
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="skipBranchBtn">Skip</button>
                    <button type="button" class="btn btn-primary" id="nextBranchBtn">Next</button>
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
            
            $('#cows_received').on('change', function () {
                var min = document.getElementById('cows_received').value;

                if (min !== '') {
                    document.getElementById('cows_received').value =
                        numberWithCommas(parseFloat(min.replace(/\,/g, ''), 10));
                } else {
                    document.getElementById('cows_received').value = '';
                }
            });
            $('#total_weight').on('change', function () {
                var min = document.getElementById('total_weight').value;

                if (min !== '') {
                    document.getElementById('total_weight').value =
                        numberWithCommas(parseFloat(min.replace(/\,/g, ''), 10));
                } else {
                    document.getElementById('total_weight').value = '';
                }
            });
            $('#meat_output').on('change', function () {
                var min = document.getElementById('meat_output').value;

                if (min !== '') {
                    document.getElementById('meat_output').value =
                        numberWithCommas(parseFloat(min.replace(/\,/g, ''), 10));
                } else {
                    document.getElementById('meat_output').value = '';
                }
            });

            $('#production_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: true,
                maxDate: moment(),
                locale: {
                    format: 'YYYY-MM-DD'
                },
            });

            function isNumberKey(evt, obj) {
                var charCode = evt.which ? evt.which : event.keyCode;
                var value = obj.value;
                var dotcontains = value.indexOf(".") !== -1;
                if (dotcontains) if (charCode === 46) return false;
                if (charCode === 46) return true;
                return !(charCode > 31 && (charCode < 48 || charCode > 57));
            }

            function numberWithCommas(digit) {
                return String(parseFloat(digit))
                    .toString()
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
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
                    { "data": "production_date" },
                    { "data": "cows_received", "className": "dt-center" },
                    { "data": "total_weight", "className": "dt-center" },
                    { "data": "meat_output", "className": "dt-center" },
                    { "data": "yield", "className": "dt-center" },
                    {
                        "data": null,
                        "className": "dt-center",
                        "orderable": false,
                        "render": function (data, type, row) {
                            let distBtn = `<button class='btn btn-sm btn-success btn-rounded dist-btn' data-id='${row.id}'>Distributions</button>`;
                            let editBtn = `<button class='btn btn-sm btn-primary btn-rounded edit-btn' data-id='${row.id}'>Edit</button>`;
                            let delBtn = `<button class='btn btn-sm btn-danger btn-rounded delete-btn' data-id='${row.id}'>Delete</button>`;
                            return distBtn + ' ' + editBtn + ' ' + delBtn;
                        }
                    }
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

                console.log('Total Weight:', totalWeight);
                console.log('Meat Output:', meatOutput);
                return;

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

            // Initialize edit date picker
            $('#edit_production_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: true,
                maxDate: moment(),
                locale: {
                    format: 'YYYY-MM-DD'
                },
            });

            // Handle Edit button click
            $(document).on('click', '.edit-btn', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ url('production') }}/" + id,
                    type: "GET",
                    success: function (response) {
                        if (response.success) {
                            var data = response.data;
                            $('#edit_production_id').val(data.id);
                            $('#edit_production_date').val(data.production_date);
                            $('#edit_cows_received').val(data.cows_received);
                            $('#edit_total_weight').val(data.total_weight);
                            $('#edit_meat_output').val(data.meat_output);
                            $('#editProductionModal').modal('show');
                        } else {
                            notify('Error loading production data', 'top', 'right', 'danger');
                        }
                    },
                    error: function () {
                        notify('Error loading production data', 'top', 'right', 'danger');
                    }
                });
            });

            // Handle Edit form submission
            $('#edit_production_form').on('submit', function (e) {
                e.preventDefault();
                var id = $('#edit_production_id').val();
                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

                $.ajax({
                    url: "{{ url('production') }}/" + id,
                    type: "PUT",
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            notify(response.message || 'Production updated successfully', 'top', 'right', 'success');
                            $('#editProductionModal').modal('hide');
                            table.ajax.reload();
                        } else {
                            notify('Error updating production', 'top', 'right', 'danger');
                        }
                    },
                    error: function (xhr) {
                        var errors = xhr.responseJSON?.errors;
                        if (errors) {
                            var errorMsg = Object.values(errors).join('<br>');
                            notify(errorMsg, 'top', 'right', 'danger');
                        } else {
                            notify('Error updating production', 'top', 'right', 'danger');
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).html('Update');
                    }
                });
            });

            // Distribution Modal Logic
            var stores = @json($stores);
            var currentStoreIndex = 0;
            var distributions = [];
            var currentProductionId = null;
            var totalMeatWeight = 0;

            function resetDistributionModal() {
                currentStoreIndex = 0;
                distributions = [];
                $('#dist_store_id').val('');
                $('#dist_meat_type').val('');
                $('#dist_weight').val('');
                $('#distributionSummary').hide();
                $('#summaryContent').html('');
                updateStepIndicator();
                updateRemainingWeight();
            }

            function updateStepIndicator() {
                var html = '';
                for (var i = 0; i < stores.length; i++) {
                    var dotClass = 'step-dot';
                    if (i < currentStoreIndex) {
                        dotClass += ' completed';
                    } else if (i === currentStoreIndex) {
                        dotClass += ' active';
                    }
                    html += '<div class="' + dotClass + '" title="' + stores[i].name + '"></div>';
                }
                $('#stepIndicator').html(html);
            }

            function updateRemainingWeight() {
                var distributed = distributions.reduce(function(sum, d) {
                    return sum + parseFloat(d.weight_distributed || 0);
                }, 0);
                var remaining = totalMeatWeight - distributed;
                $('#dist_remaining_weight').text(remaining.toFixed(2));
                $('#totalDistributed').text(distributed.toFixed(2));
                return remaining;
            }

            function updateSummary() {
                if (distributions.length === 0) {
                    $('#distributionSummary').hide();
                    return;
                }
                var html = '';
                distributions.forEach(function(d) {
                    var storeName = stores.find(s => s.id == d.store_id)?.name || 'Unknown';
                    html += '<div class="distribution-summary-item">';
                    html += '<span>' + storeName + ' (' + d.meat_type + ')</span>';
                    html += '<span>' + parseFloat(d.weight_distributed).toFixed(2) + ' kg</span>';
                    html += '</div>';
                });
                $('#summaryContent').html(html);
                $('#distributionSummary').show();
                updateRemainingWeight();
            }

            function moveToNextBranch() {
                currentStoreIndex++;
                if (currentStoreIndex >= stores.length) {
                    // All branches done, change button to Save
                    $('#nextBranchBtn').text('Save').removeClass('btn-primary').addClass('btn-success');
                } else {
                    // Pre-select next store
                    $('#dist_store_id').val(stores[currentStoreIndex].id);
                }
                $('#dist_meat_type').val('');
                $('#dist_weight').val('');
                updateStepIndicator();
            }

            // Handle Distribution button click
            $(document).on('click', '.dist-btn', function () {
                var id = $(this).data('id');
                currentProductionId = id;
                resetDistributionModal();

                $.ajax({
                    url: "{{ url('production') }}/" + id + "/distributions",
                    type: "GET",
                    success: function (response) {
                        if (response.success) {
                            var production = response.production;
                            totalMeatWeight = parseFloat(production.meat_output);
                            $('#dist_production_id').val(production.id);
                            $('#dist_production_date').text(production.production_date);
                            $('#dist_total_weight').text(parseFloat(production.meat_output).toFixed(2));

                            // Load existing distributions if any
                            if (response.data && response.data.length > 0) {
                                distributions = response.data.map(function(d) {
                                    return {
                                        store_id: d.store_id,
                                        meat_type: d.meat_type,
                                        weight_distributed: d.weight_distributed
                                    };
                                });
                                updateSummary();
                            }

                            // Pre-select first store
                            if (stores.length > 0) {
                                $('#dist_store_id').val(stores[0].id);
                            }
                            updateStepIndicator();
                            updateRemainingWeight();
                            $('#nextBranchBtn').text('Next').removeClass('btn-success').addClass('btn-primary');
                            $('#distributionModal').modal('show');
                        } else {
                            notify('Error loading distribution data', 'top', 'right', 'danger');
                        }
                    },
                    error: function () {
                        notify('Error loading distribution data', 'top', 'right', 'danger');
                    }
                });
            });

            // Handle Next/Save button
            $('#nextBranchBtn').on('click', function () {
                var storeId = $('#dist_store_id').val();
                var meatType = $('#dist_meat_type').val();
                var weight = $('#dist_weight').val();

                // If we're on the last branch and clicking Save
                if ($(this).text() === 'Save') {
                    // Add current entry if filled
                    if (storeId && meatType && weight) {
                        distributions.push({
                            store_id: storeId,
                            meat_type: meatType,
                            weight_distributed: weight
                        });
                    }

                    if (distributions.length === 0) {
                        notify('Please add at least one distribution', 'top', 'right', 'warning');
                        return;
                    }

                    // Save all distributions
                    var btn = $(this);
                    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

                    $.ajax({
                        url: "{{ url('production') }}/" + currentProductionId + "/distributions",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            distributions: distributions
                        },
                        success: function (response) {
                            if (response.success) {
                                notify(response.message || 'Distributions saved successfully', 'top', 'right', 'success');
                                $('#distributionModal').modal('hide');
                                table.ajax.reload();
                            } else {
                                notify('Error saving distributions', 'top', 'right', 'danger');
                            }
                        },
                        error: function (xhr) {
                            var errors = xhr.responseJSON?.errors;
                            if (errors) {
                                var errorMsg = Object.values(errors).flat().join('<br>');
                                notify(errorMsg, 'top', 'right', 'danger');
                            } else {
                                notify('Error saving distributions', 'top', 'right', 'danger');
                            }
                        },
                        complete: function () {
                            btn.prop('disabled', false).html('Save');
                        }
                    });
                    return;
                }

                // Validate current entry
                if (!storeId || !meatType || !weight) {
                    notify('Please fill all fields before proceeding', 'top', 'right', 'warning');
                    return;
                }

                // Check for duplicate store
                var existingIndex = distributions.findIndex(d => d.store_id == storeId);
                if (existingIndex >= 0) {
                    // Update existing
                    distributions[existingIndex] = {
                        store_id: storeId,
                        meat_type: meatType,
                        weight_distributed: weight
                    };
                } else {
                    // Add new
                    distributions.push({
                        store_id: storeId,
                        meat_type: meatType,
                        weight_distributed: weight
                    });
                }

                updateSummary();
                moveToNextBranch();
            });

            // Handle Skip button
            $('#skipBranchBtn').on('click', function () {
                moveToNextBranch();
            });
        });
    </script>
@endpush