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
                <a class="nav-link active text-uppercase" id="stock-transfer-tablist" data-toggle="pill"
                   href="#stock-transfer" role="tab"
                   aria-controls="stock_transfer" aria-selected="true">Stock Transfer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="transfer-history-tablist" data-toggle="pill"
                   href="{{ url('inventory/stock-transfer-reprint') }}" role="tab"
                   aria-controls="transfer_history" aria-selected="false">Transfer History
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
         {{-- Stock Transfer Start --}}
        <div class="tab-pane fade show active" id="stock-transfer" role="tabpanel" aria-labelledby="stock_transfer-tab">
                <a href="{{ route('stock-transfer.index') }}"></a>

            @if(auth()->user()->checkPermission('Manage Stock Transfer'))
                <div class="d-flex justify-content-lg-end align-items-center">
                    <a href="{{ route('stock-transfer.index') }}">
                        <button type="button" class="btn btn-secondary btn-sm" style="margin-bottom: 2%;">
                            New Transfer
                        </button>
                    </a>
                </div>
            @endif
                    <form id="transfer" method="post" enctype="multipart/form-data">
                        @csrf()
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">

                                </div>
                            </div>
                        </div>
                        <!-- ajax loading gif -->
                        <div id="loading">
                            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" hidden>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">From</label>
                                    <select id="from_id" name="from_id"
                                            class="js-example-basic-single form-control drop">
                                        <option selected="true" >Select store...</option>
                                        @foreach($stores as $store)
                                            <option value="{{$store->name}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">To</label>
                                    <select id="to_id" name="to_id" class="js-example-basic-single form-control drop"
                                            >
                                        <option selected="true" >Select store..</option>
                                        @foreach($stores as $store)
                                            <option value="{{$store->name}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row" id="detail">
                            <div style="display: block;" class="table-responsive">
                                <table id="stockTransferTable" class="display table nowrap table-striped table-hover"
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
                                    @foreach($transfers as $all_transfer)
                                        <tr>
                                            <td>{{$all_transfer->transfer_no}}</td>
                                            <td>{{date('d-m-Y', strtotime($all_transfer->created_at))}}</td>
                                            <td>{{$all_transfer->total_products}}</td>
                                            <td align="left">
                                                <div style="margin-right: 50%">
                                                    {{$all_transfer->fromStore->name}}
                                                </div>
                                            </td>
                                            <td align="left">
                                                <div style="margin-right: 50%">
                                                    {{$all_transfer->toStore->name}}
                                                </div>
                                            </td>

                                            <td>
                                                @php
                                                    $statuses = [
                                                        1 => ['name' => 'Created', 'class' => 'badge-secondary'],
                                                        2 => ['name' => 'Assigned', 'class' => 'badge-info'],
                                                        3 => ['name' => 'Approved', 'class' => 'badge-warning'],
                                                        4 => ['name' => 'In Transit', 'class' => 'badge-primary'],
                                                        5 => ['name' => 'Acknowledged', 'class' => 'badge-success'],
                                                        6 => ['name' => 'Completed', 'class' => 'badge-dark']
                                                    ];
                                                    $currentStatus = $all_transfer->status ?? 1;
                                                    $statusInfo = $statuses[$currentStatus] ?? ['name' => 'Unknown', 'class' => 'badge-secondary'];
                                                @endphp
                                                
                                                <span class='badge {{ $statusInfo["class"] }}'>{{ $statusInfo["name"] }}</span>
                                                
                                                <!-- Status Workflow Buttons -->
                                                <div class="mt-1">
                                                    @if($currentStatus == 1)
                                                        @can('assign_transfers')
                                                        <button type="button" class="btn btn-info btn-xs btn-rounded" onclick="updateStatus({{ $all_transfer->id }}, 2, 'assign')">Assign</button>
                                                        @endcan
                                                        @can('approve_transfers')
                                                        <button type="button" class="btn btn-warning btn-xs btn-rounded" onclick="updateStatus({{ $all_transfer->id }}, 3, 'approve')">Approve</button>
                                                        @endcan
                                                    @endif

                                                    @if($currentStatus == 2)
                                                        @can('approve_transfers')
                                                        <button type="button" class="btn btn-warning btn-xs btn-rounded" onclick="updateStatus({{ $all_transfer->id }}, 3, 'approve')">Approve</button>
                                                        @endcan
                                                        @can('manage_transfers')
                                                        <button type="button" class="btn btn-primary btn-xs btn-rounded" onclick="updateStatus({{ $all_transfer->id }}, 4, 'in-transit')">In Transit</button>
                                                        @endcan
                                                    @endif

                                                    @if($currentStatus == 3)
                                                        @can('manage_transfers')
                                                        <button type="button" class="btn btn-primary btn-xs btn-rounded" onclick="updateStatus({{ $all_transfer->id }}, 4, 'in-transit')">In Transit</button>
                                                        @endcan
                                                    @endif

                                                    @if($currentStatus == 4)
                                                        @can('acknowledge_transfers')
                                                        <button type="button" class="btn btn-success btn-xs btn-rounded" onclick="updateStatus({{ $all_transfer->id }}, 5, 'acknowledge')">Acknowledge</button>
                                                        @endcan
                                                    @endif

                                                    @if($currentStatus == 5)
                                                        @can('complete_transfers')
                                                        <button type="button" class="btn btn-dark btn-xs btn-rounded" onclick="updateStatus({{ $all_transfer->id }}, 6, 'complete')">Complete</button>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <button type='button' class='btn btn-sm btn-rounded btn-success show-btn'
                                                        data-transfer-no='{{ $all_transfer->transfer_no ?? '' }}'
                                                        data-date='{{ date('d-m-Y', strtotime($all_transfer->created_at)) }}'
                                                        data-products='{{ $all_transfer->total_products ?? '' }}'
                                                        data-from='{{ $all_transfer->fromStore->name ?? '' }}'
                                                        data-to='{{ $all_transfer->toStore->name ?? '' }}'
                                                        data-remarks='{{ $all_transfer->remark ?? '' }}'>Show</button>

                                                <button type='button' class='btn btn-sm btn-rounded btn-primary edit-btn'
                                                        data-id='{{ $all_transfer->id }}'
                                                        data-transfer-no='{{ $all_transfer->transfer_no }}'
                                                        data-from-store='{{ $all_transfer->from_store }}'
                                                        data-to-store='{{ $all_transfer->to_store }}'
                                                        data-remark='{{ $all_transfer->remark }}'
                                                        data-evidence='{{ $all_transfer->evidence }}'>Edit</button>
                                            </td>
                                            <td hidden>{{ $all_transfer->created_at }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
                            </div>

                        </div>

                    </form>
        </div>

        </div>
    </div>

    @include('stock_management.stock_transfer.edit')
    @include('stock_management.stock_transfer.show')

    @push("page_scripts")

    {{--    For Stock Transfer --}}
    @include('partials.notification')
    <script src="{{ asset('assets/apotek/js/stock-transfer.js') }}"></script>

    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

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

        // Edit button click handler
        $('#stockTransferTable').on('click', '.edit-btn', function() {
            var modal = $('#edit');
            var id = $(this).data('id');
            var transferNo = $(this).data('transfer-no');
            var form = modal.find('#editForm');

            // Update form action
            var action = form.attr('action');
            form.attr('action', action.substring(0, action.lastIndexOf('/')) + '/' + id);

            // Clear cart and prepare for loading
            cart_edit.clearCart();
            $('#select_id_edit').val(null).trigger('change');
            $('#loading_edit').show();

            // Load transfer details from server
            $.ajax({
                url: '{{ route('inventory.stock-transfer.show', ['transfer' => '__transfer_no__']) }}'.replace('__transfer_no__', transferNo),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Set store values
                    modal.find('#from_id_edit').val(data.from_store);
                    modal.find('#to_id_edit').val(data.to_store);
                    
                    // Set remark
                    modal.find('#remarks_edit').val(data.remark);
                    
                    // Set evidence if exists
                    if (data.evidence) {
                        var evidenceUrl = '{{ asset('storage') }}/' + data.evidence;
                        modal.find('#current_evidence').html('<a href="' + evidenceUrl + '" target="_blank">View Current Evidence</a>');
                    } else {
                        modal.find('#current_evidence').html('');
                    }

                    // Load products into cart
                    if (data.stock_transfer_items) {
                        data.stock_transfer_items.forEach(function(item) {
                            var productData = [item.product_name, item.transfer_qty, item.product_id, 0]; // QOH is placeholder
                            cart_edit.add(productData, item.transfer_qty);
                        });
                    }

                    // Filter products and sync stores
                    filterProductsForEditModal(data.from_store);
                    syncStores();
                },
                error: function() {
                    alert('Failed to load transfer details.');
                },
                complete: function() {
                    $('#loading_edit').hide();
                }
            });

            modal.modal('show');
        });

</script>


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