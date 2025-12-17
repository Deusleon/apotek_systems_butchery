@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Stock Count
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Count</a></li>
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
            @if(auth()->user()->checkPermission('View Stock Count'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="daily-stock-tablist"
                        href="{{ url('inventory/daily-stock-count') }}" role="tab" aria-selected="true">Daily Stock Count</a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Outgoing Stock'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="outgoing-stock-tablist" 
                        href="{{ url('inventory/out-going-stock') }}" role="tab" aria-selected="false">Outgoing Stock
                    </a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Inv. Count Sheet'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="count-sheet-tablist"
                        href="{{ url('inventory/inventory-count-sheet/Inventory Count Sheet') }}" role="tab"
                        aria-controls="stock_list" aria-selected="false" target="_blank">Inventory Count Sheet
                    </a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Taking'))
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" id="count-sheet-tablist" href="{{ route('stock-taking') }}"
                        role="tab" aria-selected="false">Stock Taking
                    </a>
                </li>
            @endif
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="new_sale" role="tabpanel" aria-labelledby="new_sale-tab">
                    <form id="stock_taking" action="" method="post">
                        @csrf()
                        <div id="loading">
                            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                        </div>

                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <div>
                                <button type="button" class="btn btn-primary" id="snapshot-stock-btn">
                                    Save Current Stock
                                </button>
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="mr-2 mb-0" for="">Date:</label>
                                <input type="text" name="sale_date" id="d_auto_8" class="form-control w-auto">
                            </div>
                        </div>

                        <div id="tbody2" class="table-responsive">
                            <table id="fixed-header2" class="display table nowrap table-striped table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th class="text-center">QOH</th>
                                        <th class="text-center">Physical</th>
                                        <th class="text-center">Difference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr data-product-id="{{ $product->id }}">
                                            <td>{{ $product->name }} {{ $product->brand }}
                                                {{ $product->pack_size }}{{ $product->sales_uom }}
                                            </td>
                                            <td class="qoh text-center">{{ number_format(round($product->current_stock)) }}</td>
                                            <td class="text-center">
                                                <input type="text" class="form-control form-control-sm physical text-right"
                                                    name="physical[{{ $product->id }}]" value="" inputmode="numeric"
                                                    pattern="\d*"
                                                    style="max-width:120px; margin-left:auto; margin-right: auto;">
                                            </td>
                                            <td class="difference text-center">0</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr hidden>
                        <div class="row" hidden>
                            <div class="col-md-10">

                            </div>
                            <div class="col-md-2">
                                <div style="width: 99%">
                                    <label><b>Total Amount</b></label>
                                    <input type="text" id="total" name="sub_total_amount" class="form-control" readonly
                                        value="0" onchange="filterByDate()" />
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" id="order_cart" name="cart">
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group" style="float: right;">
                                    <button class="btn btn-success" type="button" id="process-adjustments-btn">
                                        Preview
                                    </button>
                                </div>
                            </div>
                        </div>


                    </form>

                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Inventory Count Sheet</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Do you want to show Quantity on Hand (QoH) on the printout?
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> --}}
                        <button type="button" id="confirmNo" class="btn btn-secondary">No </button>
                        <button type="button" id="confirmYes" class="btn btn-primary">Yes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Adjustments Modal -->
        <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLabel">Preview Stock Adjustments</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body table-responsive">
                        <table class="table table-striped" id="preview-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th style="text-align: center">QOH</th>
                                    <th style="text-align: center">Physical</th>
                                    <th style="text-align: center">Difference</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <p class="text-muted mt-2"><span>Only products with differences will be adjusted.</span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="confirmProcessBtn">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>


@endsection

    @push("page_scripts")

        @include('partials.notification')
        <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
        <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
        <script type="text/javascript">
            var config = {
                routes: {
                    filterDailyStockCount: '{{ route('daily-stock-count-fetch') }}'
                }
            };

        </script>
        <script>
            $(document).ready(function () {

                function formatNumberNoDecimals(n) {
                    if (n === null || n === undefined || n === '') return '';
                    let num = Number(String(n).replace(/,/g, ''));
                    if (!isFinite(num)) return '';
                    let rounded = Math.round(num);
                    return rounded.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }

                // sanitize raw typed value: return integer or null when empty/invalid
                function sanitizeRawValue(str) {
                    if (str === null || str === undefined) return null;
                    let trimmed = String(str).trim();
                    if (trimmed === '') return null;
                    // remove non-digits
                    let cleaned = trimmed.replace(/[^0-9]/g, '');
                    if (cleaned === '') return null;
                    let n = parseInt(cleaned, 10);
                    if (!isFinite(n)) return null;
                    return Math.round(n);
                }

                // Updates difference cell for a row based on qoh and input.raw (or empty)
                function updateDifferenceForRow($row) {
                    let qohText = $row.find('.qoh').text().trim().replace(/,/g, '');
                    let qoh = parseInt(qohText) || 0;
                    let $input = $row.find('.physical');
                    let physicalRaw = $input.data('raw'); // may be undefined/null
                    if (physicalRaw === undefined || physicalRaw === null) {
                        // no input provided yet -> show empty difference and no color
                        $row.find('.difference').text('');
                        $row.find('.difference').removeClass('text-success text-danger');
                        // ensure input display is empty (do not overwrite if user typing)
                        if (!$input.is(':focus')) {
                            $input.val('');
                        }
                        return;
                    }

                    let physical = parseInt(physicalRaw) || 0;
                    let diff = Math.round(physical - qoh);

                    // update displays
                    $row.find('.qoh').text(formatNumberNoDecimals(qoh));
                    $input.val(formatNumberNoDecimals(physical));
                    $row.find('.difference').text(formatNumberNoDecimals(diff));

                    // color difference: +ve green, -ve red, zero default
                    let diffCell = $row.find('.difference');
                    diffCell.removeClass('text-success text-danger');
                    if (diff > 0) diffCell.addClass('text-success');
                    else if (diff < 0) diffCell.addClass('text-danger');
                }

                // on input: sanitize typed characters, update data-raw and difference
                $(document).on('input', '.physical', function (e) {
                    let $input = $(this);
                    let typed = $input.val();
                    let n = sanitizeRawValue(typed);
                    // store numeric in data-raw (null if empty)
                    $input.data('raw', n);
                    // if user is typing, we still format after small delay to avoid caret jump
                    // here we set formatted value immediately (acceptable UX)
                    if (n === null) {
                        // keep field empty
                        $input.val('');
                    } else {
                        $input.val(formatNumberNoDecimals(n));
                    }
                    // update diff for this row
                    updateDifferenceForRow($input.closest('tr'));
                });

                // allow only digits via keydown (still allow ctrl/cmd combos)
                $(document).on('keydown', '.physical', function (e) {
                    let allowedKeys = [8, 9, 13, 27, 46, 35, 36, 37, 38, 39, 40];
                    if (allowedKeys.indexOf(e.keyCode) !== -1) return;
                    if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88, 90].indexOf(e.keyCode) !== -1) return;
                    if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105)) return;
                    e.preventDefault();
                });

                // on paste, sanitize after paste
                $(document).on('paste', '.physical', function () {
                    let $input = $(this);
                    setTimeout(function () {
                        let n = sanitizeRawValue($input.val());
                        $input.data('raw', n);
                        if (n === null) $input.val('');
                        else $input.val(formatNumberNoDecimals(n));
                        updateDifferenceForRow($input.closest('tr'));
                    }, 50);
                });

                // Handle snapshot button click
                $('#snapshot-stock-btn').on('click', function () {
                    if (!confirm(`Are you sure you wan't save the current stock?`)) {
                        return;
                    }

                    $.ajax({
                        url: "{{ route('stock-taking.snapshot') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        beforeSend: function () {
                            $("#loading").show();
                            $('#snapshot-stock-btn').prop('disabled', true);
                        },
                        success: function (res) {
                            $("#loading").hide();
                            $('#snapshot-stock-btn').prop('disabled', false);
                            if (res.success) {
                                notify(res.message, 'top', 'right', 'success');
                            } else {
                                notify(res.message || 'Failed to create snapshot.', 'top', 'right', 'danger');
                            }
                        },
                        error: function (xhr) {
                            $("#loading").hide();
                            $('#snapshot-stock-btn').prop('disabled', false);
                            let message = 'Error occurred while creating snapshot.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            notify(message, 'top', 'right', 'danger');
                        }
                    });
                });


                // on blur ensure formatting correct
                $(document).on('blur', '.physical', function () {
                    let $input = $(this);
                    let n = sanitizeRawValue($input.val());
                    $input.data('raw', n);
                    if (n === null) $input.val('');
                    else $input.val(formatNumberNoDecimals(n));
                    updateDifferenceForRow($input.closest('tr'));
                });

                // initialize table rows: set inputs empty and differences blank
                $('#fixed-header2 tbody tr').each(function () {
                    let $r = $(this);
                    let qohText = $r.find('.qoh').text().trim().replace(/,/g, '');
                    let qoh = Math.round(parseFloat(qohText) || 0);
                    $r.find('.qoh').text(formatNumberNoDecimals(qoh));
                    let $inp = $r.find('.physical');
                    if ($inp.length) {
                        $inp.data('raw', null);
                        $inp.val('');
                    }
                    $r.find('.difference').text('');
                });

                // DataTable initialization (if not already)
                var table = $('#fixed-header2').DataTable({
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    order: [],
                    columnDefs: [
                        { targets: [1, 2, 3], className: 'dt-body-right' }
                    ],
                    responsive: false,
                    autoWidth: false
                });
                
                // Preview button — collect numeric from data-raw (only rows where user entered a value)
                $('#process-adjustments-btn').on('click', function () {
                    let items = [];
                    let tbody = $('#preview-table tbody');
                    tbody.empty();

                    // choose rows for ALL products (including paginated ones)
                    let rows = table.rows().nodes();

                    $(rows).each(function () {
                        let $r = $(this);
                        let product_id = $r.data('product-id');
                        let product_name = $r.find('td:first').text().trim();
                        let qohText = $r.find('.qoh').text().trim().replace(/,/g, '');
                        let qoh = parseInt(qohText) || 0;

                        let $input = $r.find('.physical');
                        let physical = $input.data('raw'); // null if empty

                        // skip rows without user input
                        if (physical === undefined || physical === null) return;

                        let diff = Math.round(physical - qoh);

                        // skip if no difference
                        if (diff === 0) return;

                        let type = diff > 0 ? 'Increase' : 'Decrease';
                        items.push({ product_id, qoh: Math.round(qoh), physical: Math.round(physical), diff, product_name, type });
                    });

                    if (items.length === 0) {
                        toastr.info('No adjustments needed. Make sure you entered counts for items you want to adjust.');
                        return;
                    }

                    // Populate preview modal
                    let totalIncrease = 0, totalDecrease = 0;
                    items.forEach((item, index) => {
                        if (item.diff > 0) totalIncrease += item.diff;
                        else totalDecrease += Math.abs(item.diff);
                        let color = item.diff > 0 ? 'text-success' : 'text-danger';
                        tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.product_name}</td>
                        <td class="text-right">${formatNumberNoDecimals(item.qoh)}</td>
                        <td class="text-right">${formatNumberNoDecimals(item.physical)}</td>
                        <td class="${color} text-right">${formatNumberNoDecimals(item.diff)}</td>
                        <td class="${color}"><strong>${item.type}</strong></td>
                    </tr>
                `);
                    });

                    tbody.append(`
                <tr>
                    <td colspan="6" class="text-right">
                        <small>Total Increase: ${formatNumberNoDecimals(totalIncrease)} &nbsp; | &nbsp; Total Decrease: ${formatNumberNoDecimals(totalDecrease)}</small>
                    </td>
                </tr>
            `);

                    $('#confirmProcessBtn').data('items', items);
                    $('#previewModal').modal('show');
                });

                // Confirm -> send to backend
                $('#confirmProcessBtn').on('click', function () {
                    let items = $(this).data('items') || [];
                    if (items.length === 0) {
                        toastr.error('No items to process.');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('stock-taking.process') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            date: $('input[name="sale_date"]').val(),
                            items: items
                        },
                        beforeSend: function () {
                            $('#previewModal').modal('hide');
                            $("#loading").show();
                        },
                        success: function (res) {
                            $("#loading").hide();
                            if (res.success) {
                                notify(res.message, 'top', 'right', 'success');
                                setTimeout(() => location.reload(), 1200);
                            } else {
                                // toastr.error(res.message || 'Failed to process stock taking.');
                                notify(res.message, 'top', 'right', 'danger');
                            }
                        },
                        error: function (xhr) {
                            $("#loading").hide();
                            // toastr.error("Error occurred while processing adjustments.");
                            notify('Error occurred while processing adjustments.', 'top', 'right', 'danger');
                        }
                    });
                });

            });
        
            $(document).ready(function () {
                var baseUrl = $('#count-sheet-tablist').attr('href');

                $('#count-sheet-tablist').on('click', function (e) {
                    e.preventDefault();
                    $('#confirmModal').modal('show');
                });

                $('#confirmYes').on('click', function () {
                    $('#confirmModal').modal('hide');
                    window.open(baseUrl + '?showQoH=1', '_blank');
                });

                $('#confirmNo').on('click', function () {
                    $('#confirmModal').modal('hide');
                    window.open(baseUrl + '?showQoH=0', '_blank');
                });
            });
        </script>

    @endpush