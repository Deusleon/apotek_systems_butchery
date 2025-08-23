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
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        .ms-container {
            background: transparent url('../assets/plugins/multi-select/img/switch.png') no-repeat 50% 50%;
            width: 100%;
        }

        .ms-selectable, .ms-selection {
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
                <a class="nav-link text-uppercase" id="invoice-received"
                    href="{{ route('stock-transfer.index') }}" role="tab" aria-controls="quotes_list"
                    aria-selected="false">New Transfer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="order-received"
                    href="{{ route('stock-transfer-history') }}" role="tab" aria-controls="new_quotes"
                    aria-selected="true">Transfer History
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            {{-- New Stock Transfer Start --}}
            <div class="tab-pane fade" id="stock-transfer" role="tabpanel" aria-labelledby="stock_transfer-tab">
                <p>New stock transfer form goes here. This is currently pointing to another page. You might want to create a dedicated view or include a form here.</p>
                <a href="{{ route('stock-transfer.index') }}" class="btn btn-primary">Create New Transfer</a>
            </div>

            {{-- Stock Transfer History Start --}}
            <div class="tab-pane fade show active" id="transfer-history" role="tabpanel" aria-labelledby="transfer-history-tab">
                <div class="mb-3 bg-light">
                    <div class="row ml-1 text-right justify-content-end">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="from_id justify-content-start">From:</label>
                                <select name="from_id" id="from_id" class="form-control">
                                    <option value="">Select branch...</option>
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
                                    <option value="">Select branch..</option>
                                    @foreach($stores as $store)
                                        <option value="{{$store->name}}">{{$store->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="detail">
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
                                <tr>
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

                                    <td>
                                        @php
                                            $statuses = [
                                                1 => ['name' => 'created', 'class' => 'badge-secondary'],
                                                2 => ['name' => 'Assigned', 'class' => 'badge-info'],
                                                3 => ['name' => 'Approved', 'class' => 'badge-warning'],
                                                4 => ['name' => 'In Transit', 'class' => 'badge-primary'],
                                                5 => ['name' => 'Acknowledged', 'class' => 'badge-success'],
                                                6 => ['name' => 'Completed', 'class' => 'badge-dark']
                                            ];
                                            $currentStatus = $transfer->status ?? 1;
                                            $statusInfo = $statuses[$currentStatus] ?? ['name' => 'Unknown', 'class' => 'badge-secondary'];
                                        @endphp
                                        
                                        <span class='badge {{ $statusInfo["class"] }}'>{{ $statusInfo["name"] }}</span>
                                        
                                        <!-- Status Workflow Buttons -->
                                        <div class="mt-1">
                                            @if($currentStatus == 1)
                                                @can('assign_transfers')
                                                <button type="button" class="btn btn-info btn-xs" onclick="updateStatus({{ $transfer->id }}, 2, 'assign')">
                                                    <i class="fas fa-user-check"></i> Assign
                                                </button>
                                                @endcan
                                                
                                                @can('approve_transfers')
                                                <button type="button" class="btn btn-warning btn-xs" onclick="updateStatus({{ $transfer->id }}, 3, 'approve')">
                                                    <i class="fas fa-check-circle"></i> Approve
                                                </button>
                                                @endcan
                                            @endif

                                            @if($currentStatus == 2)
                                                @can('approve_transfers')
                                                <button type="button" class="btn btn-warning btn-xs" onclick="updateStatus({{ $transfer->id }}, 3, 'approve')">
                                                    <i class="fas fa-check-circle"></i> Approve
                                                </button>
                                                @endcan
                                                
                                                @can('manage_transfers')
                                                <button type="button" class="btn btn-primary btn-xs" onclick="updateStatus({{ $transfer->id }}, 4, 'in-transit')">
                                                    <i class="fas fa-truck"></i> In Transit
                                                </button>
                                                @endcan
                                            @endif

                                            @if($currentStatus == 3)
                                                @can('manage_transfers')
                                                <button type="button" class="btn btn-primary btn-xs" onclick="updateStatus({{ $transfer->id }}, 4, 'in-transit')">
                                                    <i class="fas fa-truck"></i> In Transit
                                                </button>
                                                @endcan
                                            @endif

                                            @if($currentStatus == 4)
                                                @can('acknowledge_transfers')
                                                <button type="button" class="btn btn-success btn-xs" onclick="updateStatus({{ $transfer->id }}, 5, 'acknowledge')">
                                                    <i class="fas fa-handshake"></i> Acknowledge
                                                </button>
                                                @endcan
                                            @endif

                                            @if($currentStatus == 5)
                                                @can('complete_transfers')
                                                <button type="button" class="btn btn-dark btn-xs" onclick="updateStatus({{ $transfer->id }}, 6, 'complete')">
                                                    <i class="fas fa-flag-checkered"></i> Complete
                                                </button>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <!-- Show Button -->
                                        <button type="button" class="btn btn-sm btn-rounded btn-success btn-show-transfer"
                                                data-toggle="modal"
                                                data-target="#showStockTransferModal"
                                                data-transfer-no="{{ $transfer->transfer_no }}"
                                                data-from-store="{{ $transfer->fromStore->name }}"
                                                data-to-store="{{ $transfer->toStore->name }}"
                                                data-status="{{ $statusInfo['name'] }}"
                                                data-remarks="{{ $transfer->remarks }}"
                                                data-items='{{ json_encode($transfer->all_items->map(function($item) {
                                     return [
                                         'id' => $item->id,
                                         'product_name' => optional(optional($item->currentStock)->product)->name,
                                         'quantity' => $item->transfer_qty,
                                         'accepted_qty' => $item->accepted_qty ?? 0,
                                     ];
                                })) }}'               title="Show Details">
                                           Show
                                        </button>

                                        <!-- Edit Button -->
                                        <button type="button" class="btn btn-sm btn-rounded btn-primary btn-edit-transfer"
                                                data-toggle="modal"
                                                data-target="#editStockTransferModal"
                                                data-update-url="{{ route('stock-transfer.update', $transfer->id) }}"
                                                data-transfer-no="{{ $transfer->transfer_no }}"
                                                data-from-store="{{ $transfer->fromStore->name }}"
                                                data-to-store="{{ $transfer->toStore->name }}"
                                                data-remarks="{{ $transfer->remarks }}"
                                                data-items='{{ json_encode($transfer->all_items->map(function($item) {
                                             return [
                                                 'id' => $item->id,
                                                 'product_name' => optional(optional($item->currentStock)->product)->name,
                                                 'quantity' => $item->transfer_qty,
                                             ];
                                        })) }}'                  title="Edit Transfer">
                                             Edit
                                        </button>
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

