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

        .small-table table td,
        .small-table table th {
            padding: 0.35rem 0.5rem;
            font-size: 0.875rem;
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
                                <label for="from_id" class="justify-content-start">From:</label>
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
                                                                            'created' => ['name' => 'Pending', 'class' => 'background-color: #bfdbfe; color:#3B82F6;'],
                                                                            'assigned' => ['name' => 'Assigned', 'class' => 'background-color: #FDBA74; color:#FF2A00;'],
                                                                            'approved' => ['name' => 'Approved', 'class' => 'background-color: #FCE300; color:#FFEB3B;'],
                                                                            'in_transit' => ['name' => 'In Transit', 'class' => 'background-color: #67E8F9; color: #00BCD4;'],
                                                                            'acknowledged' => ['name' => 'Acknowledged', 'class' => 'background-color:#BBF7D0; color:#48bb78;'],
                                                                            'completed' => ['name' => 'Completed', 'class' => 'background-color: #d8b4fe; color:#8B5CF6;'],
                                                                            'cancelled' => ['name' => 'Cancelled', 'class' => 'background-color:#FECACA; color:#f56565;']
                                                                        ];
                                                                        $currentStatus = $transfer->status ?? 1;
                                                                        $statusInfo = $statuses[$currentStatus] ?? ['name' => 'Unknown', 'class' => 'badge-secondary'];
                                                                    @endphp

                                                                    <button class='badge btn btn-sm btn-rounded mt-2 p-2'
                                                                        style="width: 120px; {{ $statusInfo["class"] }}">{{ $statusInfo["name"] }}</button>
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
                                                                        data-acknowledged-by="{{ $transfer->acknowledged_by ? $transfer->acknowledged_by_name : '' }}"
                                                                        data-remarks="{{ $transfer->remarks }}"
                                                                        data-notes="{{ $transfer->notes }}"
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
                                                                    @if (userCan('stock_transfer.acknowledge'))
                                                                        <!-- Acknowledge Button -->
                                                                        @if ($transfer->status === 'approved')
                                                                            {{-- <a
                                                                                href="{{ route('stock-transfer-acknowledge.index', $transfer->transfer_no) }}"
                                                                                class="mt-2 btn btn-sm btn-rounded btn-primary">
                                                                                Acknowledge
                                                                            </a> --}}
                                                                            <button type="button" class="btn btn-primary btn-sm btn-rounded btn-acknowledge"
                                                                                data-toggle="modal" data-target="#acknowledgeModal"
                                                                                data-transfer-no="{{ $transfer->transfer_no }}"
                                                                                data-from-store="{{ $transfer->fromStore->id }}"
                                                                                data-to-store="{{ $transfer->toStore->id }}"
                                                                                data-acknowledged-by="{{ $transfer->acknowledged_by ? $transfer->acknowledged_by_name : '' }}">
                                                                                Acknowledge
                                                                            </button>
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
    @include('stock_management.stock_transfer._acknowledge_modal')

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
                approveStockTransfer: '{{route('approve-transfer')}}',
                fetchTransferToAcknowledge: "{{ url('inventory/stock-transfer-acknowledge') }}/:transfer_no/acknowledge"
            }
        };

        // Handle Show button click
        $('.btn-show-transfer').on('click', function () {
            const transferNo = $(this).data('transfer-no');
            const fromStore = $(this).data('from-store');
            const toStore = $(this).data('to-store');
            const status = $(this).data('status');
            const remarks = $(this).data('remarks');
            const notes = $(this).data('notes');
            const approvedBy = $(this).data('approved-by') || 'N/A';
            const cancelledBy = $(this).data('cancelled-by') || 'N/A';
            const acknowledgedBy = $(this).data('acknowledged-by') || 'N/A';
            // console.log('remarks', remarks);
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
            $('#show_acknowledged_by').text(acknowledgedBy || 'N/A');
            $('#show_remarks_textarea').text(remarks || 'N/A');
            if (status === 'Pending') {
                $('#show_remarks_label').text('Remarks:');
                $('#show_remarks_textarea').text(remarks || 'N/A');
                $('#acknowledge_remark_div').attr('hidden', true);
                $('#show_approved_by_label').text('Approved By:');
                $('#show_approved_by').text(approvedBy);
            }else if (status === 'Approved') {
                $('#show_remarks_label').text('Remarks:');
                $('#show_remarks_textarea').text(remarks || 'N/A');
                $('#acknowledge_remark_div').attr('hidden', true);
                $('#show_approved_by_label').text('Approved By:');
                $('#show_approved_by').text(approvedBy);
            } else if (status === 'Cancelled') {
                $('#show_remarks_label').text('Remarks:');
                $('#show_remarks_textarea').text(remarks || 'N/A');
                $('#show_approved_by_label').text('Cancelled By:');
                $('#show_approved_by').text(cancelledBy);
            } else {
                $('#acknowledge_remark_div').attr('hidden', true);
                $('#show_remarks_label').text('Transfer Remarks:');
                $('#show_acknowledge_remark_textarea').text(notes || 'N/A');
                $('#show_approved_by_label').text('Approved By:');
                $('#show_approved_by').text(approvedBy || 'N/A');
                $('#acknowledge_remark_div').removeAttr('hidden');
            }
            $('#show_status').text(status);
            $('#show_remarks').text(remarks || 'N/A');

            const itemsTableBody = $('#show_items_table_body');
            itemsTableBody.empty();
            if (items && items.length > 0) {
                if (status === 'Completed' || status === 'Acknowledged') {
                    $('#display_qty').attr('hidden', true);
                    $('#hidden_transferred').removeAttr('hidden');
                    $('#hidden_received').removeAttr('hidden');
                } else {
                    $('#display_qty').removeAttr('hidden');
                    $('#hidden_transferred').attr('hidden', true);
                    $('#hidden_received').attr('hidden', true);
                }
                items.forEach(item => {
                    let row = `
                <tr>
                    <td>${item.product_name}</td>
                    <td>${Number(item.quantity ?? 0).toFixed(0)}</td>
            `;

                    if (status === 'Completed' || status === 'Acknowledged') {
                        row += `
                    <td>${Number(item.accepted_qty ?? 0).toFixed(0)}</td>
                `;
                    }

                    row += `</tr>`;
                    itemsTableBody.append(row);
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

        $(document).on('click', '.btn-acknowledge', function () {
            const transferNo = $(this).data('transfer-no');
            $('#acknowledge_items_body').empty();
            $('#acknowledge_transfer_no_input').val(transferNo);
            $('#acknowledge_from_store_input').val($(this).data('from-store'));
            $('#acknowledge_to_store_input').val($(this).data('to-store'));

            $('#acknowledge_items_body').find('.receive')
                .attr('contenteditable', false)
                .removeClass('form-control p-2 mt-2 form-control-sm');
            $('#edit-acknowledge-btn').text('Edit').data('editing', false);

            const url = config.routes.fetchTransferToAcknowledge.replace(':transfer_no', transferNo);

            // AJAX call
            $.ajax({
                url: url,
                type: "GET",
                dataType: "json",
                success: function (response) {
                    $('#acknowledge_items_body').empty();

                    if (response.length > 0) {
                        response.forEach((item, index) => {
                            const productLabel =
                                (item.current_stock && item.current_stock.product)
                                    ? (item.current_stock.product.name + ' ' +
                                        item.current_stock.product.brand + ' ' +
                                        item.current_stock.product.pack_size +
                                        item.current_stock.product.sales_uom)
                                    : 'Unknown Product';

                            const transferQty = Number(item.transfer_qty || 0).toFixed(0);
                            const acceptedQty = Number(item.accepted_qty || 0).toFixed(0);
                            const receiveDefault = (Number(transferQty) - Number(acceptedQty)).toFixed(0);

                            $('#acknowledge_transfer_no').text(transferNo);
                            $('#acknowledge_from_store').text(item.from_store.name);
                            $('#acknowledge_to_store').text(item.to_store.name);

                            $('#acknowledge_items_body').append(`
                                                <tr data-item-id="${item.id}">
                                                    <td>
                                                        ${productLabel}
                                                        <!-- Hidden inputs for id and original transfer_qty so they are submitted -->
                                                        <input type="hidden" name="transfers[${index}][id]" value="${item.id}">
                                                        <input type="hidden" name="transfers[${index}][transfer_qty]" value="${transferQty}">
                                                    </td>
                                                    <td class="transferred">${transferQty}</td>
                                                    <td class="received">${acceptedQty}</td>
                                                    <td class="receive text-center" data-original="${transferQty}" contenteditable="false">${receiveDefault}</td>
                                                </tr>
                                            `);
                        });
                    } else {
                        notify(
                            response.message || "Failed to fetch transferred Items",
                            "top",
                            "right",
                            "danger"
                        );
                    }
                },
                error: function (xhr, status, error) {
                    var message = "Failed to fetch transferred Items!";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    notify(message, "top", "right", "danger");
                },
                complete: function () {
                    $("#loading").hide();
                },
                timeout: 20000,
            });
        });

        $('#acknowledgeModal .edit-acknowledge-btn').on('click', function () {
            const $btn = $(this);
            const isEditing = $btn.data('editing') || false;
            if (!isEditing) {
                $('#acknowledge_items_body').find('.receive')
                    .attr('contenteditable', true)
                    .addClass('form-control p-1 form-control-sm');
                $btn.text('Ignore').data('editing', true);
            } else {
                $('#acknowledge_items_body').find('.receive')
                    .attr('contenteditable', false)
                    .removeClass('form-control p-1 form-control-sm');
                $btn.text('Edit').data('editing', false);
            }
        });

        $(document).on('keydown', '#acknowledge_items_body .receive', function (e) {
            const allowedKeys = [
                8, 9, 13, 27, 46,
                35, 36, 37, 38, 39, 40
            ];
            const isNumberKey = (e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105);
            if (isNumberKey || allowedKeys.indexOf(e.keyCode) !== -1) return;
            if (e.ctrlKey || e.metaKey) return;
            e.preventDefault();
        });

        function getCaretPosition(element) {
            let sel = window.getSelection();
            if (!sel.rangeCount) return 0;
            let range = sel.getRangeAt(0);
            let preCaretRange = range.cloneRange();
            preCaretRange.selectNodeContents(element);
            preCaretRange.setEnd(range.endContainer, range.endOffset);
            return preCaretRange.toString().length;
        }

        function setCaretPosition(element, pos) {
            let range = document.createRange();
            let sel = window.getSelection();
            if (!element.firstChild) element.appendChild(document.createTextNode(''));
            const textLen = element.firstChild ? element.firstChild.length : 0;
            const safePos = Math.max(0, Math.min(pos, textLen));
            range.setStart(element.firstChild, safePos);
            range.collapse(true);
            sel.removeAllRanges();
            sel.addRange(range);
        }

        $(document).on('input', '#acknowledge_items_body .receive', function () {
            const el = this;
            const $cell = $(this);

            let caretPos = getCaretPosition(el);
            const original = $cell.text();
            let cleaned = original.replace(/\D+/g, '');

            cleaned = cleaned.replace(/^0+(?=\d)/, '');

            if (cleaned !== original) {
                const removed = original.length - cleaned.length;
                $cell.text(cleaned);
                const newCaret = Math.max(0, caretPos - removed);
                setCaretPosition(el, newCaret);
                caretPos = newCaret;
            }

            const transferred = parseInt($cell.siblings('.transferred').text(), 10) || 0;
            const parsed = parseInt($cell.text().trim(), 10);
            if (!isNaN(parsed) && parsed > transferred) {
                $cell.text(transferred);
                setCaretPosition(el, String(transferred).length);
            }

        });

        $(document).on('blur', '#acknowledge_items_body .receive', function () {
            const $cell = $(this);
            const transferred = parseInt($cell.siblings('.transferred').text(), 10) || 0;
            let text = $cell.text().trim();

            text = text.replace(/\D+/g, '').replace(/^0+(?=\d)/, '');
            let parsed = parseInt(text, 10);
            if (isNaN(parsed)) parsed = 0;
            if (parsed < 0) parsed = 0;
            if (parsed > transferred) parsed = transferred;

            $cell.text(parsed);
        });

        $('#transfer').on('submit', function (e) {
            e.preventDefault();

            $('#transfer').find('input[name$="[accepted_qty]"]').remove();

            $('#acknowledge_items_body tr').each(function (i, tr) {
                const $tr = $(tr);
                const transferred = parseInt($tr.find('.transferred').text().replace(/\D/g, ''), 10) || 0;
                let acceptedText = $tr.find('.receive').text().trim();
                acceptedText = (acceptedText === '') ? '0' : acceptedText.replace(/\D/g, '');
                let accepted = parseInt(acceptedText, 10);
                if (isNaN(accepted)) accepted = 0;
                if (accepted < 0) accepted = 0;
                if (accepted > transferred) accepted = transferred;

                const input = $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', `transfers[${i}][accepted_qty]`)
                    .val(accepted);
                $('#transfer').append(input);
            });

            $('#acknowledgeModal').off('hidden.bs.modal').one('hidden.bs.modal', function () {
                $('#confirmAcknowledgeModal').modal('show');
            });

            $('#acknowledgeModal').modal('hide');
        });

        $('#confirmAcknowledgeBtn').on('click', function () {
            $('#transfer')[0].submit();
        });

    </script>


    <script type="text/javascript">

        //Applying datatabale on the table
        const stockTransfer = $('#fixed-header1').DataTable({
            responsive: true,
            order: [[7, 'desc']],
            columnDefs: [
                {
                    targets: [3, 4],
                    visible: true,
                    searchable: true,
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