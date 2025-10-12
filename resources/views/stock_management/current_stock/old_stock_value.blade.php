@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Old Stock
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Old Stock </a></li>
@endsection

@section("content")


    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="current-stock-tablist" data-toggle="pill"
                    href="{{ url('inventory/current-stocks') }}" role="tab" aria-controls="current-stock"
                    aria-selected="true">Current Stock</a>
            </li>
            @if (auth()->user()->checkPermission('View Current Stock Value'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="all-stock-tablist" data-toggle="pill"
                        href="{{ url('inventory/current-stock-value') }}" role="tab" aria-controls="stock_list"
                        aria-selected="false">Current Stock Value
                    </a>
                </li>
            @endif
            @if (auth()->user()->checkPermission('View OLd Stock Value'))
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" id="old-stock-tablist" data-toggle="pill"
                        href="{{ url('inventory/old-stocks') }}" role="tab" aria-controls="stock_list" aria-selected="false">Old
                        Stock Value
                    </a>
                </li>
            @endif
        </ul>
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('old-stocks') }}">
                    @csrf

                    <div class="d-flex justify-content-end mb-3 align-items-center">
                        <label class="mr-2" for="">Date:</label>
                        <input type="text" name="old_stock_date" id="old_stock_date" class="form-control w-auto">
                    </div>
                    <div class="d-flex justify-content-end mb-3">
                        <div class="d-flex align-items-center" style="width: 284px;">
                            <label for="price_category" class="form-label mb-0"
                                style="white-space: nowrap; margin-right: 10px;">Price Type:</label>
                            <select name="price_category" id="price_category" class="form-control"
                                onchange="this.form.submit()">
                                @foreach($price_categories as $price_category)
                                    <option value="{{ $price_category->id }}" {{ $price_category->id == request('price_category', 1) ? 'selected' : '' }}>
                                        {{ $price_category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
                {{--Stock Value Div Begins here--}}
                <div class="table-responsive" id="summary">

                    <table id="old_stock" class="table table-striped table-hover mb-3"
                        style="background: white;width: 100%">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th style="text-align: center;">Quantity</th>
                                <th style="text-align: right;">Buy Price</th>
                                <th style="text-align: right;">Sell Price</th>
                                <th style="text-align: right;">Total Buy</th>
                                <th style="text-align: right;">Total Sell</th>
                            </tr>
                        </thead>
                        {{-- @dd($stocks) --}}
                        <tbody>
                            @foreach ($stocks as $stock)
                                <tr>
                                    <td>{{ $stock->name . ' ' . ($stock->brand ? $stock->brand . ' ' : $stock->brand) . ($stock->pack_size ?? '') . ($stock->sales_uom ?? '') }}
                                    </td>
                                    <td style="text-align: center;">{{ number_format($stock->quantity, 0) ?? 0 }}
                                    </td>
                                    <td style="text-align: right;">{{ number_format($stock->buy_price, 2) ?? 0 }}
                                    </td>
                                    <td style="text-align: right;">{{ number_format($stock->sell_price, 2) ?? 0 }}</td>
                                    <td style="text-align: right;">
                                        {{ number_format($stock->quantity * $stock->buy_price, 2) ?? 0 }}
                                    </td>
                                    <td style="text-align: right;">
                                        {{ number_format($stock->quantity * $stock->sell_price, 2) ?? 0 }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>



            </div>
        </div>
    </div>
    </div>

@endsection

@push("page_scripts")
    <script src="{{asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/ac-datepicker.js')}}"></script>
    <script>
        //Datatable and Tabs managed here
        $(document).ready(function () {

            $('#old_stock').DataTable({
                responsive: true,
                order: [[0, 'asc']]
            });

            $('#current-stock-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#old-stock-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#all-stock-tablist').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });
        });
    </script>
@endpush