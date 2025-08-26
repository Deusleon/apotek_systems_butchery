@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Stock Transfer
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Transfer </a></li>
@endsection


@section("content")
    <style>
        .datepicker>.datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        .ms-container {
            background: transparent url('../assets/plugins/multi-select/img/switch.png') no-repeat 50% 50%;
            width: 100%;
        }

        .ms-selectable,
        .ms-selection {
            background: #fff;
            color: #555555;
            float: left;
            width: 45%;
        }

        #loading {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            display: none;
            opacity: 0.7;
            background-color: #fff;
            z-index: 99;
            text-align: center;
        }

        #loading-image {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 100;
        }
    </style>
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="invoice-received" href="{{ route('stock-transfer.index') }}"
                    role="tab" aria-controls="quotes_list" aria-selected="false">New Transfer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="order-received" href="{{ route('stock-transfer-history') }}"
                    role="tab" aria-controls="new_quotes" aria-selected="true">Transfer History
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            {{-- Stock Transfer History Start --}}
            <div class="tab-pane fade show active" id="transfer-history" role="tabpanel"
                aria-labelledby="transfer-history-tab">
                <div class="mb-3 bg-light">
                    <div class="row ml-1 text-right justify-content-end">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="from_id justify-content-start">From:</label>
                                <select name="from_id" id="from_id" class="form-control">
                                    <option value="">Select Branch...</option>
                                    @foreach($stores as $store)
                                        <option value="{{$store->name}}">{{$store->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="to_id">To:</label>
                                <select name="to_id" id="to_id" class="form-control">
                                    <option value="">Select Branch..</option>
                                    @foreach($stores as $store)
                                        <option value="{{$store->name}}">{{$store->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row ml-1 mr-1" id="detail">
                    <div style="display: block;" class="table-responsive">
                        <table id="fixed-header1" class="display table nowrap table-striped table-hover"
                            style="width:100%; ">
                            <thead>
                                <tr>
                                    <th>Transfer #</th>
                                    <th>Date</th>
                                    <th>Products</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <th hidden>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $transfer)
                                                            <tr class="p-0">
                                                                <td>{{$transfer->transfer_no}}</td>
                                                                <td>{{ $transfer->created_at->format('Y-m-d') }}</td>
                                                                <td>{{$transfer->total_products}}</td>
                                                                <td align="left">
                                                                    <div style="margin-right: 50%">
                                                                        {{$transfer->fromStore->name}}
                                                                    </div>
                                                                </td>
                                                                <td align="left">
                                                                    <div style="margin-right: 50%">
                                                                        {{$transfer->toStore->name}}
                                                                    </div>
                                                                </td>

                                                                <td class="justify-content-center">
                                                                    @php
                                                                        $statuses = [
                                                                            'created' => ['name' => 'Pending', 'class' => 'badge-secondary'],
                                                                            'assigned' => ['name' => 'Assigned', 'class' => 'badge-info'],
                                                                            'approved' => ['name' => 'Approved', 'class' => 'badge-warning'],
                                                                            'in_transit' => ['name' => 'In Transit', 'class' => 'badge-primary'],
                                                                            'acknowledged' => ['name' => 'Acknowledged', 'class' => 'badge-success'],
                                                                            'completed' => ['name' => 'Completed', 'class' => 'badge-dark'],
                                                                            'cancelled' => ['name' => 'Cancelled', 'class' => 'badge-danger']
                                                                        ];
                                                                        $currentStatus = $transfer->status ?? 1;
                                                                        $statusInfo = $statuses[$currentStatus] ?? ['name' => 'Unknown', 'class' => 'badge-secondary'];
                                                                    @endphp

                                                                    <button class='badge {{ $statusInfo["class"] }} btn btn-sm btn-rounded mt-2 p-2'
                                                                        style="width: 100px;">{{ $statusInfo["name"] }}</button>

                                                                    <!-- Status Workflow Buttons -->
                                                                    <div class="mt-1">
                                                                        @if($currentStatus == 1)
                                                                            @can('assign_transfers')
                                                                                <button type="button" class="btn btn-info btn-xs"
                                                                                    onclick="updateStatus({{ $transfer->id }}, 2, 'assign')">
                                                                                    <i class="fas fa-user-check"></i> Assign
                                                                                </button>
                                                                            @endcan

                                                                            @can('approve_transfers')
                                                                                <button type="button" class="btn btn-warning btn-xs"
                                                                                    onclick="updateStatus({{ $transfer->id }}, 3, 'approve')">
                                                                                    <i class="fas fa-check-circle"></i> Approve
                                                                                </button>
                                                                            @endcan
                                                                        @endif

                                                                        @if($currentStatus == 2)
                                                                            @can('approve_transfers')
                                                                                <button type="button" class="btn btn-warning btn-xs"
                                                                                    onclick="updateStatus({{ $transfer->id }}, 3, 'approve')">
                                                                                    <i class="fas fa-check-circle"></i> Approve
                                                                                </button>
                                                                            @endcan

                                                                            @can('manage_transfers')
                                                                                <button type="button" class="btn btn-primary btn-xs"
                                                                                    onclick="updateStatus({{ $transfer->id }}, 4, 'in-transit')">
                                                                                    <i class="fas fa-truck"></i> In Transit
                                                                                </button>
                                                                            @endcan
                                                                        @endif

                                                                        @if($currentStatus == 3)
                                                                            @can('manage_transfers')
                                                                                <button type="button" class="btn btn-primary btn-xs"
                                                                                    onclick="updateStatus({{ $transfer->id }}, 4, 'in-transit')">
                                                                                    <i class="fas fa-truck"></i> In Transit
                                                                                </button>
                                                                            @endcan
                                                                        @endif

                                                                        @if($currentStatus == 4)
                                                                            @can('acknowledge_transfers')
                                                                                <button type="button" class="btn btn-success btn-xs"
                                                                                    onclick="updateStatus({{ $transfer->id }}, 5, 'acknowledge')">
                                                                                    <i class="fas fa-handshake"></i> Acknowledge
                                                                                </button>
                                                                            @endcan
                                                                        @endif

                                                                        @if($currentStatus == 5)
                                                                            @can('complete_transfers')
                                                                                <button type="button" class="btn btn-dark btn-xs"
                                                                                    onclick="updateStatus({{ $transfer->id }}, 6, 'complete')">
                                                                                    <i class="fas fa-flag-checkered"></i> Complete
                                                                                </button>
                                                                            @endcan
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td class="">
                                                                    <!-- Show Button -->
                                                                    <button type="button"
                                                                        class="mt-2 btn btn-sm btn-rounded btn-success btn-show-transfer"
                                                                        data-toggle="modal" data-target="#showStockTransferModal"
                                                                        data-transfer-no="{{ $transfer->transfer_no }}"
                                                                        data-from-store="{{ $transfer->fromStore->name }}"
                                                                        data-to-store="{{ $transfer->toStore->name }}"
                                                                        data-status="{{ $statusInfo['name'] }}" data-remarks="{{ $transfer->remarks }}"
                                                                        data-approved-by="{{ $transfer->approved_by ? $transfer->approved_by_name : '' }}"
                                                                        data-cancelled-by="{{ $transfer->cancelled_by ? $transfer->cancelled_by_name : '' }}"
                                                                        data-items='{{ json_encode($transfer->all_items->map(function ($item) {
                                        return [
                                            'id' => $item->id,
                                            'product_name' =>
                                                optional(optional($item->currentStock)->product)->name . ' ' .
                                                optional(optional($item->currentStock)->product)->brand . ' ' .
                                                optional(optional($item->currentStock)->product)->pack_size .
                                                optional(optional($item->currentStock)->product)->sales_uom,
                                            'quantity' => $item->transfer_qty,
                                            'accepted_qty' => $item->accepted_qty ?? 0,
                                        ];
                                    })) }}' title="Show Details">
                                                                        Show
                                                                    </button>
                                                                    @if (userCan('stock_transfer.edit'))
                                                                        <!-- Edit Button -->
                                                                        @if ($transfer->status === 'created')
                                                                            <a href="{{ route('stock-transfer.edit', $transfer->transfer_no) }}"
                                                                                class="mt-2 btn btn-sm btn-rounded btn-primary">
                                                                                Edit
                                                                            </a>
                                                                        @endif

                                                                    @endif
                                                                </td>
                                                                <td hidden>{{ $transfer->created_at }}</td>
                                                            </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('stock_management.stock_transfer._show_modal')
    @include('stock_management.stock_transfer._edit_modal')
    @include('stock_management.stock_transfer.confirm_modal')
    @include('stock_management.stock_transfer.confirm_reject_modal')

