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
                    <a class="nav-link active text-uppercase" id="daily-stock-tablist" data-toggle="pill"
                        href="{{ url('inventory/daily-stock-count') }}" role="tab"
                        aria-selected="true">Daily Stock Count</a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Outgoing Stock'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="outgoing-stock-tablist" data-toggle="pill"
                        href="{{ url('inventory/out-going-stock') }}" role="tab"
                        aria-selected="false">Outgoing Stock
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
                    <a class="nav-link text-uppercase" id="count-sheet-tablist"
                        href="{{ route('stock-taking') }}" role="tab"
                        aria-selected="false">Stock Taking
                    </a>
                </li>
            @endif
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="new_sale" role="tabpanel" aria-labelledby="new_sale-tab">
                    {{-- <form id="daily-stock" action="{{ route('daily-stock-count-pdf-gen') }}" method="post" --}} <form
                        id="daily-stock" action="" method="post" enctype="multipart/form-data">
                        @csrf()
                        <!-- ajax loading gif -->
                        <div id="loading">
                            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                        </div>

                        <div class="d-flex justify-content-end mb-3 align-items-center">
                            <label class="mr-2" for="">Date:</label>
                            <input type="text" name="sale_date" id="d_auto_8" class="form-control w-auto">
                        </div>
                        <div id="tbody2" class="table-responsive">
                            <table id="fixed-header2" class="display table nowrap table-striped table-hover"
                                style="width:100%">

                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Sold Qty</th>
                                        <th>QOH</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                        <div class="row" hidden>
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
                const today = new Date().toISOString().slice(0, 10);
                fetchDailyCount(today); // Fetch data for today on page load
                document.getElementById('d_auto_8').value = today;

                // Listen for the click event on the Transfer History tab
                $('#outgoing-stock-tablist').on('click', function (e) {
                    e.preventDefault(); // Prevent default tab switching behavior
                    var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                    window.location.href = redirectUrl; // Redirect to the URL
                });

                // Calculate difference when physical stock input changes
                $('.physical_stock_input').on('input', function () {
                    var physicalStock = parseFloat($(this).val()) || 0;
                    var qoh = parseFloat($(this).data('qoh')) || 0;
                    var difference = physicalStock - qoh;
                    $(this).closest('tr').find('.difference_output').text(difference.toFixed(0)); // Display difference
                });

                $('#process-adjustments-btn').on('click', function () {
                    var adjustments = [];
                    $('#fixed-header2 tbody tr').each(function () {
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
                            success: function (response) {
                                if (response.success) {
                                    notify(response.message, 'top', 'right', 'success');
                                    // Optionally, refresh the page or update the table
                                    location.reload();
                                } else {
                                    notify(response.message, 'top', 'right', 'danger');
                                }
                            },
                            error: function (xhr) {
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

                $('#d_auto_8').on('change input', function () {
                    fetchDailyCount($(this).val());
                });

            });

            //daily stock count date picker
            $("#d_auto_8")
                .datepicker({
                    todayHighlight: true,
                    format: "yyyy-mm-dd",
                    changeYear: true,
                })
                .on("change", function () {
                    // filterByDate();
                    $(".datepicker").hide();
                })
                .attr("readonly", "readonly");

            let dailyTable = $("#fixed-header2").DataTable({
                destroy: true,
                columns: [
                    { data: "product_name" },
                    { data: "total_sold" },
                    { data: "current_stock" },
                ],
            });

            function fetchDailyCount(date) {
                if (!date) {
                    return;
                }

                $("#loading").show();
                $.ajax({
                    url: config.routes.filterDailyStockCount,
                    type: "get",
                    dataType: "json",
                    data: {
                        date: date,
                    },
                    success: function (data) {
                        // console.log("Response products:", data);

                        dailyTable.clear();
                        let rows = data.items
                            .filter(item => item.store_id != null)
                            .map(item => {
                                return {
                                    product_name:
                                        (item.product ? item.product.name : "") + " " +
                                        (item.product && item.product.brand ? item.product.brand + " " : "") +
                                        (item.product && item.product.pack_size ? item.product.pack_size : "") +
                                        (item.product && item.product.sales_uom ? item.product.sales_uom : ""),
                                    total_sold: numberWithCommas(item.total_sold),
                                    current_stock: numberWithCommas(item.current_stock),
                                };
                            });
                        dailyTable.rows.add(rows).draw();

                    },
                    complete: function () {
                        $("#loading").hide();
                    },
                });
            }

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

            function numberWithCommas(digit) {
                return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        </script>
    @endpush