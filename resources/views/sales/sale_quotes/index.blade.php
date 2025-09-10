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
    </style>
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="new-order" data-toggle="pill" href="{{ route('sale-quotes.index') }}"
                    role="tab" aria-controls="pills-home" aria-selected="true">New Order</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="order-list" data-toggle="pill" href="{{ route('sale-quotes.order_list') }}"
                    role="tab" aria-controls="pills-profile" aria-selected="false">Order List</a>
            </li>
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
                    @csrf()
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label id="cat_label">Sales Type<font color="red">*</font></label>
                                <select id="price_category" class="js-example-basic-single form-control">
                                    <option value="">Select Sales Type</option>
                                    @foreach ($price_category as $price)
                                        <!-- <option value="{{ $price->id }}">{{ $price->name }}</option> -->
                                        <option value="{{ $price->id }}" {{ $default_sale_type === $price->id ? 'selected' : '' }}>{{ $price->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                            <table id="cart_table" class="table nowrap table-striped table-hover" width="100%"></table>
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
                        <input type="hidden" value="" id="fixed_price">

                        <input type="hidden" value="" id="category">
                        <input type="hidden" value="" id="customers">
                        <input type="hidden" value="" id="print">
                        <input type="hidden" value="{{ $enable_discount }}" id="enable_discount">

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remark" id="remark" class="form-control"></textarea>
                            </div>

                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-md-6"></div>
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
                                <th>Quote ID</th>
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
    </div>
    @include('sales.sale_quotes.details')
    @include('sales.customers.create')
@endsection

@push('page_scripts')
    @include('partials.notification')
    <script type="text/javascript">
        $(document).ready(function () {
            // Global variables
            var cart = [];
            var default_cart = [];
            var order_cart = [];
            var tax = parseFloat(document.getElementById('vat').value) || 0;
            var sale_discount = 0;
            var discount_enable = document.getElementById('enable_discount').value === 'YES';
            var cart_table;
            var edit_btn_set = 0; // For edit functionality
            var fixed_price = document.getElementById('fixed_price').value || 'NO';

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
                    total_vat += (vat * quantity);
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
                document.getElementById('total').value = formatMoney(total);

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
                // console.log('Product added to cart:', item);
            }

            // Function to load products based on price category
            function loadProducts() {
                var priceCategory = $('#price_category').val();
                if (!priceCategory) {
                    $('#products').empty().append('<option value="">Select Sales Type First</option>');
                    return;
                }

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

            // Product selection event handler
            $("#products").on('change', function (event) {
                let customer_id = document.getElementById("customer_id").value;
                let selectedProduct = $(this).val();

                // console.log('Product selected:', selectedProduct);
                // console.log('Customer ID:', customer_id);

                if (!customer_id) {
                    if (typeof notify === 'function') {
                        notify('Select Customer First', 'top', 'right', 'warning');
                    } else {
                        alert('Select Customer First');
                    }
                    $(this).val(null).trigger('change.select2');
                    return;
                }

                if (selectedProduct && selectedProduct !== '') {
                    // console.log('Adding product to cart');
                    addProductToCart(selectedProduct);
                    // Clear the selection after adding to cart
                    setTimeout(() => {
                        $(this).val('').trigger('change');
                    }, 100);
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
                // console.log('Cart cleared');
            }

            // Initialize customer select2
            $('#customer_id').select2({
                placeholder: 'Select Customer',
                allowClear: true
            });

            // Initialize price category select2
            $('#price_category').select2({
                placeholder: 'Select Sales Type',
                allowClear: true
            });

            // Initialize products select2
            $('#products').select2({
                placeholder: 'Select Product',
                allowClear: true,
                data: []
            });

            $('#price_category').on('change', function () {
                var selectedCategory = $(this).val();
                // console.log('Price category changed to:', selectedCategory);
                loadProducts();
            });

            $('#customer_id').on('change', function () {
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
                    } else {
                        return false;
                    }
                } else {
                    deselectQuote();
                }
            });

            // Save button
            $('#save_btn').on('click', function () {
                saveQuoteForm();
            });

            // Save quote form function
            function saveQuoteForm() {
                var customer_id = $('#customer_id').val();
                var price_category = $('#price_category').val();
                var remark = $('#remark').val();

                if (!customer_id) {
                    if (typeof notify === 'function') {
                        notify('Please select a customer', 'top', 'right', 'error');
                    } else {
                        alert('Please select a customer');
                    }
                    return;
                }

                if (!price_category) {
                    if (typeof notify === 'function') {
                        notify('Please select sales type', 'top', 'right', 'error');
                    } else {
                        alert('Please select sales type');
                    }
                    return;
                }

                if (cart.length === 0) {
                    if (typeof notify === 'function') {
                        notify('Please add at least one product to the cart', 'top', 'right', 'error');
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
                            notify('Quote saved successfully!', 'top', 'right', 'success');
                        } else {
                            alert('Quote saved successfully!');
                        }

                        // Clear form
                        deselectQuote();
                        $('#customer_id').val(null).trigger('change.select2');
                        $('#price_category').val('').trigger('change');
                        $('#remark').val('');

                        // Re-enable save button
                        $('#save_btn').prop('disabled', false).text('Save');
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