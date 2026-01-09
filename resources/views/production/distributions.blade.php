@extends("layouts.master")

@section('page_css')
    <link rel="stylesheet" href="{{asset('assets/plugins/data-tables/css/datatables.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .text-center {
            text-align: center !important;
        }
        .dt-center {
            text-align: center !important;
        }
        .summary-card {
            background: white;
            color: white;
            border-radius: 3px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .summary-card h4 {
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
        }
        .summary-card h2 {
            margin: 5px 0 0 0;
            font-size: 28px;
        }
    </style>
@endsection

@section('content-title')
    Production Distributions Report
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{route('production.index')}}">Production</a></li>
    <li class="breadcrumb-item"><a href="#">Distributions Report</a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Distribution Records</h5>
                    <div class="form-inline">
                        <label for="filter_store" class="mr-2">Branch:</label>
                        <select id="filter_store" class="form-control mr-3" style="min-width: 150px;">
                            <option value="">All Branches</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                        <label for="filter_meat_type" class="mr-2">Meat Type:</label>
                        <select id="filter_meat_type" class="form-control mr-3" style="min-width: 120px;">
                            <option value="">All Types</option>
                            <option value="Beef">Beef</option>
                            <option value="Offal">Offal</option>
                            <option value="Bones">Bones</option>
                            <option value="Mixed">Mixed</option>
                        </select>
                        <label for="date_range" class="mr-2">Date:</label>
                        <input type="text" id="date_range" class="form-control" autocomplete="off" style="min-width:200px;">
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="distributions_table" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Branch</th>
                                <th>Meat Type</th>
                                <th class="text-center">Weight (kg)</th>
                                <th class="text-center">Production ID</th>
                                <th class="text-center">Actions</th>
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
@endsection

@push("page_scripts")
    <script src="{{asset('assets/plugins/data-tables/js/datatables.min.js')}}"></script>
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
            $('#date_range').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));

            // Initialize DataTable
            var table = $('#distributions_table').DataTable({
                "processing": true,
                "serverSide": true,
                "searching": true,
                "ajax": {
                    "url": "{{ route('distributions.data') }}",
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
                        d.store_id = $('#filter_store').val();
                        d.meat_type = $('#filter_meat_type').val();
                    },
                    "error": function (xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        notify('Error loading distribution data', 'top', 'right', 'danger');
                    }
                },
                "columns": [
                    { "data": "production_date" },
                    { "data": "store_name" },
                    { "data": "meat_type" },
                    { "data": "weight_distributed", "className": "dt-center" },
                    { "data": "production_id", "className": "dt-center" },
                    {
                        "data": null,
                        "className": "dt-center",
                        "orderable": false,
                        "render": function (data, type, row) {
                            return `<button class='btn btn-sm btn-danger btn-rounded delete-dist-btn' data-id='${row.id}'>
                                <i class='feather icon-trash'></i>
                            </button>`;
                        }
                    }
                ],
                "order": [[0, 'desc']],
                "language": {
                    "emptyTable": "No distribution records found",
                    "processing": '<img id="loading-image" style="width: 50px; height: 50px; opacity: 0.5;" src="{{asset('assets/images/spinner.gif')}}" />'
                },
                "drawCallback": function(settings) {
                    updateSummary(settings.json);
                }
            });

            function updateSummary(json) {
                if (json && json.summary) {
                    $('#totalDistributions').text(json.summary.total_distributions || 0);
                    $('#totalWeight').text(parseFloat(json.summary.total_weight || 0).toFixed(2));
                    $('#branchesServed').text(json.summary.branches_served || 0);
                    $('#totalProductions').text(json.summary.total_productions || 0);
                }
            }

            // Filter events
            $('#date_range').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                table.ajax.reload();
            });
            $('#date_range').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                table.ajax.reload();
            });
            $('#filter_store, #filter_meat_type').on('change', function() {
                table.ajax.reload();
            });

            // Handle delete button click
            $(document).on('click', '.delete-dist-btn', function () {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this distribution record?')) {
                    $.ajax({
                        url: "{{ url('distributions') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{csrf_token()}}"
                        },
                        success: function (response) {
                            if (response.success) {
                                notify(response.message || 'Distribution deleted successfully', 'top', 'right', 'success');
                                table.ajax.reload();
                            } else {
                                notify('Error deleting distribution', 'top', 'right', 'danger');
                            }
                        },
                        error: function () {
                            notify('Error deleting distribution', 'top', 'right', 'danger');
                        }
                    });
                }
            });
        });
    </script>
@endpush
