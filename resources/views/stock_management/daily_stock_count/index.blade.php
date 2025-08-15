@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Daily Stock Count
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Daily Stock Count </a></li>
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
                <a class="nav-link active text-uppercase" id="daily-stock-tablist" data-toggle="pill"
                   href="{{ url('inventory/daily-stock-count') }}" role="tab"
                   aria-controls="stock_adjustment" aria-selected="true">Daily Stock Count</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="outgoing-stock-tablist" data-toggle="pill"
                   href="{{ url('inventory/out-going-stock') }}" role="tab"
                   aria-controls="stock_list" aria-selected="false">Outgoing Stock
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="count-sheet-tablist"
                   href="{{ url('inventory/inventory-count-sheet/Inventory Count Sheet') }}" role="tab"
                   aria-controls="stock_list" aria-selected="false" target="_blank">Inventory Count Sheet
                </a>
            </li>
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="new_sale" role="tabpanel" aria-labelledby="new_sale-tab">
                    <form id="daily-stock" action="{{ route('daily-stock-count-pdf-gen') }}" method="post"
                          enctype="multipart/form-data">
                        @csrf()
                        <div class="row">
                            <div class="col-md-6">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-download mr-1"></i> Export
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('daily-stock-count-pdf-gen') }}" target="_blank">
                                            <i class="far fa-file-pdf text-danger mr-2"></i>PDF
                                        </a>
                                        <a class="dropdown-item" href="{{ route('daily-stock-count.export', ['date' => $today]) }}">
                                            <i class="far fa-file-excel text-success mr-2"></i>Excel
                                        </a>
                                        {{-- Add CSV option if needed later --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" style="margin-left: 2.5%">
                                <label style="margin-left: 80%" for="issued_date"
                                       class="col-form-label text-md-right">Date:</label>
                            </div>
                            <div class="col-md-3" style="margin-left: -3.1%">
                                <input type="text" name="sale_date" class="form-control"
                                       id="d_auto_8" value="{{$today}}" required>
                            </div>
                        </div>


                        <!-- ajax loading gif -->
                        <div id="loading">
                            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                        </div>


                        <div id="tbody2" class="table-responsive">
                            <table id="fixed-header2" class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Sold Qty</th>
                                    <th>QOH</th>
                                    <th>Physical Stock</th>
                                    <th>Difference</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{$product['product_name']}}</td>
                                        <td>{{ number_format($product['quantity_sold'],0) }}</td>
                                        <td>{{ number_format($product['quantity_on_hand'],0) }}</td>
                                        <td>
                                            <input type="number" class="form-control physical_stock_input" 
                                                   data-product-id="{{$product['product_id']}}"
                                                   data-qoh="{{$product['quantity_on_hand']}}"
                                                   value="" min="0">
                                        </td>
                                        <td class="difference_output">0</td>
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
                                           value="0" onchange="filterByDate()"/>
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
                                    <button class="btn btn-primary" hidden>Transfer</button>
                                    <button class="btn btn-danger" id="deselect-all" hidden>Cancel</button>
                                    <button class="btn btn-success" type="button" id="process-adjustments-btn">
                                        <span class="fa fa-save" aria-hidden="true"></span> Process Adjustments
                                    </button>
                                    <button class="btn btn-secondary" type="submit">
                                        <span class="fa fa-print" aria-hidden="true"></span> Print
                                    </button>
                                </div>
                            </div>
                        </div>


                    </form>

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
                        filterShow: '{{ route('daily-stock-count-filter') }}'
                    }
                };

            </script>
            <script src="{{asset("assets/apotek/js/outgoing-stock.js")}}"></script>

            <script>
                $(document).ready(function() {
                    // Listen for the click event on the Transfer History tab
                    $('#outgoing-stock-tablist').on('click', function(e) {
                        e.preventDefault(); // Prevent default tab switching behavior
                        var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                        window.location.href = redirectUrl; // Redirect to the URL
                    });

                    // Calculate difference when physical stock input changes
                    $('.physical_stock_input').on('input', function() {
                        var physicalStock = parseFloat($(this).val()) || 0;
                        var qoh = parseFloat($(this).data('qoh')) || 0;
                        var difference = physicalStock - qoh;
                        $(this).closest('tr').find('.difference_output').text(difference.toFixed(0)); // Display difference
                    });

                    $('#process-adjustments-btn').on('click', function() {
                        var adjustments = [];
                        $('#fixed-header2 tbody tr').each(function() {
                            var productId = $(this).find('.physical_stock_input').data('product-id');
                            var physicalStock = parseFloat($(this).find('.physical_stock_input').val());
                            var qoh = parseFloat($(this).find('.physical_stock_input').data('qoh'));

                            if (!isNaN(physicalStock) && physicalStock !== null) { // Only send if a value is entered
                                adjustments.push({
                                    product_id: productId,
                                    physical_stock: physicalStock,
                                    qoh: qoh
                                });
                            }
                        });

                        if (adjustments.length > 0) {
                            $.ajax({
                                url: '{{ route('daily-stock-count.process-adjustment') }}',
                                method: 'POST',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    adjustments: adjustments
                                },
                                success: function(response) {
                                    if (response.success) {
                                        notify(response.message, 'top', 'right', 'success');
                                        // Optionally, refresh the page or update the table
                                        location.reload();
                                    } else {
                                        notify(response.message, 'top', 'right', 'danger');
                                    }
                                },
                                error: function(xhr) {
                                    var errors = xhr.responseJSON.errors;
                                    var message = 'Error processing adjustments.';
                                    if (errors) {
                                        message += '\n' + Object.values(errors).join('\n');
                                    }
                                    notify(message, 'top', 'right', 'danger');
                                }
                            });
                        } else {
                            notify('No physical stock values entered for adjustment.', 'top', 'right', 'warning');
                        }
                    });
                });
            </script>


    @endpush