@endsection


@push("page_scripts")

    {{-- For Stock Tranfer --}}
    @include('partials.notification')
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    <script>
        var config = {
            routes: {
                rejectTransfer: '{{route('reject-transfer')}}',
                approveStockTransfer: '{{route('approve-transfer')}}'
            }
        };

        // Handle Show button click
        $('.btn-show-transfer').on('click', function () {
            const transferNo = $(this).data('transfer-no');
            const fromStore = $(this).data('from-store');
            const toStore = $(this).data('to-store');
            const status = $(this).data('status');
            const remarks = $(this).data('remarks');
            const approvedBy = $(this).data('approved-by') || 'N/A';
            const cancelledBy = $(this).data('cancelled-by') || 'N/A';
            const items = $(this).data('items');
            $('#approve')
                .data('transfer-no', transferNo)
                .data('from-store', fromStore)
                .data('to-store', toStore)
                .data('status', status)
                .data('remarks', remarks);

            $('#reject')
                .data('transfer-no', transferNo)
                .data('from-store', fromStore)
                .data('to-store', toStore)
                .data('status', status)
                .data('remarks', remarks);

            const approveBtn = $('#approve');
            const rejectBtn = $('#reject');
            const closeBtn = $('#close');
            approveBtn.hide();
            rejectBtn.hide();
            closeBtn.hide();
            const canApprove = @json(userCan('stock_transfer.approve'));
            if (status === 'Pending' && canApprove) {
                approveBtn.show().data({
                    'transfer-no': transferNo,
                    'from-store': fromStore,
                    'to-store': toStore,
                    'status': status
                });
                rejectBtn.show();
            } else {
                closeBtn.show();
            }

            $('#show_transfer_no').text(transferNo);
            $('#show_from_store').text(fromStore);
            $('#show_to_store').text(toStore);
            if (status === 'Approved') {
                $('#show_approved_by_label').text('Approved By:');
                $('#show_approved_by').text(approvedBy);
            } else if (status === 'Cancelled') {
                $('#show_approved_by_label').text('Cancelled By:');
                $('#show_approved_by').text(cancelledBy);
            }else{
                $('#show_approved_by_label').text('Approved By:');
                $('#show_approved_by').text('N/A');
            }
            $('#show_status').text(status);
            $('#show_remarks').text(remarks || 'N/A');

            const itemsTableBody = $('#show_items_table_body');
            itemsTableBody.empty();
            if (items && items.length > 0) {
                items.forEach(item => {
                    itemsTableBody.append(`
                                                                <tr>
                                                                    <td>${item.product_name}</td>
                                                                    <td>${Number(item.quantity ?? 0).toFixed(0)}</td>
                                                                </tr>
                                                            `);
                });
            }
        });

        $(document).on('click', '.btn-approve-transfer', function () {
            const $btn = $(this);
            const transferNo = $btn.data('transfer-no');
            const action = $btn.data('action');
            const status = $btn.data('status');
            const fromStore = $btn.data('from-store');
            const toStore = $btn.data('to-store');

            $('#confirm_transfer_no').val(transferNo);
            $('#confirm_action').val(action);
            $('#confirm_status').val(status || '');
            $('#confirm_from_store').val(fromStore || '');
            $('#confirm_to_store').val(toStore || '');
            $('#confirm-modal-title').text('Approve Stock Transfer');
            $('#confirm-message').text('Are you sure you want to approve this transfer from ' + fromStore + ' to ' + toStore + '?');

            $('#showStockTransferModal').off('hidden.bs.modal').one('hidden.bs.modal', function () {
                $('#confirmModal').modal('show');
            });

            $('#showStockTransferModal').modal('hide');
        });

        $(document).on('click', '.btn-reject-transfer', function () {
            const $btn = $(this);
            const transferNo = $btn.data('transfer-no');
            const action = $btn.data('action');
            const fromStore = $btn.data('from-store');
            const toStore = $btn.data('to-store');

            $('#reject_transfer_no').val(transferNo);
            $('#reject-modal-title').text('Reject Stock Transfer');
            $('#reject-message').text('Are you sure you want to reject this transfer from ' + fromStore + ' to ' + toStore + '? Please enter rejection reason below:');

            $('#rejection_reason').val('');

            $('#showStockTransferModal').off('hidden.bs.modal').one('hidden.bs.modal', function () {
                $('#confirmRejectModal').modal('show');
            });

            $('#showStockTransferModal').modal('hide');
        });

        $('.btn-edit-transfer').on('click', function () {
            const updateUrl = $(this).data('update-url');
            const transferNo = $(this).data('transfer-no');
            const fromStore = $(this).data('from-store');
            const toStore = $(this).data('to-store');
            const remarks = $(this).data('remarks');
            const items = $(this).data('items');

            $('#form_stock_transfer_edit').attr('action', updateUrl);
            $('#edit_transfer_no').text(transferNo);
            $('#edit_from_store').text(fromStore);
            $('#edit_to_store').text(toStore);
            $('#remarks_edit').val(remarks);

            const itemsContainer = $('#edit_items_container');
            itemsContainer.empty();
            if (items && items.length > 0) {
                const table = $('<table class="table table-bordered table-sm"><thead><tr><th>Product</th><th>Quantity</th></tr></thead><tbody></tbody></table>');
                const tbody = table.find('tbody');
                items.forEach((item, index) => {
                    tbody.append(`
                                                                <tr>
                                                                    <td>
                                                                        ${item.product_name}
                                                                        <input type="hidden" name="transfers[${index}][id]" value="${item.id}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" name="transfers[${index}][transfer_qty]" class="form-control" value="${item.quantity}" required>
                                                                    </td>
                                                                </tr>
                                                            `);
                });
                itemsContainer.append(table);
            }
        });

    </script>


    <script type="text/javascript">

        //Applying datatabale on the table
        const stockTransfer = $('#fixed-header1').DataTable({
            responsive: true,
            order: [[7, 'desc']],
            columnDefs: [
                {
                    targets: [3, 4], // Zero-based index of the hidden column
                    visible: true, // Hide the column from view
                    searchable: true, // Allow it to be searchable
                },
            ]
        });

        //On Select
        function storeSelectValidator() {


            try {
                var from = document.getElementById("from_id");
                var from_id = from.options[from.selectedIndex].value;
                var to = document.getElementById("to_id");
                var to_id = to.options[to.selectedIndex].value

            } catch (e) {

            }

            if (from_id === to_id) {
                notify('From and To should not be the same', 'top', 'right', 'info');
                return false;
            }

        }

        //Filtering the Branch from
        $('#from_id').on('change', function (e) {
            // e.preventDefault();

            storeSelectValidator();

            const selectedValue = $(this).val(); // Get the selected dropdown value
            console.log("DataSelected", selectedValue);


            if (selectedValue === 'Select store...') {
                stockTransfer.column(3).search('').draw();
            }

            // Check if nothing is selected and reset the filter
            if (selectedValue && selectedValue !== 'Select store...') {
                stockTransfer.column(3).search(selectedValue).draw(); // Filter by the hidden column
            } else {
                stockTransfer.column(3).search('').draw(); // Reset the filter
            }
        });

        //Filtering the Branch to
        $('#to_id').on('change', function (e) {
            // e.preventDefault();

            storeSelectValidator();

            const selectedValue = $(this).val(); // Get the selected dropdown value
            console.log("DataSelected", selectedValue);

            if (selectedValue === 'Select store..') {
                stockTransfer.column(4).search('').draw();
            }

            // Check if nothing is selected and reset the filter
            if (selectedValue && selectedValue !== 'Select store..') {
                stockTransfer.column(4).search(selectedValue).draw(); // Filter by the hidden column
            } else {
                stockTransfer.column(4).search('').draw(); // Reset the filter
            }
        });

    </script>

    <script>
        $(document).ready(function () {
            // Listen for the click event on the Transfer History tab
            $('#transfer-history-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#acknowledge_all').on('show.bs.modal', function (event) {

                var modal = $(this);
                modal.show();
                var _token = $('input[name="_token"]').val();

            });//end edit
        });
    </script>

@endpush