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
    Stock Adjustments
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Adjustments</a></li>
@endsection

@section("content")


    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="current-stock-tablist"
                    href="{{ route('new-stock-adjustment') }}" aria-controls="current-stock" aria-selected="true">Stock
                    Adjustment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="all-stock-tablist" href="{{ route('stock-adjustments-history') }}"
                    aria-controls="stock_list" aria-selected="false">Adjustment History
                </a>
            </li>
        </ul>
        <div class="card">
            <div class="card-body">
                {{-- <div class="form-group row d-flex">
                    <div class="col-md-6">
                        <label for="stock_status" class="col-form-label text-md-right">Status:</label>
                        <select name="stock_status" class="js-example-basic-single form-group" id="stock_status_id">
                            <option name="store_name" value="all">All</option>
                            <option name="store_name" value="1">In Stock</option>
                            <option name="store_name" value="0">Out Of Stock</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="col-form-label text-md-left">Type:</label>

                        <select name="category" class="js-example-basic-single form-control" id="category_id">
                            <option name="store_name" value="1">Summary</option>
                            <option name="store_name" value="0">Detailed</option>
                        </select>
                    </div>
                </div> --}}
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
                                {{-- <th>Type</th> --}}
                                {{-- <th>Quantity</th> --}}
                                {{-- <th>Date</th> --}}
                                {{-- <th hidden>Branch</th> --}}
                                {{-- <th>Reason</th> --}}
                                {{-- <th>Adjusted By</th> --}}
                                @if (userCan('view stock adjustments'))
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($adjustments as $adjustment)
                                <tr>
                                    <td id="name_{{ $adjustment->currentStock->id }}">
                                        {{ $adjustment->currentStock->product->name }}
                                        {{ $adjustment->currentStock->product->brand ? ' ' . $adjustment->currentStock->product->brand : '' }}
                                        {{ $adjustment->currentStock->product->pack_size ?? '' }}{{ $adjustment->currentStock->product->sales_uom ?? '' }}
                                    </td>
                                    <td id="category_{{ $adjustment->currentStock->id }}">{{ $adjustment->currentStock->product->category->name ?? 'N/A' }}</td>
                                    {{-- <td id="type_{{ $adjustment->currentStock->id }}">
                                        <span
                                            class="badge p-2 btn-rounded badge-{{ $adjustment->adjustment_type === 'increase' ? 'success' : 'danger' }}" style="width: 70px;">
                                            {{ ucfirst($adjustment->adjustment_type) }}
                                        </span>
                                    </td>
                                    <td id="quantity_{{ $adjustment->currentStock->id }}">{{ number_format($adjustment->new_quantity) }}</td>
                                    <td id="date_{{ $adjustment->currentStock->id }}">{{ $adjustment->created_at->format('Y-m-d') }}</td>
                                    <td id="reason_{{ $adjustment->currentStock->id }}">{{ $adjustment->reason }}</td>
                                    <td id="adjusted_by_{{ $adjustment->currentStock->id }}">{{ $adjustment->user->name ?? 'N/A' }}</td> --}}
                                    @if (userCan('create stock adjustments'))
                                        <td id="actions_{{ $adjustment->currentStock->id }}">
                                            <!-- Adjustment Button -->
                                            <button type="button" class="btn btn-success btn-sm btn-rounded btn-show-adjustment"
                                                data-toggle="modal" data-target="#showAdjustedStockModal"
                                                data-id="{{ $adjustment->id }}" data-product-id="{{ $adjustment->currentStock->product->id }}" data-product-name="{{ $adjustment->currentStock->product->name }}"
                                                data-product-brand="{{ $adjustment->currentStock->product->brand }}"
                                                data-product-pack-size="{{ $adjustment->currentStock->product->pack_size }}"
                                                data-product-sales-uom="{{ $adjustment->currentStock->product->sales_uom }}"
                                                data-adjusted-qty="{{ $adjustment->new_quantity }}">
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
            const product_name = $btn.data('product-name');
            const product_brand = $btn.data('product-brand');
            const product_pack_size = $btn.data('product-pack-size');
            const product_sales_uom = $btn.data('product-sales-uom');
            const current_stock = $btn.data('current-stock');
            const id = $btn.data('id');
            const product_id = $btn.data('product-id');
            let stock = Number(current_stock);
            let displayStock = (stock % 1 === 0) ? stock : stock.toFixed(1);
            $('#show_product_name').text(product_name+' '+product_brand+' '+product_pack_size+product_sales_uom);
            $('#show_current_stock').text(displayStock);
            $('#confirmAdjustmentProductName').text(product_name);
            $('#product_id').val(product_id);
            $('#stock_id').val(id);
            $('#current_stock_input').val(current_stock);

            $('#showStockAdjustment').modal('show');
        });

        function formatNumber(num) {
            if (num === null || num === undefined || num === '') return '';
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

    </script>
@endpush