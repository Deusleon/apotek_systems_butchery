@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Price List
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Price List </a></li>
@endsection

@section("content")

    <style>
        .datepicker>.datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
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

        .select2-container {
            width: 103% !important;
        }
    </style>

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="form-group row">
                        <div class="col-md-6" style="margin-left: 2%">
                        </div>
                        <div class="col-md-3">
                            <label for="price_category" class="col-form-label text-md-right"
                                style="margin-left: 68%">Category:</label>
                        </div>
                        <div class="col-md-3" style="margin-left: -2.6%;">
                            <select name="price_category" class="js-example-basic-single form-control" id="price_category">
                                @foreach($price_categories as $price_category)
                                    <option value="{{$price_category->id}}">
                                        {{ $price_category->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6" style="margin-left: 2%">
                        </div>
                        <div class="col-md-3">
                            <label for="type_id" class="col-form-label text-md-right" style="margin-left: 68%">Type:</label>
                        </div>
                        <div class="col-md-3" style="margin-left: -2.6%;">
                            <select name="type_id" class="js-example-basic-single form-control" id="type_id">
                                <option readonly value="" id="store_name_edit" disabled>Select Type...
                                </option>
                                <option name="store_name" value="1" selected>Current</option>
                                <option name="store_name" value="pending">Pending</option>
                                <option name="store_name" value="0">History</option>
                            </select>
                        </div>
                    </div>

                    <div id="pendingTable" class="table-responsive">
                        <table id="pendingPrices" class="display table nowrap table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    @if($batch_enabled === 'YES')
                                        <th>Batch Number</th>
                                    @endif
                                    <th>Buy Price</th>
                                    <th>Sell Price</th>
                                    <th>Profit%</th>
                                    @if(auth()->user()->checkPermission('Edit Price List'))
                                        <th>Action</th>
                                    @endif
                                    <th hidden>Category ID</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                    <div id="tbody1" class="table-responsive" style="display: none;">
                        <table id="fixed-header2" class="display table nowrap table-striped table-hover" style="width:100%">

                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    @if($batch_enabled === 'YES')
                                        <th>Batch Number</th>
                                    @endif
                                    <th>Buy Price</th>
                                    <th>Sell Price</th>
                                    <th>Profit%</th>
                                    @if(auth()->user()->checkPermission('Edit Price List'))
                                        <th>Action</th>
                                    @endif
                                    <th hidden>Category ID</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>
                    </div>

                    <div id="historyTable" class="table-responsive" style="display: none;">
                        <table id="priceHistory" class="display table nowrap table-striped table-hover"
                            style="width: 100%;">

                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    @if($batch_enabled === 'YES')
                                        <th>Batch Number</th>
                                    @endif
                                    <th>Buy Price</th>
                                    <th>Sell Price</th>
                                    {{-- <th>Purchase Date</th> --}}
                                    <th hidden>Profit%</th>
                                    <th hidden>Real Time</th>
                                </tr>
                            </thead>

                            <tbody>
                            </tbody>

                        </table>
                    </div>

                </div>

                <div id="loading">
                    <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                </div>

            </div>
        </div>
    </div>

    @include('stock_management.price_list.edit')
    @include('stock_management.price_list.show')
    @include('stock_management.price_list.history')
@endsection

@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    <script src="{{asset("assets/apotek/js/stock-transfer.js")}}"></script>

    @include('partials.notification')

    <script>

        // On page load, restore from localStorage (if present)
        $(document).ready(function () {
            var savedCategory = localStorage.getItem('price_category');
            var savedType = localStorage.getItem('type_id');

            if (savedCategory !== null) {
                $('#price_category').val(savedCategory);
            }
            if (savedType !== null) {
                $('#type_id').val(savedType);
            }

            // Trigger change once to load the table using saved values
            $('#price_category, #type_id').trigger('change');
        });

        // Save on change
        $('#price_category, #type_id').on('change', function () {
            localStorage.setItem('price_category', $('#price_category').val());
            localStorage.setItem('type_id', $('#type_id').val());
        });

        $('#tbody1').on('click', '#detail', function () {
            var data = $('#fixed-header2').DataTable().row($(this).parents('tr')).data();

            var e = document.getElementById("price_category");
            var value = e.options[e.selectedIndex].value;

            retrivePriceHistory(value, data.product_id);
        });

        $('#price_category, #type_id').on('change', function () {
            var selectedCategory = $('#price_category').val();
            var selectedType = $('#type_id').val();

            $('#pendingTable, #tbody1, #tbody_detailed').hide();

            // AJAX request backend
            $.ajax({
                url: '{{ route("fetch-price-list") }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    category_id: selectedCategory,
                    type: selectedType
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    console.log('FechResponse: ', response);
                    if (selectedType === "pending") {
                        renderTable('#pendingPrices', response);
                        $('#pendingTable').show();
                    } else if (selectedType === "1") {
                        renderTable('#fixed-header2', response);
                        $('#tbody1').show();
                    } else if (selectedType === "0") {
                        renderTable('#detailed_body', response);
                        $('#tbody_detailed').show();
                    }
                },
                complete: function () {
                    $('#loading').hide();
                },
                error: function () {
                    alert('Error fetching data!');
                }
            });
        });

        function renderTable(tableId, data) {
            var table = $(tableId).DataTable();
            table.clear();
            table.rows.add(data);
            table.draw();
        }

        $('#edit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var name = button.data('name');
            var unit_cost_edit = parseFloat(button.data('unit-cost'));
            var sell_price_edit = parseFloat(button.data('price'));
            var id = button.data('id');
            var brand = button.data('brand');
            var pack_size = button.data('pack-size');
            var sales_uom = button.data('sales-uom');
            var price_category_id = $('#price_category').val();
            var price_category = $('#price_category option:selected').text().trim();
            var selected_type = $('#type_id').val();

            var modal = $(this);

            modal.find('.modal-body #name').val(name + ' ' + brand + ' ' + pack_size + sales_uom);
            modal.find('.modal-body #brand_edit').val(brand);
            modal.find('.modal-body #unit_cost_edit').val(unit_cost_edit);
            modal.find('.modal-body #unit_cost_edit_to_show').val(unit_cost_edit.toLocaleString('en-US'));
            modal.find('.modal-body #price_category_edit').val(price_category);
            modal.find('.modal-body #sell_price_edit').val(sell_price_edit);
            modal.find('.modal-body #sell_price_edit_to_show').val(sell_price_edit.toLocaleString('en-US'));
            modal.find('.modal-body #pack_size_edit').val(pack_size);
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #price_category').val(price_category);
            modal.find('.modal-body #selected_type').val(selected_type);
            modal.find('.modal-body #price_category_id').val(price_category_id);
        });

        $('#editPriceForm').on('submit', function (e) {
            var unitCostField = $('#unit_cost_edit');
            var sellPriceField = $('#sell_price_edit');

            var unitCostValue = unitCostField.val().replace(/,/g, '');
            var sellPriceValue = sellPriceField.val().replace(/,/g, '');

            unitCostField.val(unitCostValue);
            sellPriceField.val(sellPriceValue);
        });

        $('#show').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body #name_edit').val(button.data('name'));
            modal.find('.modal-body #brand_edit').val(button.data('brand'));
            modal.find('.modal-body #unit_cost_edit').val(button.data('unit'));
            modal.find('.modal-body #price_category_edit').val(button.data('category'));
            modal.find('.modal-body #sell_price_edit').val(button.data('sell'));
            modal.find('.modal-body #pack_size_edit').val(button.data('pack-size'));
            modal.find('.modal-body #d_auto_4').val(button.data('batch'));
            modal.find('.modal-body #d_auto_5').val(button.data('expiry'));

        });

        function option() {

            loadPriceList();

        }

        function displayVals(data) {
            var val = data;
            var ajaxurl = '{{route('myitems')}}';
            $('#loading').show();
            $.ajax({
                url: ajaxurl,
                type: "get",
                dataType: "json",
                data: { val: val },
                success: function (data) {
                    document.getElementById("tbody1").style.display = 'none';
                    document.getElementById("tbody").style.display = 'block';
                    bindData(data);

                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        }

        var table = $('#fixed-header-price').DataTable({
            'columns': [
                { 'data': 'product_name' },
                {
                    'data': 'unit_cost', render: function (unit_cost) {
                        return formatMoney(unit_cost);
                    }
                },
                {
                    'data': 'selling_price', render: function (unit_cost) {
                        return formatMoney(unit_cost);
                    }
                },
                { 'data': 'price_category_name' },
                @if($batch_enabled === 'YES')
                    { 'data': 'batch_number' }
                @endif
                ]
        });

        function bindData(data) {
            tables.clear();
            tables.rows.add(data);
            tables.column(3).visible(false);
            tables.draw();

            $('#tbody').on('click', '#detail4', function () {
                var datas = tables.row($(this).parents('tr')).data();
                var e = document.getElementById("price_category");
                var value = e.options[e.selectedIndex].value;
                retrivePriceHistory(value, datas.product_id);
            });

        }

        function change() {
            $('.price-list').text('Price List');
            document.getElementById("tbody1").style.display = 'block';
            document.getElementById("tbody").style.display = 'none';

            $('input[type="search"]').val('').keyup();

        }

        function retrivePriceHistory(data, data1) {

            var ajaxurl = '{{route('price-history')}}';
            $('#loading').show();
            $.ajax({
                url: ajaxurl,
                type: "get",
                dataType: "json",
                data: { price_category_id: data, product_id: data1 },
                success: function (data) {
                    bindPriceData(data);

                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        }

        function bindPriceData(data) {
            table.clear();
            table.rows.add(data);
            table.draw();
            $('#test').modal('show');
        }

        $('#unit_cost_edit_to_show').on('change', function () {
            var newValue = document.getElementById('unit_cost_edit_to_show').value;
            if (newValue !== '') {
                document.getElementById('unit_cost_edit_to_show').value =
                    numberWithCommas(parseFloat(newValue.replace(/\,/g, ''), 10));
                document.getElementById('unit_cost_edit').value = parseFloat(newValue.replace(/\,/g, ''), 10)
            } else {
                document.getElementById('unit_cost_edit_to_show').value = '';
            }

        });

        $('#sell_price_edit_to_show').on('change', function () {
            var newValue = document.getElementById('sell_price_edit_to_show').value;
            if (newValue !== '') {
                document.getElementById('sell_price_edit_to_show').value =
                    numberWithCommas(parseFloat(newValue.replace(/\,/g, ''), 10));
                document.getElementById('sell_price_edit').value = parseFloat(newValue.replace(/\,/g, ''), 10)
            } else {
                document.getElementById('sell_price_edit_to_show').value = '';
            }

        });

        function numberWithCommas(digit) {
            return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        $(document).ready(function () {

            function initPriceTable(selector) {
                return $(selector).DataTable({
                    responsive: true,
                    order: [[0, 'asc']],
                    destroy: true,
                    columns: [
                        {
                            data: "product_name", render: function (data, type, row) {
                                return `${row.product_name} ${row.brand ?? ''} ${row.pack_size ?? ''}${row.sales_uom ?? ''}`;
                            }
                        },
                        @if($batch_enabled === 'YES')
                            { data: "batch_number", render: data => data ?? '' },
                        @endif
                        { data: "unit_cost", render: data => formatMoney(data) },
                        { data: "price", render: data => formatMoney(data) },
                        { data: "profit", render: data => (data ? `${Math.round(data)}%` : '0%') },
                        @if(auth()->user()->checkPermission('Edit Price List'))
                                        {
                                data: "id", render: function (data, type, row) {
                                    return `
                                                        <button id='pricing' class='btn btn-sm btn-rounded btn-primary'
                                                            type='button' data-toggle="modal" data-target="#edit"
                                                            data-name='${row.product_name ?? ''}'
                                                            data-unit-cost='${row.unit_cost ?? ''}'
                                                            data-price='${row.price ?? ''}'
                                                            data-id='${row.id ?? ''}'
                                                            data-brand='${row.brand ?? ''}'
                                                            data-pack-size='${row.pack_size ?? ''}'
                                                            data-sales-uom='${row.sales_uom ?? ''}'
                                                            data-price-category-id='${row.price_category_id ?? ''}'>Edit</button>
                                                    `;
                                }
                            },
                        @endif
                        { data: "price_category_id", visible: false } // hidden but present
                    ]
                });
            }

            function initHistoryTable(selector) {
                return $(selector).DataTable({
                    responsive: true,
                    order: [[0, 'asc']],
                    destroy: true,
                    columns: [
                        {
                            data: "product_name", render: function (data, type, row) {
                                return `${row.product_name} ${row.brand ?? ''} ${row.pack_size ?? ''}${row.sales_uom ?? ''}`;
                            }
                        },
                        @if($batch_enabled === 'YES')
                            { data: "batch_number", render: data => data ?? '' },
                        @endif
                        { data: "unit_cost", render: data => formatMoney(data) },
                        { data: "price", render: data => formatMoney(data) },
                        // { data: "purchased_at", render: data => data ? data.split(' ')[0] : '' },
                        // { data: "updated_at", render: data => data ? data.split(' ')[0] : '' }, 
                        { data: "price_category_id", visible: false }
                    ]
                });
            }

            const currentStocks = initPriceTable('#fixed-header2');
            const pendingPrices = initPriceTable('#pendingPrices');
            const priceHistory = initHistoryTable('#priceHistory');

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                var categoryId = $('#price_category').val();
                var typeId = $('#type_id').val();

                var api = new $.fn.dataTable.Api(settings);
                var rowData = api.row(dataIndex).data();
                var rowCategory = rowData ? rowData.price_category_id : null;

                var rowCatStr = rowCategory === null || typeof rowCategory === 'undefined' ? '' : String(rowCategory);
                var catStr = categoryId === null || typeof categoryId === 'undefined' ? '' : String(categoryId);

                if (typeId === "pending") {
                    return rowCatStr !== catStr;
                } else if (typeId === "1") {
                    return rowCatStr === catStr;
                } else if (typeId === "0") {
                    return rowCatStr === catStr || rowCatStr === '';
                }
                return true;
            });

            function renderTable(tableId, data) {
                var table = $(tableId).DataTable();
                table.clear();
                table.rows.add(data);
                table.draw();
            }

            $('#price_category, #type_id').on('change', function () {
                var selectedCategory = $('#price_category').val();
                var selectedType = $('#type_id').val();

                $('#pendingTable, #tbody1, #historyTable').hide();

                $.ajax({
                    url: '{{ route("fetch-price-list") }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        category_id: selectedCategory,
                        type: selectedType
                    },
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (response) {
                        console.log('FechResponse: ', response);

                        if (selectedType === "pending") {
                            renderTable('#pendingPrices', response);
                            $('#pendingTable').show();
                        } else if (selectedType === "1") {
                            renderTable('#fixed-header2', response);
                            $('#tbody1').show();
                        } else if (selectedType === "0") {
                            renderTable('#priceHistory', response);
                            $('#historyTable').show();
                        }
                    },
                    complete: function () {
                        $('#loading').hide();
                    },
                    error: function () {
                        alert('Error fetching data!');
                    }
                });
            });

            $('#price_category, #type_id').trigger('change');

        });

    </script>

@endpush