@endsection


@push("page_scripts")

{{--    For Stock Tranfer --}}
@include('partials.notification')
<script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
<script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

<script>
    $(document).ready(function() {
        // Handle Show button click
        $('.btn-show-transfer').on('click', function() {
            const transferNo = $(this).data('transfer-no');
            const fromStore = $(this).data('from-store');
            const toStore = $(this).data('to-store');
            const status = $(this).data('status');
            const remarks = $(this).data('remarks');
            const items = $(this).data('items');

            $('#show_transfer_no').text(transferNo);
            $('#show_from_store').text(fromStore);
            $('#show_to_store').text(toStore);
            $('#show_status').text(status);
            $('#show_remarks').text(remarks || 'N/A');

            const itemsTableBody = $('#show_items_table_body');
            itemsTableBody.empty();
            if (items && items.length > 0) {
                items.forEach(item => {
                    itemsTableBody.append(`
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                        </tr>
                    `);
                });
            }
        });

        // Handle Edit button click
        $('.btn-edit-transfer').on('click', function() {
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
    });
</script>


<script type="text/javascript">

    //Applying datatabale on the table
    const stockTransfer = $('#fixed-header1').DataTable({
        responsive: true,
        order: [[7, 'desc']],
        columnDefs: [
            {
                targets: [3,4] , // Zero-based index of the hidden column
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
        console.log("DataSelected",selectedValue);


        if(selectedValue === 'Select store...')
        {
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
        console.log("DataSelected",selectedValue);

        if(selectedValue === 'Select store..')
        {
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
    $(document).ready(function() {
        // Listen for the click event on the Transfer History tab
        $('#transfer-history-tablist').on('click', function(e) {
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

@section('page_js')
<script>
function updateStatus(transferId, newStatus, action) {
    if (!confirm('Are you sure you want to ' + action + ' this transfer?')) {
        return;
    }

    $.ajax({
        url: '/stock-transfer/' + transferId + '/' + action,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: newStatus
        },
        success: function(response) {
            toastr.success(response.message);
            setTimeout(function() {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.error) {
                toastr.error(xhr.responseJSON.error);
            } else {
                toastr.error('An error occurred while updating the status');
            }
        }
    });
}
</script>
@endsection
