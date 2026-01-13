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
            @if (current_store_id() === 2)
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
                                    <th>Details</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-center">Total Weight (kg)</th>
                                    <th class="text-center">Weight Diff (kg)</th>
                                    <th class="text-center">Meat (kg)</th>
                                    <th class="text-center">Steak (kg)</th>
                                    <th class="text-center">Beef Fillet (kg)</th>
                                    <th class="text-center">Beef Liver (kg)</th>
                                    <th class="text-center">Tripe (kg)</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="card-body">
                    <div class="card-title">Production is only allowed from Main Branch</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Production Modal -->
    <div class="modal fade" id="productionModal" tabindex="-1" role="dialog" aria-labelledby="productionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="production_date" class="col-md-4 col-form-label text-md-right">Date <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="production_date" name="production_date"
                                        value="{{ date('Y-m-d') }}" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="details" class="col-md-4 col-form-label text-md-right">Details</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="details" name="details">
                                            <option value="">Select type...</option>
                                            <option value="Cows">Cows</option>
                                            <option value="Goat">Goat</option>
                                            {{-- <option value="Fish">Fish</option>
                                            <option value="Chicken">Chicken</option> --}}
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="items_received" class="col-md-4 col-form-label text-md-right">Count <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="items_received" name="items_received" min="1"
                                            placeholder="Count" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="total_weight" class="col-md-4 col-form-label text-md-right">Total Weight<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control bg-light" id="total_weight"
                                            placeholder="Total weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="weight_difference" class="col-md-4 col-form-label text-md-right">Weight Diff<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control weight-input" id="weight_difference" name="weight_difference"
                                            min="0" placeholder="weight difference" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="meat" class="col-md-4 col-form-label text-md-right">Meat<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control weight-input" id="meat" name="meat"
                                            min="0" placeholder="Meat weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="steak" class="col-md-4 col-form-label text-md-right">Steak<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control weight-input" id="steak" name="steak"
                                            min="0" placeholder="Steak weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="beef_fillet" class="col-md-4 col-form-label text-md-right">Beef Fillet<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control weight-input" id="beef_fillet" name="beef_fillet"
                                            min="0" placeholder="Beef fillet weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="beef_liver" class="col-md-4 col-form-label text-md-right">Beef Liver</label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control" id="beef_liver" name="beef_liver"
                                            min="0" placeholder="Beef liver weight">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tripe" class="col-md-4 col-form-label text-md-right">Tripe</label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control" id="tripe" name="tripe"
                                            min="0" placeholder="Tripe weight">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
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
        <div class="modal-dialog modal-lg" role="document">
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="edit_production_date" class="col-md-4 col-form-label text-md-right">Date <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="edit_production_date" name="production_date"
                                            autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_details" class="col-md-4 col-form-label text-md-right">Details</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="edit_details" name="details">
                                            <option value="">Select type...</option>
                                            <option value="Cows">Cows</option>
                                            <option value="Goat">Goat</option>
                                            {{-- <option value="Fish">Fish</option>
                                            <option value="Chicken">Chicken</option> --}}
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_items_received" class="col-md-4 col-form-label text-md-right">Count <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="edit_items_received" name="items_received" min="1"
                                            placeholder="Count" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_total_weight" class="col-md-4 col-form-label text-md-right">Total Weight<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control bg-light" id="edit_total_weight"
                                            placeholder="Total weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_weight_difference" class="col-md-4 col-form-label text-md-right">Weight Diff<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control edit-weight-input" id="edit_weight_difference" name="weight_difference"
                                            min="0" placeholder="Weight difference" required readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="edit_meat" class="col-md-4 col-form-label text-md-right">Meat<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control edit-weight-input" id="edit_meat" name="meat"
                                            min="0" placeholder="Meat weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_steak" class="col-md-4 col-form-label text-md-right">Steak<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control edit-weight-input" id="edit_steak" name="steak"
                                            min="0" placeholder="Steak weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_beef_fillet" class="col-md-4 col-form-label text-md-right">Beef Fillet<span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control edit-weight-input" id="edit_beef_fillet" name="beef_fillet"
                                            min="0" placeholder="Beef fillet weight" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_beef_liver" class="col-md-4 col-form-label text-md-right">Beef Liver</label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control" id="edit_beef_liver" name="beef_liver"
                                            min="0" placeholder="Beef liver weight">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="edit_tripe" class="col-md-4 col-form-label text-md-right">Tripe</label>
                                    <div class="col-md-8">
                                        <input type="text" step="0.01" class="form-control" id="edit_tripe" name="tripe"
                                            min="0" placeholder="Tripe weight">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
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
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="distributionModalLabel">Distribution to Branches</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Loading Overlay -->
                    <div id="distributionLoading" class="text-center py-5">
                        <img id="loading-image" src="{{asset('assets/images/spinner.gif')}}" />
                        {{-- <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading distribution data...</p> --}}
                    </div>
                    
                    <!-- Content Container (hidden while loading) -->
                    <div id="distributionContent" style="display: none;">
                    <div class="d-flex justify-content-between">
                        <div style="display: none;"><strong>Production Date:</strong> <span id="dist_production_date"></span></div>
                        {{-- <div><strong>Total Weight:</strong> <span id="dist_total_weight"></span>kg</div> --}}
                        {{-- <div><strong>Remaining:</strong> <span id="dist_remaining_weight">0.00</span>kg</div> --}}
                    </div>                    
                    <div class="branch-step-indicator" id="stepIndicator" style="display: none;">
                        <!-- Step dots will be dynamically added -->
                    </div>
                    <div class="mb-3">

                    </div>
                    
                    <div id="distributionFormContainer">
                        <input type="hidden" id="dist_production_id">
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="dist_meat_type">Meat Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="dist_meat_type" name="meat_type" required>
                                <option value="">Select Meat Type</option>
                                <option value="Meat">Meat</option>
                                <option value="Steak">Steak</option>
                                <option value="Beef Liver">Beef Liver</option>
                                <option value="Beef Fillet">Beef Fillet</option>
                            </select>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="dist_total_weight">Total Weight </label>
                            <input type="text" class="form-control" id="dist_total_weight" name="total_weight" 
                                placeholder="Total weight" disabled>
                        </div>
                        <div class="form-group d-flex align-items-center">
                            <label class="col-4" for="dist_remaining_weight">Remaining Weight </label>
                            <input type="text" class="form-control" id="dist_remaining_weight" name="remaining_weight" 
                                placeholder="Remaining weight" disabled>
                        </div>
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
                            <label class="col-4" for="dist_weight">Distributed (kg) <span class="text-danger">*</span></label>
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
                    </div><!-- End distributionContent -->
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> --}}
                    <button type="button" class="btn btn-warning" id="skipBranchBtn">Skip</button>
                    <button type="button" class="btn btn-info" id="backBranchBtn" disabled>Prev</button>
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

            // Format number with thousand separators (decimals only when applicable)
            function numberWithCommas(digit) {
                if (digit === '' || digit === null || isNaN(digit)) return '';
                var num = parseFloat(digit);
                // Check if the number has meaningful decimals
                if (num % 1 === 0) {
                    // No decimal part, format without decimals
                    return num.toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                } else {
                    // Has decimal part, show up to 2 decimal places
                    return num.toLocaleString('en-US', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    });
                }
            }

            // Remove commas and get raw number
            function unformatNumber(value) {
                if (!value) return 0;
                return parseFloat(String(value).replace(/,/g, '')) || 0;
            }

            // Format input field on blur (when user leaves the field)
            function formatInputOnBlur(selector) {
                $(document).on('blur', selector, function() {
                    var val = $(this).val();
                    if (val !== '') {
                        var num = unformatNumber(val);
                        $(this).val(numberWithCommas(num));
                    }
                });
            }

            // Apply formatting to all weight fields in Add modal
            formatInputOnBlur('#total_weight');
            formatInputOnBlur('#meat');
            formatInputOnBlur('#steak');
            formatInputOnBlur('#beef_fillet');
            formatInputOnBlur('#weight_difference');
            formatInputOnBlur('#beef_liver');
            formatInputOnBlur('#tripe');
            formatInputOnBlur('#items_received');

            // Apply formatting to all weight fields in Edit modal
            formatInputOnBlur('#edit_total_weight');
            formatInputOnBlur('#edit_meat');
            formatInputOnBlur('#edit_steak');
            formatInputOnBlur('#edit_beef_fillet');
            formatInputOnBlur('#edit_weight_difference');
            formatInputOnBlur('#edit_beef_liver');
            formatInputOnBlur('#edit_tripe');
            formatInputOnBlur('#edit_items_received');

            // Apply formatting to distribution weight field
            formatInputOnBlur('#dist_weight');
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
                    { "data": "details" },
                    { "data": "items_received", "className": "dt-center" },
                    { "data": "total_weight", "className": "dt-center" },
                    { "data": "weight_difference", "className": "dt-center" },
                    { "data": "meat", "className": "dt-center" },
                    { "data": "steak", "className": "dt-center" },
                    { "data": "beef_fillet", "className": "dt-center" },
                    { "data": "beef_liver", "className": "dt-center" },
                    { "data": "tripe", "className": "dt-center" },
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
                    "emptyTable": "No data available in table",
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
                            $('#weight_difference').val('');
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
                        submitBtn.prop('disabled', false).html('Save');
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

            // Calculate weight difference: totalWeight - (meat + steak + beefFillet)
            function calculateWeightDifference(prefix) {
                var totalWeight = unformatNumber($('#' + prefix + 'total_weight').val());
                var meat = unformatNumber($('#' + prefix + 'meat').val());
                var steak = unformatNumber($('#' + prefix + 'steak').val());
                var beefFillet = unformatNumber($('#' + prefix + 'beef_fillet').val());
                var weightDiff = totalWeight - (meat + steak + beefFillet);
                $('#' + prefix + 'weight_difference').val(numberWithCommas(weightDiff));
            }

            // Add form - calculate weight difference when total_weight, meat, steak, or beef_fillet changes
            $(document).on('input', '#total_weight, #meat, #steak, #beef_fillet', function () {
                calculateWeightDifference('');
            });

            // Edit form - calculate weight difference when total_weight, meat, steak, or beef_fillet changes
            $(document).on('input', '#edit_total_weight, #edit_meat, #edit_steak, #edit_beef_fillet', function () {
                calculateWeightDifference('edit_');
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
                            $('#edit_details').val(data.details);
                            $('#edit_items_received').val(numberWithCommas(data.items_received));
                            $('#edit_total_weight').val(numberWithCommas(data.total_weight));
                            $('#edit_meat').val(numberWithCommas(data.meat));
                            $('#edit_steak').val(numberWithCommas(data.steak));
                            $('#edit_beef_fillet').val(numberWithCommas(data.beef_fillet));
                            $('#edit_weight_difference').val(numberWithCommas(data.weight_difference));
                            $('#edit_beef_liver').val(numberWithCommas(data.beef_liver));
                            $('#edit_tripe').val(numberWithCommas(data.tripe));
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
            var productionData = null; // Store full production data
            var meatTypeLocked = false; // Flag to lock meat type after first distribution

            function resetDistributionModal() {
                currentStoreIndex = 0;
                distributions = [];
                totalMeatWeight = 0;
                productionData = null;
                meatTypeLocked = false;
                $('#dist_store_id').val('');
                $('#dist_meat_type').val('').prop('disabled', false);
                $('#dist_weight').val('');
                $('#dist_total_weight').val('0');
                $('#dist_remaining_weight').val('0');
                $('#distributionSummary').hide();
                $('#summaryContent').html('');
                $('#backBranchBtn').prop('disabled', true);
                updateStepIndicator();
            }

            // Get weight for specific meat type from production data
            function getMeatTypeWeight(meatType) {
                if (!productionData) return 0;
                switch(meatType) {
                    case 'Meat': return parseFloat(productionData.meat) || 0;
                    case 'Steak': return parseFloat(productionData.steak) || 0;
                    case 'Beef Fillet': return parseFloat(productionData.beef_fillet) || 0;
                    case 'Beef Liver': return parseFloat(productionData.beef_liver) || 0;
                    default: return 0;
                }
            }

            // Get already distributed weight for specific meat type
            function getDistributedWeightForMeatType(meatType) {
                return distributions
                    .filter(d => d.meat_type === meatType)
                    .reduce((sum, d) => sum + parseFloat(d.weight_distributed || 0), 0);
            }

            // Update weights based on selected meat type
            function updateWeightsForMeatType() {
                var selectedMeatType = $('#dist_meat_type').val();
                if (!selectedMeatType || !productionData) {
                    $('#dist_total_weight').val('0');
                    $('#dist_remaining_weight').val('0');
                    return;
                }
                
                var totalForType = getMeatTypeWeight(selectedMeatType);
                var distributedForType = getDistributedWeightForMeatType(selectedMeatType);
                var remainingForType = totalForType - distributedForType;
                
                totalMeatWeight = totalForType;
                $('#dist_total_weight').val(formatSmartDecimal(totalForType));
                $('#dist_remaining_weight').val(formatSmartDecimal(remainingForType));
            }

            // Handle meat type change
            $(document).on('change', '#dist_meat_type', function() {
                updateWeightsForMeatType();
            });

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

            // Format number for display (decimals only when applicable)
            function formatSmartDecimal(num) {
                if (num === null || num === undefined || num === '' || isNaN(num)) {
                    return '0';
                }
                num = parseFloat(num);
                if (isNaN(num)) return '0';
                if (num % 1 === 0) {
                    return num.toLocaleString('en-US');
                }
                return num.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
            }

            function updateRemainingWeight() {
                var selectedMeatType = $('#dist_meat_type').val();
                if (!selectedMeatType) {
                    $('#dist_remaining_weight').val('0');
                    $('#totalDistributed').text('0');
                    return 0;
                }
                
                var distributedForType = getDistributedWeightForMeatType(selectedMeatType);
                var totalForType = getMeatTypeWeight(selectedMeatType);
                var remaining = totalForType - distributedForType;
                
                $('#dist_remaining_weight').val(formatSmartDecimal(remaining));
                $('#totalDistributed').text(formatSmartDecimal(distributedForType));
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
                    html += '<span>' + formatSmartDecimal(parseFloat(d.weight_distributed)) + ' kg</span>';
                    html += '</div>';
                });
                $('#summaryContent').html(html);
                $('#distributionSummary').hide();
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
                // Keep meat type selected and locked - don't clear it
                // $('#dist_meat_type').val(''); // Removed - keep meat type selected
                $('#dist_weight').val('');
                // Enable back button since we moved forward
                $('#backBranchBtn').prop('disabled', false);
                updateStepIndicator();
                updateRemainingWeight(); // Update remaining weight for current meat type
            }

            function moveToPreviousBranch() {
                if (currentStoreIndex <= 0) return;
                
                currentStoreIndex--;
                
                // Reset Next button if we were on Save
                $('#nextBranchBtn').text('Next').removeClass('btn-success').addClass('btn-primary');
                
                // Pre-select the previous store
                $('#dist_store_id').val(stores[currentStoreIndex].id);
                
                // Check if there was a distribution for this store and meat type, pre-fill weight
                var currentMeatType = $('#dist_meat_type').val();
                var existingDist = distributions.find(d => d.store_id == stores[currentStoreIndex].id && d.meat_type == currentMeatType);
                if (existingDist) {
                    $('#dist_weight').val(formatSmartDecimal(existingDist.weight_distributed));
                } else {
                    $('#dist_weight').val('');
                }
                
                // Disable back button if we're at the first branch
                if (currentStoreIndex === 0) {
                    $('#backBranchBtn').prop('disabled', true);
                    // Unlock meat type if going back to first branch and no distributions yet
                    if (distributions.length === 0) {
                        meatTypeLocked = false;
                        $('#dist_meat_type').prop('disabled', false);
                    }
                }
                
                updateStepIndicator();
                updateRemainingWeight();
            }

            // Handle Distribution button click
            $(document).on('click', '.dist-btn', function () {
                var id = $(this).data('id');
                currentProductionId = id;
                resetDistributionModal();

                // Show modal immediately with loading state
                $('#distributionLoading').show();
                $('#distributionContent').hide();
                $('#distributionModal').modal('show');

                $.ajax({
                    url: "{{ url('production') }}/" + id + "/distributions",
                    type: "GET",
                    success: function (response) {
                        if (response.success) {
                            var production = response.production;
                            console.log('Distribution data:', response);
                            
                            // Store full production data for meat type weight lookups
                            productionData = production;
                            
                            $('#dist_production_id').val(production.id);
                            $('#dist_production_date').text(production.production_date);
                            
                            // Don't set total weight yet - wait for meat type selection
                            $('#dist_total_weight').val('0');
                            $('#dist_remaining_weight').val('0');

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
                            $('#nextBranchBtn').text('Next').removeClass('btn-success').addClass('btn-primary');
                            
                            // Hide loading, show content
                            $('#distributionLoading').hide();
                            $('#distributionContent').show();
                        } else {
                            $('#distributionModal').modal('hide');
                            notify('Error loading distribution data', 'top', 'right', 'danger');
                        }
                    },
                    error: function () {
                        $('#distributionModal').modal('hide');
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
                        // Validate weight doesn't exceed remaining before adding
                        var enteredWeight = unformatNumber(weight);
                        var currentRemaining = updateRemainingWeight();
                        if (enteredWeight > currentRemaining) {
                            notify('Distributed weight ' + formatSmartDecimal(enteredWeight) + 'kg  cannot exceed remaining weight ' + formatSmartDecimal(currentRemaining) + 'kg', 'top', 'right', 'warning');
                            return;
                        }
                        distributions.push({
                            store_id: storeId,
                            meat_type: meatType,
                            weight_distributed: unformatNumber(weight)
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

                // Validate weight doesn't exceed remaining
                var enteredWeight = unformatNumber(weight);
                var currentRemaining = updateRemainingWeight();
                if (enteredWeight > currentRemaining) {
                    notify('Distributed weight ' + formatSmartDecimal(enteredWeight) + 'kg cannot exceed remaining weight ' + formatSmartDecimal(currentRemaining) + 'kg', 'top', 'right', 'warning');
                    return;
                }

                // Check for duplicate store with same meat type
                var existingIndex = distributions.findIndex(d => d.store_id == storeId && d.meat_type == meatType);
                if (existingIndex >= 0) {
                    // Update existing
                    distributions[existingIndex] = {
                        store_id: storeId,
                        meat_type: meatType,
                        weight_distributed: unformatNumber(weight)
                    };
                } else {
                    // Add new
                    distributions.push({
                        store_id: storeId,
                        meat_type: meatType,
                        weight_distributed: unformatNumber(weight)
                    });
                }

                // Lock meat type after first distribution is added
                if (!meatTypeLocked) {
                    meatTypeLocked = true;
                    $('#dist_meat_type').prop('disabled', true);
                }

                updateSummary();
                moveToNextBranch();
            });

            // Handle Skip button
            $('#skipBranchBtn').on('click', function () {
                moveToNextBranch();
            });

            // Handle Back button
            $('#backBranchBtn').on('click', function () {
                moveToPreviousBranch();
            });
        });
    </script>
@endpush