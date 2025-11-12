@extends("layouts.master")

@section('content-title')
    Sales Order
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Sales Order</a></li>
@endsection


@section('content')
    <style>
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
        @if(auth()->user()->checkPermission('View Sales Order'))
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                @if(auth()->user()->checkPermission('View Sales Order'))
                    <li class="nav-item">
                        <a class="nav-link active" id="new-order" data-toggle="pill" href="{{ route('sale-quotes.index') }}"
                            role="tab" aria-controls="pills-home" aria-selected="true">New Order</a>
                    </li>
                @endif
                @if(auth()->user()->checkPermission('View Order List'))
                    <li class="nav-item">
                        <a class="nav-link" id="order-list" data-toggle="pill" href="{{ route('sale-quotes.order_list') }}"
                            role="tab" aria-controls="pills-profile" aria-selected="false">Order List</a>
                    </li>
                @endif
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <form id="quote_sale_form">
                        @if (auth()->user()->checkPermission('Manage Customers'))
                            <div class="row">
                                <div class="col-md-12">
                                    <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                                        data-toggle="modal" data-target="#create"> Add
                                        New Customer
                                    </button>
                                </div>

                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                                    data-toggle="modal" data-target="#create"> Add
                                    New Customer
                                </button>
                            </div>

                        </div>
                        @csrf()
                        <input type="hidden" name="" id="is_all_store" value="{{ current_store()->name }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label id="cat_label">Sales Type<font color="red">*</font></label>
                                    <select id="price_category" class="js-example-basic-single form-control">
                                        <option value="" selected="true" disabled>Select Type</option>
                                        @foreach ($price_category as $price)
                                            <!-- <option value="{{ $price->id }}">{{ $price->name }}</option> -->
                                            <option value="{{ $price->id }}" {{ $default_sale_type === $price->id ? 'selected' : '' }}>{{ $price->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <input type="text" id="quote_barcode_input" autocomplete="off"
                                style="position:absolute; left:-9999px;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Products<font color="red">*</font></label>
                                    <select id="products" class="form-control">
                                        <option value="" disabled selected style="display:none;">Select Product</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">Customer Name <font color="red">*</font></label>
                                    <select id="customer_id" name="customer_id" class="js-example-basic-single form-control"
                                        required>
                                        <option value="" disabled selected="true">Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="detail">
                            <hr>
                            <div class="table-responsive" style="width: 100%;">
                                <table id="cart_table" class="table nowrap table-striped table-hover pl-3 pr-3" width="100%">
                                </table>
                            </div>

                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                @if ($enable_discount === 'YES')
                                    <div style="width: 99%">
                                        <label>Discount</label>
                                        <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                            value="0.00" />
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4">

                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <label class="col-md-6 col-form-label text-md-right"><b>Sub Total:</b></label>
                                    <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                        <input type="text" id="sub_total" class="form-control-plaintext text-md-right" readonly
                                            value="0.00" />
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                    <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                        <input type="text" id="total_vat" class="form-control-plaintext text-md-right" readonly
                                            value="0.00" />
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-md-6 col-form-label text-md-right"><b>Total
                                            Amount:</b></label>
                                    <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                        <input type="text" id="total" class="form-control-plaintext text-md-right" readonly
                                            value="0.00" />
                                    </div>
                                    <span class="help-inline text text-danger" style="display: none; margin-left: 63%"
                                        id="discount_error">Invalid Amount</span>
                                </div>
                            </div>


                            <input type="hidden" value="{{ $vat }}" id="vat">
                            <input type="hidden" value="0.00" id="sale_paid">
                            <input type="hidden" value="Yes" id="quotes_page">
                            <input type="hidden" value="0.00" id="change_amount">
                            <input type="hidden" id="price_cat" name="price_category_id">
                            <input type="hidden" id="discount_value" name="discount_amount">
                            <input type="hidden" id="order_cart" name="cart">
                            <input type="hidden" value="{{$fixed_price}}" id="fixed_price">

                            <input type="hidden" value="" id="category">
                            <input type="hidden" value="" id="customers">
                            <input type="hidden" value="" id="print">
                            <input type="hidden" value="{{ $enable_discount }}" id="enable_discount">

                        </div>
                        {{--
                        <hr> --}}
                        <div class="row" hidden>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea name="remark" id="remark" class="form-control"></textarea>
                                </div>

                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-md-6 d-flex">
                                <div>
                                    <b>Total Items:</b>
                                    <span id="total_items">0</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group" style="float: right;">
                                    <button type="button" class="btn btn-danger" id="deselect-all-quote">Cancel</button>
                                    <button id="save_btn" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

                    <div class="d-flex justify-content-end mb-2 align-items-center">
                        <label class="mr-2" for="">Date:</label>
                        <input type="text" id="date_range" class="form-control w-auto" onchange="getQuotes()">
                    </div>
                    <div class="table-responsive">
                        <table id="quote_table" class="table table-striped table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        @endif
        <!-- ajax loading gif -->
        <div id="loading">
            <img id="loading-image" src="{{asset('assets/images/spinner.gif')}}" />
        </div>
        @if(!Auth::user()->checkPermission('View Sales Order'))
            <div class="" style="background-color: #fff; min-height: 80px; ">
                <div class="tab-pane fade show" id="credit-sale-receiving" role="tabpanel" aria-labelledby="credit_sales-tab">
                    <div class="row" style="padding: 10px 0px 0px 30px;">
                        <p>You do not have permission to View This Page</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @include('sales.sale_quotes.details')
    @include('sales.customers.create')
@endsection

@push('page_scripts')
    @include('partials.notification')
    <script type="text/javascript">
        $(document).ready(function () {
            loadProducts();
            setTimeout(function () { $('#quote_barcode_input').focus(); }, 150);
            // Global variables
            var cart = JSON.parse(localStorage.getItem('cart')) || [];
            var default_cart = JSON.parse(localStorage.getItem('default_cart')) || [];
            var order_cart = JSON.parse(localStorage.getItem('order_cart')) || [];
            var tax = parseFloat(document.getElementById('vat').value) || 0;
            var sale_discount = "0.00";
            var discount_enable = document.getElementById('enable_discount').value === 'YES';
            var cart_table;
            var edit_btn_set = 0; // For edit functionality
            var fixed_price = document.getElementById('fixed_price').value || 'NO';

            $(document).ready(function () {
                var initialValues = {
                    price_category: $("#price_category").val(),
                    product_id: $("#product_id").val(),
                    customer_id: $("#customer_id").val(),
                };

                $("#price_category, #product_id, #customer_id").on("change", function () {
                    var check_store = $("#is_all_store").val();
                    var id = $(this).attr("id");

                    if (check_store === "ALL") {
                        notify(
                            "You can't sell in ALL branches. Please switch to a specific branch to proceed",
                            "top",
                            "right",
                            "warning"
                        );

                        $(this).val(initialValues[id]).trigger("change.select2");
                    } else {
                        initialValues[id] = $(this).val();
                    }
                });
            });

            // Initialize DataTable for cart
            try {
                cart_table = $('#cart_table').DataTable({
                    searching: false,
                    paging: false,
                    info: false,
                    ordering: false,
                    data: cart,
                    columns: [
                        { title: "Product Name" },
                        { title: "Quantity" },
                        { title: "Price" },
                        { title: "VAT" },
                        { title: "Amount" },
                        { title: "Stock Qty", visible: false },
                        { title: "productID", visible: false },
                        { title: "Product Type", visible: false },
                        {
                            title: "Action",
                            defaultContent: "<div><input type='button' value='Edit' id='edit_btn' class='btn btn-info btn-rounded btn-sm'/><input type='button' value='Delete' id='delete_btn' class='btn btn-danger btn-rounded btn-sm'/></div>"
                        }
                    ]
                });
                // console.log('Cart table initialized successfully');
            } catch (error) {
                // console.error('Error initializing cart table:', error);
            }

            // Discount calculation function
            function discount() {
                var sub_total = 0;
                var total_vat = 0;
                var total = 0;

                // Calculate totals from cart
                cart.forEach(function (item) {
                    var quantity = item[1];
                    var price = parseFloat(item[2].replace(/\,/g, ''));
                    var vat = parseFloat(item[3].replace(/\,/g, ''));
                    var amount = parseFloat(item[4].replace(/\,/g, ''));

                    // Handle quantity with "Max" indicator
                    if (typeof quantity === 'string' && quantity.includes('Max')) {
                        quantity = parseFloat(quantity.split(' ')[0].replace(/,/g, ''));
                    } else {
                        quantity = parseFloat(quantity.toString().replace(/,/g, ''));
                    }

                    sub_total += (price * quantity);
                    total_vat += vat;
                    total += (amount);
                });

                // Apply discount if enabled
                if (discount_enable) {
                    var discount_amount = parseFloat(document.getElementById('sale_discount').value) || 0;
                    sale_discount = discount_amount;
                    total = total - discount_amount;
                }

                // Update display
                document.getElementById('sub_total').value = formatMoney(sub_total);
                document.getElementById('total_vat').value = formatMoney(total_vat);
                document.getElementById('sale_discount').value = formatMoney(discount_amount);
                document.getElementById('total').value = formatMoney(total);
                document.getElementById('total_items').innerHTML = cart.length;

                // Update cart table
                if (cart_table) {
                    cart_table.clear().rows.add(cart).draw();
                }
            }

            // Format money function
            function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
                try {
                    decimalCount = Math.abs(decimalCount);
                    decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
                    const negativeSign = amount < 0 ? "-" : "";
                    let i = parseInt(
                        (amount = Math.abs(Number(amount) || 0).toFixed(decimalCount))
                    ).toString();
                    let j = i.length > 3 ? i.length % 3 : 0;
                    return (
                        negativeSign +
                        (j ? i.substr(0, j) + thousands : "") +
                        i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) +
                        (decimalCount
                            ? decimal +
                            Math.abs(amount - i)
                                .toFixed(decimalCount)
                                .slice(2)
                            : "")
                    );
                } catch (e) { }
            }

            // Number formatting function
            function numberWithCommas(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // Edit button functionality
            $('#cart_table tbody').on('click', '#edit_btn', function () {
                var quantity;
                let price;
                if (edit_btn_set === 0) {
                    var row_data = cart_table.row($(this).parents('tr')).data();
                    var index = cart_table.row($(this).parents('tr')).index();
                    quantity = row_data[1].toString().replace(',', '');
                    price = row_data[2];
                    row_data[1] = "<input style='width: 80%' type='text' min='1' class='form-control' id='edit_quantity' required onkeypress='return isNumberKey(event,this)'>";

                    if (fixed_price === "NO") {
                        row_data[2] = "<input style='width: 130%; margin-left: -10%' type='text' class='form-control' id='edit_price' required onkeypress='return isNumberKey(event,this)'>";
                    }

                    cart[index] = row_data;
                    cart_table.clear();
                    cart_table.rows.add(cart);
                    cart_table.draw();

                    // Save cart to localStorage for persistence on page reload
                    localStorage.setItem('cart', JSON.stringify(cart));
                    localStorage.setItem('default_cart', JSON.stringify(default_cart));
                    localStorage.setItem('order_cart', JSON.stringify(order_cart));

                    var quantity_ = quantity.split('<');
                    document.getElementById("edit_quantity").value = quantity_[0];

                    if (fixed_price === "NO") {
                        document.getElementById("edit_price").value = price.replace(/,/g, '');
                    }

                    edit_btn_set = 1;
                } else {
                    $('#edit_quantity').change();
                    if (fixed_price === "NO") {
                        $('#edit_price').change();
                    }
                }
            });

            // Edit quantity change handler
            $('#cart_table tbody').on('change', '#edit_quantity', function () {
                edit_btn_set = 0;
                var row_data = cart_table.row($(this).parents('tr')).data();
                var index = cart_table.row($(this).parents('tr')).index();

                if (document.getElementById("edit_quantity").value === '' || document.getElementById("edit_quantity").value === '0') {
                    edit_btn_set = 1;
                    if (typeof notify === 'function') {
                        notify('Quantity is required', 'top', 'right', 'warning');
                    } else {
                        alert('Quantity is required');
                    }
                    setTimeout(function () { $('#quote_barcode_input').focus(); }, 150);
                    return false;
                }

                // Calculate VAT and total
                var vat;
                var unit_total;
                let vat_money;
                if (fixed_price === "NO") {
                    var editPrice = parseFloat(document.getElementById("edit_price").value.replace(/\,/g, ''), 10);
                    vat = Number((editPrice * tax).toFixed(2));
                    unit_total = formatMoney(editPrice + vat);
                    vat_money = formatMoney(vat);
                } else {
                    var originalPrice = parseFloat(row_data[2].replace(/\,/g, ''), 10);
                    vat = Number((originalPrice * tax).toFixed(2));
                    unit_total = formatMoney(originalPrice + vat);
                    vat_money = formatMoney(vat);
                }

                row_data[1] = numberWithCommas(document.getElementById("edit_quantity").value);

                if (fixed_price === "NO") {
                    row_data[2] = formatMoney(parseFloat(document.getElementById("edit_price").value.replace(/\,/g, ''), 10));
                }

                // For quotes page, no maximum quantity restriction
                if ($('#quotes_page').length) {
                    row_data[2] = formatMoney(parseFloat(row_data[2].replace(/\,/g, ''), 10));
                    row_data[3] = formatMoney(parseFloat(vat_money.replace(/\,/g, ''), 10) * row_data[1].toString().replace(',', ''));
                    row_data[4] = formatMoney(parseFloat(unit_total.replace(/\,/g, ''), 10) * row_data[1].toString().replace(',', ''));
                } else {
                    // Check stock quantity
                    var dif = row_data[5] - row_data[1].toString().replace(/,/g, '');
                    if (dif < 0) {
                        row_data[1] = row_data[5];
                        row_data[2] = formatMoney(parseFloat(row_data[2].replace(/\,/g, ''), 10));
                        row_data[3] = formatMoney(parseFloat(vat_money.replace(/\,/g, ''), 10) * row_data[5]);
                        row_data[4] = formatMoney(parseFloat(unit_total.replace(/\,/g, ''), 10) * row_data[5]);
                        row_data[1] = numberWithCommas(row_data[5]) + " " + "<span class='text text-danger'>Max</span>";
                    } else {
                        row_data[2] = formatMoney(parseFloat(row_data[2].replace(/\,/g, ''), 10));
                        row_data[3] = formatMoney(parseFloat(vat_money.replace(/\,/g, ''), 10) * row_data[1].toString().replace(',', ''));
                        row_data[4] = formatMoney(parseFloat(unit_total.replace(/\,/g, ''), 10) * row_data[1].toString().replace(',', ''));
                    }
                }

                cart[index] = row_data;
                discount();
                setTimeout(function () { $('#quote_barcode_input').focus(); }, 150);
            });

            // Edit price change handler (if fixed price is NO)
            if (fixed_price === "NO") {
                $('#cart_table tbody').on('change', '#edit_price', function () {
                    edit_btn_set = 0;
                    var row_data = cart_table.row($(this).parents('tr')).data();
                    var index = cart_table.row($(this).parents('tr')).index();

                    if (document.getElementById("edit_price").value === '') {
                        edit_btn_set = 1;
                        if (typeof notify === 'function') {
                            notify('Price is required', 'top', 'right', 'warning');
                        } else {
                            alert('Price is required');
                        }
                        return false;
                    }

                    // Update the default cart with new price
                    default_cart[index][0] = formatMoney(parseFloat(document.getElementById("edit_price").value.replace(/\,/g, ''), 10));
                    default_cart[index][1] = formatMoney(parseFloat(document.getElementById("edit_price").value.replace(/\,/g, ''), 10) * tax);
                    default_cart[index][2] = formatMoney(parseFloat(document.getElementById("edit_price").value.replace(/\,/g, ''), 10) * (1 + tax));

                    // Trigger quantity change to recalculate
                    $('#edit_quantity').change();
                });
            }

            // Delete button functionality
            $('#cart_table tbody').on('click', '#delete_btn', function () {
                edit_btn_set = 0;
                var index = cart_table.row($(this).parents('tr')).index();
                cart.splice(index, 1);
                default_cart.splice(index, 1);
                discount();
                setTimeout(function () { $('#quote_barcode_input').focus(); }, 150);
            });

            // Helper function for number validation
            window.isNumberKey = function (evt, element) {
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 44) {
                    if (evt.preventDefault) {
                        evt.preventDefault();
                    }
                    return false;
                }
                return true;
            };

            // Function to add product to cart
            function addProductToCart(productData) {
                let customer_id = document.getElementById("customer_id").value;

                if (!customer_id) {
                    notify('Please select customer first', 'top', 'right', 'warning');
                    $(this).val(null).trigger('change.select2');
                    return;
                }
                if (!productData) return;

                $("#edit_quantity").change();
                $("#edit_price").change();

                var sel = document.getElementById("products");
                var productValue = sel.value;
                if (!productValue) return;

                var selectedOption = sel.options[sel.selectedIndex];
                var name = selectedOption.getAttribute("data-name") || selectedOption.text;
                var available_quantity = Number(
                    selectedOption.getAttribute("data-quantity") || 0
                );
                var productID = productValue;

                // Check if the item already exist in cart
                let idx = cart.findIndex((r) => r[6] == productID);

                if (idx !== -1) {
                    var price = parseFloat(cart[idx][2].replace(/,/g, ''));
                    // Unit calcs
                    var vatUnit = Number((price * tax).toFixed(2));
                    var unitTotal = Number(price + vatUnit);
                    // If exist then add qty and move it on top.
                    let row = cart[idx];

                    let rawQty =
                        typeof row[1] === "number" ? row[1] : String(row[1]).split("<")[0];
                    rawQty = Number(String(rawQty).replace(/,/g, "")) || 0;

                    let newQty = rawQty + 1;
                    if (newQty > available_quantity) {
                        row[1] = numberWithCommas(rawQty) +
                            "<span class='text text-danger'> Max</span>";
                    } else {
                        row[1] = numberWithCommas(newQty);
                    }

                    row[2] = formatMoney(price);
                    row[3] = formatMoney(vatUnit * newQty);
                    row[4] = formatMoney(unitTotal * newQty);
                    row[5] = available_quantity;
                    row[6] = productID;

                    // take on top of the cart
                    cart.splice(idx, 1);
                    cart.unshift(row);

                    if (default_cart && default_cart.length) {
                        const dc = default_cart.splice(idx, 1)[0];
                        default_cart.unshift(dc);
                    }
                } else {
                    var price = Number(selectedOption.getAttribute("data-price") || 0);
                    // Unit calcs
                    var vatUnit = Number((price * tax).toFixed(2));
                    var unitTotal = Number(price + vatUnit);
                    var item = [
                        name,
                        1,
                        formatMoney(price),
                        formatMoney(vatUnit),
                        formatMoney(unitTotal),
                        available_quantity,
                        productID,
                        "",
                    ];
                    cart.unshift(item);

                    var cart_data = [
                        formatMoney(price),
                        formatMoney(vatUnit),
                        formatMoney(unitTotal),
                    ];
                    default_cart.unshift(cart_data);
                }

                discount();

                $("#products").val(null).trigger("change");
                $("#quote_barcode_input").focus();
            }

            // Function to load products based on price category
            function loadProducts() {
                var priceCategory = $('#price_category').val();

                $.ajax({
                    url: '{{ route("selectProducts") }}',
                    type: 'POST',
                    data: {
                        id: priceCategory,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        // console.log('Products loaded:', data);

                        // Clear existing options
                        $('#products').empty();
                        response.data.forEach(function (p) {
                            $("#products").append(
                                $("<option>", {
                                    value: "",
                                    text: "Select product",
                                }),
                                $("<option>", {
                                    value: p.id,
                                    text: p.name,
                                    "data-name": p.name,
                                    "data-price": p.price,
                                    "data-quantity": p.quantity,
                                })
                            );
                        });

                        // Refresh select2
                        $('#products').trigger('change');
                    },
                    error: function (xhr, status, error) {
                        // console.error('Error loading products:', xhr.responseText);
                        $('#products').empty().append('<option value="">Error loading products</option>');
                    }
                });
            }

            $("#quote_barcode_input").on("keypress", function (e) {
                if (e.which === 13) {
                    e.preventDefault();
                    let barcode = $(this).val().trim();
                    console.log("Barcode", barcode);
                    if (barcode !== "") {
                        fetchProductByBarcode(barcode);
                        $(this).val("");
                    }
                }
            });

            function fetchProductByBarcode(barcode) {
                var price_category = $("#price_category").val();

                $.ajax({
                    url: "{{ route('filter-product-by-word')}}",
                    method: "GET",
                    data: {
                        word: barcode,
                        price_category_id: price_category,
                    },
                    dataType: "json",
                    success: function (res) {
                        console.log("Res Data:", res);

                        // ensure array + at least one result
                        if (res && Array.isArray(res.data) && res.data.length > 0) {
                            const prod = res.data[0];

                            // Build normalized product object expected by addProductToCartScan
                            const product = {
                                id: prod.id,
                                // server returns a combined name string already from your controller
                                name: prod.name || prod.product_name || "",
                                // ensure price is number
                                price: Number(prod.price) || parseFloat(prod.price) || 0,
                                // quantity field from server might be "quantity" (sum) or "stock_qty"
                                quantity: Number(prod.quantity) || Number(prod.stock_qty) || 0,
                                stock_qty: Number(prod.quantity) || Number(prod.stock_qty) || 0,
                                // optional
                                type: prod.type || "",
                            };

                            // For credit sales require customer selected
                            let customer_id = $("#customer_id").val();
                            if (!customer_id || customer_id === "") {
                                notify("Please select customer first", "top", "right", "warning");
                                return;
                            }

                            addProductToCartScan(product);
                        } else {
                            notify("Product not found", "top", "right", "danger");
                        }
                    },
                    error: function (err) {
                        console.error("Error fetching product by barcode", err);
                        notify("Error fetching product", "top", "right", "danger");
                    },
                });
            }
            // ===== addProductToCartScan =====
            function addProductToCartScan(product) {
                // console.log("Item Receive", product);

                // Normalize numeric fields
                const priceNum = Number(product.price) || 0;
                const stockQty = Number(product.quantity || product.stock_qty || 0);
                const vatUnit = Number((priceNum * tax).toFixed(2));
                const unitTotal = Number(priceNum + vatUnit);

                // Find existing product in cart by productId (index 6)
                let idx = cart.findIndex((r) => String(r[6]) == String(product.id));

                if (idx !== -1) {
                    // Existing row -> increment quantity numerically and recalc totals
                    let row = cart[idx];

                    // Normalize existing qty to number (strip commas and any "< Max" HTML)
                    let existingQtyRaw = String(row[1] || "0").split("<")[0];
                    // let existingPriceRaw = String(row[2] || "0").split("<")[0];
                    let existingVatRaw = String(row[2] || "0").split("<")[0];
                    let existingPriceRaw = String(row[2] || "0").split("<")[0];
                    let existingQty = Number(existingQtyRaw.replace(/\,/g, "")) || 0;
                    let existingPrice = Number(existingPriceRaw.replace(/\,/g, "")) || 0;
                    let newVat = Number((existingPrice * tax).toFixed(2)) || 0;
                    let newTotal = Number(existingPrice + newVat);

                    // Incoming increment (scanner adds 1 each time)
                    let incomingQty = 1;

                    let newQty = existingQty + incomingQty;

                    // Check stock limit (if not quotes page)
                    if (!$("#quotes_page").length && stockQty && newQty > stockQty) {
                        // set to max and show Max label
                        row[1] =
                            numberWithCommas(stockQty) +
                            " <span class='text text-danger'>Max</span>";
                        // use stockQty for calculations
                        row[2] = formatMoney(existingPrice);
                        row[3] = formatMoney(newVat * stockQty);
                        row[4] = formatMoney(newTotal * stockQty);
                    } else {
                        row[1] = numberWithCommas(newQty);
                        row[2] = formatMoney(existingPrice);
                        row[3] = formatMoney(newVat * newQty);
                        row[4] = formatMoney(newTotal * newQty);
                    }

                    row[5] = stockQty;
                    row[6] = product.id;
                    row[7] = product.type || "";

                    // Move to top
                    cart.splice(idx, 1);
                    cart.unshift(row);

                    // Keep default_cart in sync (move matching entry to top if exists)
                    if (default_cart && default_cart.length && default_cart[idx]) {
                        const dc = default_cart.splice(idx, 1)[0];
                        default_cart.unshift(dc);
                    }
                } else {
                    // New item: create array-format row to match existing code
                    var item = [
                        product.name,
                        1,
                        formatMoney(priceNum),
                        formatMoney(vatUnit),
                        formatMoney(unitTotal),
                        stockQty,
                        product.id,
                        product.type || "",
                    ];

                    var cart_data = [
                        formatMoney(priceNum),
                        formatMoney(vatUnit),
                        formatMoney(unitTotal),
                    ];

                    default_cart.unshift(cart_data);
                    cart.unshift(item);
                }

                // Recalculate totals (uses your discount() which expects array-based cart)
                if (typeof discount === "function") {
                    discount();
                } else {
                    // minimal fallback (shouldn't be needed if discount exists)
                    cart_table.clear();
                    cart_table.rows.add(cart);
                    cart_table.draw();
                }

                // redraw UI
                cart_table.clear();
                cart_table.rows.add(cart);
                cart_table.draw();
            }

            // Product selection event handler
            $("#products").on('change', function (event) {
                let selectedProduct = $(this).val();

                if (selectedProduct && selectedProduct !== '') {
                    // console.log('Adding product to cart');
                    addProductToCart(selectedProduct);
                    // Clear the selection after adding to cart
                    setTimeout(() => {
                        $(this).val('').trigger('change');
                    }, 100);
                    setTimeout(function () { $('#quote_barcode_input').focus(); }, 150);
                    // Make sales type readonly after starting to add items
                    $('#price_category').prop('disabled', true);
                }
            });

            // Function to clear cart
            function deselectQuote() {
                edit_btn_set = 0;
                cart = [];
                default_cart = [];
                order_cart = [];
                if (cart_table) {
                    cart_table.clear().draw();
                }
                discount(); // Update totals
                document.getElementById('total_items').innerHTML = 0;
                document.getElementById("sale_discount").value = "0.00";
                document.getElementById("total").value = "0.00";
                // Clear localStorage to prevent cart persistence on page reload
                localStorage.removeItem('cart');
                localStorage.removeItem('default_cart');
                localStorage.removeItem('order_cart');
                // console.log('Cart cleared');
            }

            // Initialize customer select2
            $('#customer_id').select2({
                placeholder: 'Select Customer',
                allowClear: false
            });

            // Initialize price category select2
            $('#price_category').select2({
                placeholder: 'Select Sales Type',
                allowClear: false
            });

            // Initialize products select2
            $('#products').select2({
                placeholder: 'Select Product',
                allowClear: false,
                data: []
            });

            $('#price_category').on('change', function () {
                var selectedCategory = $(this).val();
                // console.log('Price category changed to:', selectedCategory);
                loadProducts();
                // Make sales type readonly after starting to add items
                if (cart.length > 0) {
                    $(this).prop('disabled', true);
                }
            });

            $('#customer_id').on('change', function () {
                setTimeout(function () { $('#quote_barcode_input').focus(); }, 150);
                if ($('#price_category').val()) {
                    loadProducts();
                }
            });

            // Clear cart button
            $('#deselect-all-quote').on('click', function () {
                var cart_data = document.getElementById("order_cart").value;
                if (!(cart_data === '' || cart_data === 'undefined')) {
                    var r = confirm('Cancel quote?');
                    if (r === true) {
                        deselectQuote();
                        // Re-enable sales type when cart is cleared
                        $('#price_category').prop('disabled', false);
                    } else {
                        return false;
                    }
                } else {
                    deselectQuote();
                    // Re-enable sales type when cart is cleared
                    $('#price_category').prop('disabled', false);
                }
            });

            $("#sale_discount").on("blur", function () {
                $("#quote_barcode_input").focus();
            });

            $("#remark").on("blur", function () {
                $("#quote_barcode_input").focus();
            });

            // Save button
            $('#save_btn').on('click', function (e) {
                e.preventDefault()
                $("#loading").show();
                saveQuoteForm();
            });

            // Save quote form function
            function saveQuoteForm() {
                var customer_id = $('#customer_id').val();
                var price_category = $('#price_category').val();
                var remark = $('#remark').val();

                if (!customer_id) {
                    if (typeof notify === 'function') {
                        notify('Please select a customer', 'top', 'right', 'warning');
                        $("#loading").hide();
                    } else {
                        alert('Please select a customer');
                    }
                    return;
                }

                if (!price_category) {
                    if (typeof notify === 'function') {
                        notify('Please select sales type', 'top', 'right', 'warning');
                        $("#loading").hide();
                    } else {
                        alert('Please select sales type');
                    }
                    return;
                }

                if (cart.length == 0) {
                    if (typeof notify === 'function') {
                        notify('Please add at least one product to the cart', 'top', 'right', 'warning');
                        $("#loading").hide();
                    } else {
                        alert('Please add at least one product to the cart');
                    }
                    return;
                }

                // Prepare order cart data
                order_cart = [];
                cart.forEach(function (item) {
                    var quantity = item[1];
                    // Handle quantity with "Max" indicator
                    if (typeof quantity === 'string' && quantity.includes('Max')) {
                        quantity = quantity.split(' ')[0].replace(/,/g, '');
                    }

                    var product = {
                        product_id: item[6],
                        quantity: quantity,
                        price: parseFloat(item[2].replace(/\,/g, '')),
                        vat: parseFloat(item[3].replace(/\,/g, '')),
                        amount: parseFloat(item[4].replace(/\,/g, ''))
                    };
                    order_cart.push(product);
                });

                // Update hidden fields
                document.getElementById("order_cart").value = JSON.stringify(order_cart);
                document.getElementById("price_cat").value = price_category;
                document.getElementById("discount_value").value = sale_discount;

                var formData = {
                    customer_id: customer_id,
                    price_category_id: price_category,
                    cart: JSON.stringify(order_cart),
                    discount_amount: parseFloat(sale_discount) || 0,
                    remark: remark,
                    _token: '{{ csrf_token() }}'
                };

                // console.log('Saving quote with data:', formData);

                // Disable save button
                $('#save_btn').prop('disabled', true).text('Saving...');

                $.ajax({
                    url: "{{ route('storeQuote') }}",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        // console.log('Quote saved successfully:', response);

                        if (typeof notify === 'function') {
                            notify('Order saved successfully!', 'top', 'right', 'success');
                        } else {
                            alert('Order saved successfully!');
                        }

                        // Clear form
                        deselectQuote();
                        $('#customer_id').val(null).trigger('change.select2');
                        $('#price_category').prop('disabled', false);
                        $('#remark').val('');

                        // Re-enable save button
                        $('#save_btn').prop('disabled', false).text('Save');
                        $("#loading").hide();
                    },
                    error: function (xhr, status, error) {
                        // console.error('Error saving quote:', xhr.responseText);

                        if (typeof notify === 'function') {
                            notify('Error saving quote: ' + xhr.responseText, 'top', 'right', 'error');
                        } else {
                            alert('Error saving quote: ' + xhr.responseText);
                        }

                        // Re-enable save button
                        $('#save_btn').prop('disabled', false).text('Save');
                        $("#loading").hide();
                    }
                });
            }

            // Make discount function available globally for the onchange event
            window.discount = discount;

            // Tab navigation handlers
            $('#new-order').on('click', function (e) {
                e.preventDefault();
                var redirectUrl = $(this).attr('href');
                window.location.href = redirectUrl;
            });

            $('#order-list').on('click', function (e) {
                e.preventDefault();
                var redirectUrl = $(this).attr('href');
                window.location.href = redirectUrl;
            });
        });
    </script>
    <script src="{{ asset('assets/apotek/js/notification.js') }}"></script>
    <script src="{{ asset('assets/apotek/js/customer.js') }}"></script>
@endpush