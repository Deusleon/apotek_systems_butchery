@extends('layouts.master')

@section('content-title')
    Stock Adjustments
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Adjustments </a></li>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
@endsection

@section('content')
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="invoice-received" href="{{ route('stock-adjustments.create') }}"
                    role="tab" aria-controls="quotes_list" aria-selected="false">New Adjustment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="order-received" href="{{ route('stock-adjustments.index') }}"
                    role="tab" aria-controls="new_quotes" aria-selected="true">Adjustment History
                </a>
            </li>
        </ul>
        <div class="card">
            {{-- <div class="card-header">
                <div class="float-right">
                    <a href="{{ route('stock-adjustments.create') }}" class="btn btn-secondary">
                        New Adjustment
                    </a>
                </div>
                <h5>Stock Adjustment History</h5>
            </div> --}}
            <div class="card-body">
                <!-- Date Filter Form -->
                <form method="GET" action="{{ route('stock-adjustments.index') }}" id="date-filter-form">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control float-right" id="date_filter" name="date_range"
                                    placeholder="Filter by date" value="{{ request('date_range') }}">
                                <input type="hidden" name="start_date" id="start_date" value="{{ request('start_date') }}">
                                <input type="hidden" name="end_date" id="end_date" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control" name="search" placeholder="Search product or reason"
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="adjustment_type" class="form-control">
                                <option value="">All Types</option>
                                <option value="increase" {{ request('adjustment_type') == 'increase' ? 'selected' : '' }}>
                                    Increase</option>
                                <option value="decrease" {{ request('adjustment_type') == 'decrease' ? 'selected' : '' }}>
                                    Decrease</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="adjustments-table">
                        <thead>
                            <tr>
                                <!-- <th>Reference</th> -->
                                <th>Product name</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Date</th>
                                <th>Branch</th>
                                <th>Reason</th>
                                <th>Adjusted By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($adjustments as $adjustment)
                                <tr>
                                    <!-- <td>{{ $adjustment->created_at->format('Y-m-d H:i') }}</td> -->
                                    <!-- <td>{{ $adjustment->reference_number ?? 'N/A' }}</td> -->
                                    <td>{{ optional($adjustment->currentStock)->product->name ?? 'Unknown Product' }}</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $adjustment->adjustment_type === 'increase' ? 'success' : 'danger' }}">
                                            {{ ucfirst($adjustment->adjustment_type) }}
                                        </span>
                                    </td>
                                    <td>{{ abs($adjustment->adjustment_quantity) }}</td>
                                    <td>{{ $adjustment->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ optional($adjustment->store)->name ?? 'Unknown Store' }}</td>

                                    <td>{{ $adjustment->reason }}</td>
                                    <td>{{ optional($adjustment->user)->name ?? 'Unknown User' }}</td>
                                    <td>
                                        <a href="{{ route('stock-adjustments.show', $adjustment) }}"
                                            class="btn btn-success btn-rounded btn-sm" data-toggle="tooltip"
                                            title="View Details">
                                            Show
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Show filter info if any filters are active -->
                @if(request('start_date') || request('search') || request('adjustment_type'))
                    <div class="alert alert-info">
                        <i class="feather icon-info"></i>
                        <strong>Active Filters:</strong>
                        @if(request('start_date') && request('end_date'))
                            Date: <strong>{{ request('start_date') }}</strong> to <strong>{{ request('end_date') }}</strong>
                        @endif
                        @if(request('search'))
                            @if(request('start_date') && request('end_date')) | @endif
                            Search: <strong>"{{ request('search') }}"</strong>
                        @endif
                        @if(request('adjustment_type'))
                            @if(request('start_date') || request('search')) | @endif
                            Type: <strong>{{ ucfirst(request('adjustment_type')) }}</strong>
                        @endif
                        <a href="{{ route('stock-adjustments.index') }}" class="float-right">Clear all filters</a>
                    </div>
                @endif

                <div class="d-flex justify-content-center mt-4">
                    {{ $adjustments->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable with basic functionality
            var table = $('#adjustments-table').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
            });

            // Initialize DateRangePicker
            $('#date_filter').daterangepicker({
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    format: 'YYYY-MM-DD'
                },
                autoUpdateInput: false,
                startDate: '{{ request("start_date") ?: "" }}',
                endDate: '{{ request("end_date") ?: "" }}'
            });

            // Set initial value if dates are provided
            @if(request('start_date') && request('end_date'))
                $('#date_filter').val('{{ request("start_date") }} - {{ request("end_date") }}');
            @endif

            $('#date_filter').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
                $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
            });

            $('#date_filter').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
                $('#start_date').val('');
                $('#end_date').val('');
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush