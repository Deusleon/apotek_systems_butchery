@extends("layouts.master")

@section('page_css')
    <style>
        .small-table table td,
        .small-table table th {
            padding: 0.35rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('content-title')
    Stock Adjustment
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Adjustment</a></li>
@endsection

@section("content")


    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            @if(auth()->user()->checkPermission('Create Stock Adjustment'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="current-stock-tablist" href="{{ route('new-stock-adjustment') }}"
                        aria-controls="current-stock" aria-selected="true">Stock
                        Adjustment</a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Stock Adjustment'))
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" id="all-stock-tablist"
                        href="{{ route('stock-adjustments-history') }}" aria-controls="stock_list"
                        aria-selected="false">Adjustment History
                    </a>
                </li>
            @endif
        </ul>
        <div class="card">
            <div class="card-body">
                <!-- main table -->
                {{--All Summary--}}
                <div class="table-responsive" id="all_summary_stocks">
                    {{--Summary--}}
                    <table id="history" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%; font-size: 14px;">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                @if(auth()->user()->checkPermission('View Stock Adjustment'))
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($adjustments as $adjustment)
                                <tr>
                                    <td>
                                        {{ $adjustment->name }}
                                        {{ $adjustment->brand ? ' ' . $adjustment->brand : '' }}
                                        {{ $adjustment->pack_size ?? '' }}{{ $adjustment->sales_uom ?? '' }}
                                    </td>
                                    <td>{{ $adjustment->category_name }}</td>
                                    @if(auth()->user()->checkPermission('View Stock Adjustment'))
                                        <td>
                                            <button type="button" class="btn btn-success btn-sm btn-rounded btn-show-adjustment"
                                                data-toggle="modal" data-target="#showStockAdjustment"
                                                data-product-id="{{ $adjustment->product_id }}"
                                                data-product-name="{{ $adjustment->name }}"
                                                data-product-brand="{{ $adjustment->brand }}"
                                                data-product-pack-size="{{ $adjustment->pack_size }}"
                                                data-product-sales-uom="{{ $adjustment->sales_uom }}"
                                                data-more-details='@json($detailed[$adjustment->product_id] ?? [])'>
                                                Show
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>

            </div>
        </div>
    </div>
    </div>

    @include('stock_management.adjustments.show_adjustment_modal')
@endsection

@include('partials.notification')

@push("page_scripts")
    <script>
        $('#history').DataTable({
            searching: true,
            responsive: true,
            order: [
                [0, 'asc']
            ]
        });

        $(document).on('click', '.btn-show-adjustment', function () {
            const $btn = $(this);
            const productId = $btn.data('product-id');

            const productName = ($btn.data('product-name') || '');
            const brand = ($btn.data('product-brand') || '');
            const pack = ($btn.data('product-pack-size') || '');
            const uom = ($btn.data('product-sales-uom') || '');

            let detailsByBatch = $btn.data('more-details');
            if (typeof detailsByBatch === "string") {
                try {
                    detailsByBatch = JSON.parse(detailsByBatch);
                } catch (e) {
                    detailsByBatch = {};
                }
            }

            $('#show_product_name').text(`${productName} ${brand} ${pack}${uom}`);

            const $tbody = $('#show_items_table_body');
            $tbody.empty();

            const batches = Object.keys(detailsByBatch || {});
            if (batches.length === 0) {
                $tbody.append('<tr><td colspan="6" class="text-center text-muted">No adjustments found</td></tr>');
            } else {
                batches.forEach(batchNo => {
                    let logs = detailsByBatch[batchNo] || [];

                    if (!Array.isArray(logs)) logs = Object.values(logs);

                    // header row for batch
                    $tbody.append(
                        `<tr class="table-active">
                                                        <td colspan="7"><strong>Batch #:</strong> ${batchNo}</td>
                                                    </tr>`
                    );

                    logs.forEach(log => {
                        const created = extractDate(log.created_at);
                        const type = (log.adjustment_type === 'increase' ? 'Postive' : 'Negative' || '-');
                        const qty = (typeof log.adjustment_quantity !== 'undefined'
                            ? formatNumber(log.adjustment_quantity)
                            : (log.quantity ?? '-'));
                        const reason = (log.reason ?? '-');
                        const userNm = (log.user && log.user.name
                            ? log.user.name
                            : '-');
                        const prevqty = (typeof log.previous_quantity !== 'undefined'
                            ? formatNumber(log.previous_quantity)
                            : '-');
                        const newqty = (typeof log.new_quantity !== 'undefined'
                            ? formatNumber(log.new_quantity) : '-');

                        $tbody.append(
                            `<tr>
                                            <td>${created}</td>
                                            <td>
                                                <span class="badge p-2" style="${type === 'Postive' ? 'background-color:#BBF7D0; color:#48bb78;' : 'background-color:#FECACA; color:#f56565;'}width:60px;" >
                                                    ${capitalize(type)}
                                                </span>
                                            </td>
                                            <td>${qty}</td>
                                            <td>${prevqty}</td>
                                            <td>${newqty}</td>
                                            <td>${reason}</td>
                                            <td>${userNm}</td>
                                        </tr>`
                        );
                    });
                });

            }

            $('#showStockAdjustment').modal('show');
        });
        function extractDate(val) {
            if (!val) return '-';
            const d = new Date(val);
            return isNaN(d) ? String(val).split(' ')[0] : d.toISOString().split('T')[0];
        }
        function capitalize(s) { s = s || ''; return s.charAt(0).toUpperCase() + s.slice(1); }

        function formatNumber(num) {
            if (num === null || num === undefined || num === '') return '';
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 1
            });
        }

    </script>
@endpush