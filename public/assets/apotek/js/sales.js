var cart = []; //for data displayed.
var cart_data_desc = [];
var default_cart = []; //for default values.
var details = [];
var sale_items = [];
var edit_btn_set = 0;
var category_option = document.getElementById("category").value;
var customer_option = document.getElementById("customers").value;

var tax = Number(document.getElementById("vat").value);

// if (typeof fixed_price === 'undefined') {
let fixed_price;
let discount_enable;

try {
    fixed_price = document.getElementById("fixed_price").value;
} catch (e) {
    console.log("fixed_price_not_found");
}

try {
    discount_enable = document.getElementById("discount_enabled").value;
} catch (e) {
    console.log("discount error");
}

var items_table = $("#items_table").DataTable({
    searching: false,
    bPaginate: false,
    bInfo: false,
    data: sale_items,
    columns: [
        { title: "ID" },
        { title: "Product Name" },
        {
            title: "Quantity",
            render: function (data) {
                return numberWithCommas(data);
            },
        },
        {
            title: "Price",
            render: function (Price) {
                return formatMoney(Price);
            },
        },
        {
            title: "VAT",
            render: function (vat) {
                return formatMoney(vat);
            },
        },
        {
            title: "Discount",
            render: function (discount) {
                return formatMoney(discount);
            },
            visible: discount_enable === "YES",
        },
        {
            title: "Amount",
            render: function (amount) {
                return formatMoney(amount);
            },
        },
        {
            title: "Action",
            defaultContent:
                "<input type='button' value='Return' id='rtn_btn' class='btn btn-primary btn-rounded btn-sm'/>",
        },
    ],
});

var cart_table = $("#cart_table").DataTable({
    searching: false,
    bPaginate: false,
    bInfo: false,
    ordering: false,
    data: cart,
    columns: [
        { data: 0, title: "Product Name" },
        { data: 1, title: "Quantity" },
        { data: 2, title: "Price" },
        { data: 3, title: "VAT" },
        { data: 4, title: "Amount" },
        { data: 5, title: "Stock Qty", visible: false },
        { data: 6, title: "productID", visible: false },
        { data: 7, title: "Product Type", visible: false },
        {
            data: null,
            title: "Action",
            defaultContent:
                "<div><input type='button' value='Edit' id='edit_btn' class='btn btn-info btn-rounded btn-sm'/><input type='button' value='Delete' id='delete_btn' class='btn btn-danger btn-rounded btn-sm'/></div>",
        },
    ],
});

var details_table = $("#details_table").DataTable({
    searching: true,
    bPaginate: false,
    bInfo: true,
    data: details,
    columns: [
        { title: "Product Name" },
        { title: "Quantity" },
        { title: "price" },
        { title: "VAT" },
        { title: "Amount" },
    ],
});

var credit_payment_table = $("#credit_payment_table").DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    columns: [
        { data: "receipt_number" },
        { data: "name" },
        {
            data: "date",
            render: function (date) {
                return moment(date).format("YYYY-MM-DD");
            },
        },
        {
            data: "total_amount",
            render: function (total_amount) {
                return formatMoney(total_amount);
            },
        },
        {
            data: "paid_amount",
            render: function (paid_amount) {
                return formatMoney(paid_amount);
            },
        },
        {
            data: "balance",
            render: function (balance) {
                return formatMoney(balance);
            },
        },
        {
            data: "action",
            defaultContent:
                "<button type='button' id='pay_btn' class='btn btn-sm btn-rounded btn-primary'>Pay</button>",
        },
    ],
    aaSorting: [[2, "desc"]],
});

var sale_list_Table = $("#sale_list_Table").DataTable({
    order: [[1, "desc"]],
    dom: "t",
    bPaginate: false,
    bInfo: true,
    fixedHeader: true,
});

//Allows values to be arranged in descending order
function val() {
    $("#edit_quantity").change();

    /*set values to table*/
    var item = [];
    var cart_data = [];
    product = document.getElementById("products").value;
    document.getElementById("products").value = "";
    console.log(product);
    var selected_fields = product.split(",");
    var item_name = selected_fields[0];
    var price = Number(selected_fields[1]);
    var vat = Number((price * tax).toFixed(2));
    var unit_total = Number(price + vat);
    var quantity = 1;
    item.push(item_name);
    item.push(quantity);
    item.push(formatMoney(price));
    item.push(formatMoney(vat));
    item.push(formatMoney(unit_total));
    item.push(selected_fields[3]);
    item.push(selected_fields[2]);
    cart_data.push(formatMoney(price));
    cart_data.push(formatMoney(vat));
    cart_data.push(quantity);
    cart_data.push(formatMoney(unit_total));
    default_cart.push(cart_data);
    cart.unshift(item);
    discount();
    $("#barcode_input").focus();
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
}

$("#customer_id").on("change", function () {
    var check_store = $("#is_all_store").val();
    if (check_store === "ALL") {
        notify(
            "You can't sell in ALL branches. Please switch to a specific branch to proceed",
            "top",
            "right",
            "warning"
        );

        $("#products").val("").trigger("change.select2");
        $("#customer_id").val("").trigger("change.select2");
    }
    discount();
    setTimeout(function () {
        $("#barcode_input").focus();
    }, 30);
});

$("#customer_id").on("blur", function () {
    setTimeout(function () {
        $("#barcode_input").focus();
    }, 30);
});

$("#products").on("select2:select", function () {
    setTimeout(function () {
        $("#barcode_input").focus();
    }, 100);
});

$("#cash_sale_date").on("blur", function () {
    $("#barcode_input").focus();
});

$("#sale_discount").on("blur", function () {
    $("#barcode_input").focus();
});

$("#cart_table tbody").on("click", "#edit_btn", function () {
    var quantity;
    let price;
    if (edit_btn_set === 0) {
        var row_data = cart_table.row($(this).parents("tr")).data();

        var index = cart_table.row($(this).parents("tr")).index();
        quantity = row_data[1].toString().replace(",", "");
        price = row_data[2];
        row_data[1] =
            "<input style='width: 80%' type='text' min='1' class='form-control' id='edit_quantity' required  onkeypress='return isNumberKey(event,this)'>";

        if (fixed_price === "NO") {
            row_data[2] =
                "<input style='width: 130%; margin-left: -10%' type='text' class='form-control' id='edit_price' required  onkeypress='return isNumberKey(event,this)'>";
        }

        cart[index] = row_data;
        cart_table.clear();
        cart_table.rows.add(cart);
        cart_table.draw();

        var quantity_ = quantity.split("<");

        document.getElementById("edit_quantity").value = quantity_[0];

        if (fixed_price === "NO") {
            document.getElementById("edit_price").value = price;
        }

        edit_btn_set = 1;
    } else {
        // document.getElementById("edit_quantity").value
        $("#edit_quantity").change();
        if (fixed_price === "NO") {
            $("#edit_price").change();
        }
    }
});

$("#cart_table tbody").on("change", "#edit_quantity", function () {
    edit_btn_set = 0;
    var row_data = cart_table.row($(this).parents("tr")).data();
    var index = cart_table.row($(this).parents("tr")).index();
    if (
        document.getElementById("edit_quantity").value === "" ||
        document.getElementById("edit_quantity").value === "0"
    ) {
        edit_btn_set = 1;
        notify("Quantity is required", "top", "right", "warning");
        return false;
    }

    /*for vat*/
    var vat;
    var unit_total;
    let vat_money;
    if (fixed_price === "NO") {
        vat = Number(
            (
                parseFloat(
                    document
                        .getElementById("edit_price")
                        .value.replace(/\,/g, ""),
                    10
                ) * tax
            ).toFixed(2)
        );
        unit_total = formatMoney(
            parseFloat(
                document.getElementById("edit_price").value.replace(/\,/g, ""),
                10
            ) + vat
        );
        vat_money = formatMoney(vat);
    } else {
        vat = Number(
            (parseFloat(row_data[2].replace(/\,/g, ""), 10) * tax).toFixed(2)
        );
        unit_total = formatMoney(
            parseFloat(row_data[2].replace(/\,/g, ""), 10) + vat
        );
        vat_money = formatMoney(vat);
    }
    /*end for vat*/

    row_data[1] = numberWithCommas(
        document.getElementById("edit_quantity").value
    );

    if (fixed_price === "NO") {
        row_data[2] = formatMoney(
            parseFloat(
                document.getElementById("edit_price").value.replace(/\,/g, ""),
                10
            )
        );
    }

    // row_data[1] = Number((document.getElementById("edit_quantity").value));
    if (Number(parseFloat(row_data[1].replace(/\,/g, ""), 10)) < 0) {
        row_data[1] = 1;
    }

    if (row_data[7] == "consumable") {
        dif = 1;
    } else {
        dif = row_data[5] - row_data[1].toString().replace(/,/g, "");
    }

    if ($("#quotes_page").length) {
        //Qoutes has no maximum quantity
        row_data[2] = formatMoney(
            parseFloat(row_data[2].replace(/\,/g, ""), 10)
        );
        row_data[3] = formatMoney(
            parseFloat(vat_money.replace(/\,/g, ""), 10) *
                row_data[1].toString().replace(",", "")
        );
        row_data[4] = formatMoney(
            parseFloat(unit_total.replace(/\,/g, ""), 10) *
                row_data[1].toString().replace(",", "")
        );
    } else if (dif < 0) {
        row_data[1] = row_data[5];
        row_data[2] = formatMoney(
            parseFloat(row_data[2].replace(/\,/g, ""), 10)
        );
        row_data[3] = formatMoney(
            parseFloat(vat_money.replace(/\,/g, ""), 10) * row_data[5]
        );
        row_data[4] = formatMoney(
            parseFloat(unit_total.replace(/\,/g, ""), 10) * row_data[5]
        );
        row_data[1] =
            numberWithCommas(row_data[5]) +
            " " +
            "<span class='text text-danger'>Max</span>";
    } else {
        row_data[2] = formatMoney(
            parseFloat(row_data[2].replace(/\,/g, ""), 10)
        );
        row_data[3] = formatMoney(
            parseFloat(vat_money.replace(/\,/g, ""), 10) *
                row_data[1].toString().replace(",", "")
        );
        row_data[4] = formatMoney(
            parseFloat(unit_total.replace(/\,/g, ""), 10) *
                row_data[1].toString().replace(",", "")
        );
    } //replace the quantity with max stock qty available

    cart[index] = row_data;
    discount();
    $("#barcode_input").focus();
});

if (fixed_price === "NO") {
    $("#cart_table tbody").on("change", "#edit_price", function () {
        edit_btn_set = 0;
        var row_data = cart_table.row($(this).parents("tr")).data();
        var index = cart_table.row($(this).parents("tr")).index();

        if (document.getElementById("edit_price").value === "") {
            edit_btn_set = 1;
            notify("Price is required", "top", "right", "warning");
            return false;
        }

        /*for vat*/
        var vat = Number(
            (
                parseFloat(
                    document
                        .getElementById("edit_price")
                        .value.replace(/\,/g, ""),
                    10
                ) * tax
            ).toFixed(2)
        );
        var unit_total = formatMoney(
            parseFloat(
                document.getElementById("edit_price").value.replace(/\,/g, ""),
                10
            ) + vat
        );
        let vat_money = formatMoney(vat);
        /*end for vat*/

        row_data[1] = numberWithCommas(
            document.getElementById("edit_quantity").value
        );
        row_data[2] = formatMoney(
            parseFloat(
                document.getElementById("edit_price").value.replace(/\,/g, ""),
                10
            )
        );

        // row_data[1] = Number((document.getElementById("edit_quantity").value));
        if (Number(parseFloat(row_data[1].replace(/\,/g, ""), 10)) < 1) {
            row_data[1] = 1;
        }
        if (row_data[7] == "consumable") {
            dif = 1;
        } else {
            dif = row_data[5] - row_data[1].toString().replace(/,/g, "");
        }

        if ($("#quotes_page").length) {
            //Qoutes has no maximum quantity
            row_data[2] = formatMoney(
                parseFloat(row_data[2].replace(/\,/g, ""), 10)
            );
            row_data[3] = formatMoney(
                parseFloat(vat_money.replace(/\,/g, ""), 10) *
                    row_data[1].toString().replace(",", "")
            );
            row_data[4] = formatMoney(
                parseFloat(unit_total.replace(/\,/g, ""), 10) *
                    row_data[1].toString().replace(",", "")
            );
        } else if (dif < 0) {
            row_data[1] = row_data[5];
            row_data[2] = formatMoney(
                parseFloat(row_data[2].replace(/\,/g, ""), 10)
            );
            row_data[3] = formatMoney(
                parseFloat(vat_money.replace(/\,/g, ""), 10) * row_data[5]
            );
            row_data[4] = formatMoney(
                parseFloat(unit_total.replace(/\,/g, ""), 10) * row_data[5]
            );
            row_data[1] =
                numberWithCommas(row_data[5]) +
                " " +
                "<span class='text text-danger'>Max</span>";
        } else {
            row_data[2] = formatMoney(
                parseFloat(row_data[2].replace(/\,/g, ""), 10)
            );
            row_data[3] = formatMoney(
                parseFloat(vat_money.replace(/\,/g, ""), 10) *
                    row_data[1].toString().replace(",", "")
            );
            row_data[4] = formatMoney(
                parseFloat(unit_total.replace(/\,/g, ""), 10) *
                    row_data[1].toString().replace(",", "")
            );
        } //replace the quantity with max stock qty available

        cart[index] = row_data;
        discount();
        $("#barcode_input").focus();
    });
}

$("#cart_table tbody").on("click", "#delete_btn", function () {
    edit_btn_set = 0;
    var index = cart_table.row($(this).parents("tr")).index();
    var price = parseFloat(cart[index][2].replace(/\,/g, ""), 10);
    var unit_total = parseFloat(cart[index][4].replace(/\,/g, ""), 10);
    cart.splice(index, 1);
    default_cart.splice(index, 1);
    discount();
    $("#barcode_input").focus();
});

$("#deselect-all").on("click", function () {
    edit_btn_set = 0;
    var cart_data = document.getElementById("order_cart").value;
    if (!(cart_data === "" || cart_data === "undefined")) {
        var r = confirm("Cancel sale?");
        if (r === true) {
            /*continue*/
            deselect();
        } else {
            /*return false*/
            return false;
        }
    }

    $("#barcode_input").focus();
});

$("#deselect-all-credit-sale").on("click", function () {
    edit_btn_set = 0;
    var cart_data = document.getElementById("order_cart").value;
    if (!(cart_data === "" || cart_data === "undefined")) {
        var r = confirm("Cancel credit sale?");
        if (r === true) {
            /*continue*/
            deselect1();
        } else {
            /*return false*/
            return false;
        }
    }

    $("#barcode_input").focus();
});

$("#deselect-all-quote").on("click", function () {
    edit_btn_set = 0;
    var cart_data = document.getElementById("order_cart").value;
    if (!(cart_data === "" || cart_data === "undefined")) {
        var r = confirm("Cancel sale quote?");
        if (r === true) {
            /*continue*/
            deselectQuote();
        } else {
            /*return false*/
            return false;
        }
    }

    $("#barcode_input").focus();
});

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
    } catch (e) {}
}

function discount() {
    if (discount_enable === "YES") {
        var dis = document.getElementById("sale_discount").value;
        sale_discount = parseFloat(dis.replace(/\,/g, ""), 10) || 0;
    } else {
        sale_discount = 0;
    }

    var sub_total,
        total_vat,
        total = 0;
    var total_items = cart.length;
    if (cart[0]) {
        var result = [];
        var order_cart = []; //for data sent into database.
        cart.reduce(function (reducedCart, value) {
            if (!reducedCart[value[6]]) {
                reducedCart[value[6]] = value;
                result.push(reducedCart[value[6]]);
            } else {
                p = parseFloat(reducedCart[value[6]][2].replace(/\,/g, ""), 10);

                // --- Normalize existing qty to a number (remove commas and any "<Max" html)
                let existingQtyRaw = String(
                    reducedCart[value[6]][1] || "0"
                ).split("<")[0];
                let existingQty =
                    Number(existingQtyRaw.replace(/\,/g, "")) || 0;

                // --- Normalize incoming qty to a number as well
                let incomingQtyRaw = String(value[1] || "0").split("<")[0];
                let incomingQty =
                    Number(incomingQtyRaw.replace(/\,/g, "")) || 0;

                // If normalization failed (not a number), fallback to available stock
                if (!isFinite(existingQty)) {
                    existingQty = Number(reducedCart[value[6]][5]) || 0;
                }
                if (!isFinite(incomingQty)) {
                    incomingQty = 0;
                }

                // Sum quantities (numeric addition, no string concat)
                let newQty = existingQty + incomingQty;

                // Now set newQty and recompute price/vat/amount using p (unit price)
                if ($("#quotes_page").length) {
                    // Quotes: no max
                    reducedCart[value[6]][2] = formatMoney(p);
                    reducedCart[value[6]][3] = formatMoney(p * newQty * tax);
                    reducedCart[value[6]][4] = formatMoney(
                        p * newQty * (1 + tax)
                    );
                    reducedCart[value[6]][1] = numberWithCommas(newQty);
                } else if (newQty > reducedCart[value[6]][5]) {
                    // exceed available stock
                    reducedCart[value[6]][1] =
                        numberWithCommas(reducedCart[value[6]][5]) +
                        " <span class='text text-danger'>Max</span>";
                    reducedCart[value[6]][2] = formatMoney(p);
                    reducedCart[value[6]][3] = formatMoney(
                        p * reducedCart[value[6]][5] * tax
                    );
                    reducedCart[value[6]][4] = formatMoney(
                        p * reducedCart[value[6]][5] * (1 + tax)
                    );
                } else {
                    reducedCart[value[6]][1] = numberWithCommas(newQty);
                    reducedCart[value[6]][2] = formatMoney(p);
                    reducedCart[value[6]][3] = formatMoney(p * newQty * tax);
                    reducedCart[value[6]][4] = formatMoney(
                        p * newQty * (1 + tax)
                    );
                }

                dif = reducedCart[value[6]][5] - reducedCart[value[6]][1];
                if ($("#quotes_page").length) {
                    //Qoutes has no maximum quantity
                    reducedCart[value[6]][2] = formatMoney(p);
                    reducedCart[value[6]][3] = formatMoney(
                        p * reducedCart[value[6]][1] * tax
                    );
                    reducedCart[value[6]][4] = formatMoney(
                        p * reducedCart[value[6]][1] * (1 + tax)
                    );
                    reducedCart[value[6]][1] = numberWithCommas(
                        reducedCart[value[6]][1]
                    );
                } else if (dif < 0) {
                    reducedCart[value[6]][1] = reducedCart[value[6]][5];
                    reducedCart[value[6]][2] = formatMoney(p);
                    reducedCart[value[6]][3] = formatMoney(
                        p * reducedCart[value[6]][5] * tax
                    );
                    reducedCart[value[6]][4] = formatMoney(
                        p * reducedCart[value[6]][5] * (1 + tax)
                    );
                    reducedCart[value[6]][1] =
                        numberWithCommas(reducedCart[value[6]][1]) +
                        " " +
                        "<span class='text text-danger'>Max</span>";
                } else {
                    reducedCart[value[6]][2] = formatMoney(p);
                    reducedCart[value[6]][3] = formatMoney(
                        p * reducedCart[value[6]][1] * tax
                    );
                    reducedCart[value[6]][4] = formatMoney(
                        p * reducedCart[value[6]][1] * (1 + tax)
                    );
                    reducedCart[value[6]][1] = numberWithCommas(
                        reducedCart[value[6]][1]
                    );
                } //replace the quantity with max qty on stock
            }
            return reducedCart;
        }, []);
        cart = result;
        var price_category = document.getElementById("price_category").value;
        $("#price_category").prop("disabled", true);
        // document.getElementById("cat_label").style.color = "red";
        cart.forEach(function (item, index, arr) {
            var bought_product = {};
            bought_product.price = parseFloat(item[2].replace(/\,/g, ""), 10);

            if (typeof item[1] != "number") {
                if (isNaN(Number(item[1].toString().replace(",", "")))) {
                    bought_product.quantity = item[5]; //avoid Max string
                } else {
                    bought_product.quantity = numberWithCommas(
                        item[1].toString().replace(",", "")
                    ); //avoid Max string
                }
            } else {
                bought_product.quantity = item[1];
            }
            bought_product.amount = parseFloat(item[4].replace(/\,/g, ""), 10);
            bought_product.product_id = item[6];
            sub_total += bought_product.price;
            total_vat += parseFloat(item[3].replace(/\,/g, ""), 10);
            total += bought_product.amount;
            order_cart.push(bought_product);

            // No need to sum quantities, total_items is already set to cart.length
        });
        //SUBTOTAL WITH DISCOUNT
        total -= sale_discount;
        sub_total = total / (1 + tax);
        total_vat = total - sub_total;
    } else {
        $("#price_category").prop("disabled", false);
        // document.getElementById("cat_label").style.color = "black";
    }

    if (total < 0) {
        document.getElementById("save_btn").disabled = "true";
        if (discount_enable === "YES") {
            document.getElementById("discount_error").style.display = "block";
        }
    } else {
        if (discount_enable === "YES") {
            document.getElementById("discount_error").style.display = "none";
        }
        $("#save_btn").prop("disabled", false);
    }

    //Change Calculator
    try {
        var change = 0;
        var paid = document.getElementById("sale_paid").value;
        sale_paid_amount = parseFloat(paid.replace(/\,/g, ""), 10) || 0;
        change = sale_paid_amount - total;
    } catch (e) {}

    //Credit Sales
    var customer;
    var max_credit;
    var balance;
    if ($("#credit_sale").length) {
        var customer_x = document.getElementById("customer").value;
        if (customer_x === "") {
            customer = JSON.parse("{}");
        } else {
            customer = JSON.parse(document.getElementById("customer").value);
        }

        max_credit = customer.credit_limit - customer.total_credit || 0;
        balance = total - sale_paid_amount;
        if (balance > max_credit) {
            document.getElementById("save_btn").disabled = "true";
            $("div.credit_max")
                .text(formatMoney(max_credit))
                .css({ "font-weight": "Bold", color: "red" });
        } else {
            $("#save_btn").prop("disabled", false);
            $("div.credit_max")
                .text(formatMoney(max_credit))
                .css({ "font-weight": "Bold", color: "green" });
        }
        try {
            document.getElementById("paid_value").value = sale_paid_amount;
        } catch (e) {}
        $("div.sub-total")
            .text(formatMoney(sub_total))
            .css("font-weight", "Bold");
        $("div.tax-amount")
            .text(formatMoney(total_vat))
            .css("font-weight", "Bold");
        $("div.total-amount")
            .text(formatMoney(total))
            .css("font-weight", "Bold");
        $("div.balance-amount")
            .text(formatMoney(balance))
            .css("font-weight", "Bold");
    } else {
        try {
            document.getElementById("change_amount").value =
                formatMoney(change);
        } catch (e) {}
    }

    stringified_cart = JSON.stringify(order_cart);
    document.getElementById("order_cart").value = stringified_cart;
    document.getElementById("price_cat").value = price_category;
    if (discount_enable === "YES") {
        document.getElementById("discount_value").value = sale_discount;
    }

    document.getElementById("total").value = formatMoney(total);
    document.getElementById("sub_total").value = formatMoney(sub_total);
    document.getElementById("total_items").innerHTML = numberWithCommas(total_items);
    var t = document.getElementById("total").value;
    var st = document.getElementById("sub_total").value;

    document.getElementById("total_vat").value = formatMoney(
        parseFloat(t.replace(/\,/g, ""), 10) -
            parseFloat(st.replace(/\,/g, ""), 10)
    );
    console.log("Total items (number of cart entries):", total_items);
    $("#barcode_input").focus();
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
}

function deselect() {
    if (discount_enable === "YES") {
        document.getElementById("sale_discount").value = "0.00";
    }
    // var backDate = document.getElementById("cash_sale_date");
    // if (backDate) {
    //     backDate.value = "";
    // }
    document.getElementById("sub_total").value = 0.00;
    document.getElementById("total_vat").value = 0.00;
    document.getElementById("total").value = 0.00;
    document.getElementById("total_items").innerHTML = 0;
    try {
        document.getElementById("sale_paid").value = 0.00;
        document.getElementById("change_amount").value = 0.00;
    } catch (e) {}

    sub_total = 0;
    total = 0;
    cart = [];
    order_cart = [];
    default_cart = [];
    discount();
    $("#barcode_input").focus();
}

function deselect1() {
    $("#customer").val("").change();
    try {
        document.getElementById("sale_paid").value = 0;
        document.getElementById("sale_discount").value = 0;
        document.getElementById("remark").value = "";
        document.getElementById("total_items").innerHTML = 0;
    } catch (e) {
        console.log("cancel_error");
    }
    sub_total = 0;
    total = 0;
    cart = [];
    order_cart = [];
    default_cart = [];
    discount();
    $("#barcode_input").focus();
}

function deselectQuote() {
    document.getElementById("quote_sale_form").reset();
    document.getElementById("#sale_discount").value = "";
    $("#customer_id").val("").change();
    $("#customer_id").val("").change();
    document.getElementById("total_items").innerHTML = 0;
    sub_total = 0;
    total = 0;
    cart = [];
    order_cart = [];
    default_cart = [];
    discount();
    $("#barcode_input").focus();
}

function saleReturn(items, sale_id) {
    var return_cart = [];
    var id = sale_id;
    localStorage.setItem("id", id);
    document.getElementById("sales").style.display = "none";
    sale_items = [];
    returned =
        " <button class='btn btn-sm btn-rounded btn-success' disabled>Return</button>";
    pending = " <span class='badge badge-secondary'>Pending</span>";
    rejected = " <span class='badge badge-danger'>Rejected</span>";
    items.forEach(function (item) {
        var item_data = [];
        if (item.status !== 3) {
            item_data.push(item.id);
            item_data.push(
                item.name +
                    " " +
                    (item.brand ? item.brand + " " : "") +
                    (item.pack_size ?? '') +
                    (item.sales_uom ?? '')
            );
            item_data.push(item.quantity);
            item_data.push(item.price);
            item_data.push(item.vat);
            item_data.push(item.discount);
            item_data.push(item.amount);
            if (item.status === 2) {
                item_data.push(pending);
            }
            if (item.status === 4) {
                item_data.push(rejected);
            }
            if (item.status === 5) {
                item_data.push(returned);
            }
            sale_items.push(item_data);
        }
    });

    items_table.clear();
    items_table.rows.add(sale_items);
    items_table.column(0).visible(false);
    items_table.draw();
    document.getElementById("items").style.display = "block";

    $("#cancel").on("click", function () {
        return_cart = [];
        localStorage.removeItem("id");
        document.getElementById("sales").style.display = "block";
        document.getElementById("items").style.display = "none";
    });
}

function quoteDetails(remark, items, data) {
    $("#quote_remark").text(remark);
    $("#quote_no").text(data.quote_number);
    $("#customer_name").text(data.customer.name);
    $("#sales_date").text(moment(data.date).format("YYYY-MM-DD"));
    action =
        "<input type='button' value='Sale' id='sale_btn' class='btn btn-primary btn-rounded btn-sm'/>";
    sale_items = [];
    items.forEach(function (item) {
        var item_data = [];
        item_data.push(item.id);
        item_data.push(
            item.name +
                " " +
                (item.brand ? item.brand + " " : "") +
                (item.pack_size ? item.pack_size : "") +
                (item.sales_uom ? item.sales_uom : "")
        );
        item_data.push(item.quantity);
        item_data.push(item.price);
        item_data.push(item.vat);
        item_data.push(item.discount);
        item_data.push(Number(item.amount) - Number(item.discount));
        item_data.push(action);
        sale_items.push(item_data);
    });
    items_table.clear();
    items_table.rows.add(sale_items);
    items_table.column(7).visible(false);
    items_table.column(0).visible(false);
    items_table.draw();
}

$("#sale_quotes-Table tbody").on("click", "#quote_details", function () {
    $("#quote-details").modal("show");
});

$("#items_table tbody").on("click", "#rtn_btn", function () {
    var index = items_table.row($(this).parents("tr")).index();
    var data = items_table.row($(this).parents("tr")).data();
    $("#sale-return").modal("show");
    $("#sale-return").find(".modal-body #id_of_item").val(data[0]);
    $("#sale-return").find(".modal-body #og_item_qty").val(data[2]);
    $("#sale-return").find(".modal-body #name_of_item").val(data[1]);
    document.getElementById("save_btn").style.display = "block";
    $("#sale-return").on("change", "#rtn_qty_to_show", function () {
        var quantity = document.getElementById("rtn_qty").value;
        if (Number(quantity) > Number(data[2]) || Number(quantity) < 0) {
            document.getElementById("save_btn").disabled = "true";
            document.getElementById("qty_error").style.display = "block";
            $("#sale-return")
                .find(".modal-body #qty_error")
                .text("Maximum quantity is " + Math.floor(data[2]));
        } else {
            document.getElementById("qty_error").style.display = "none";
            $("#save_btn").prop("disabled", false);
        }
    });
});

if (discount_enable === "YES") {
    $("#sale_discount").on("change", function (evt) {
        if (evt.which != 110) {
            //not a fullstop
            var n = Math.abs(
                parseFloat($(this).val().replace(/\,/g, ""), 10) || 0
            );
            $(this).val(
                n.toLocaleString("en", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                })
            );
        }
    });
}

$("#paying").on("change", function (evt) {
    if (evt.which != 110) {
        //not a fullstop
        var n = Math.abs(parseFloat($(this).val().replace(/\,/g, ""), 10) || 0);
        $(this).val(
            n.toLocaleString("en", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        );
    }
    var paid = document.getElementById("paying").value;
    paid_amount = parseFloat(paid.replace(/\,/g, ""), 10) || 0;
    $("#credit-sale-payment").find(".modal-body #paid-amount").val(paid_amount);
});

$("#sale_paid").on("change", function (evt) {
    if (evt.which != 110) {
        //not a fullstop
        var n = Math.abs(parseFloat($(this).val().replace(/\,/g, ""), 10) || 0);
        $(this).val(
            n.toLocaleString("en", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        );
    }
});

$("#products").select2({
    placeholder: "Select Product...",
    allowClear: false,
});

$("#products").on("change", function (event) {
    var productValue = $(this).val();
    if (!productValue) return;

    let sel = document.getElementById("products");
    let selectedOption = sel.options[sel.selectedIndex];

    let customer_id = document.getElementById("customer_id").value;

    if (customer_id !== "") {
        valueCollection();
        $("#barcode_input").focus();
    } else {
        notify("Please select customer first", "top", "right", "warning");

        $("#products option").prop("selected", function () {
            return this.defaultSelected;
        });
    }
});

$("#barcode_input").on("keypress", function (e) {
    if (e.which === 13) {
        e.preventDefault();
        let barcode = $(this).val().trim();
        // console.log("Barcode", barcode);
        if (barcode !== "") {
            fetchProductByBarcode(barcode);
            $(this).val(""); // clear input after scan
        }
    }
});

function fetchProductByBarcode(barcode) {
    var price_category = $("#price_category").val();
    $.ajax({
        url: config.routes.filterProductByWord,
        method: "GET",
        data: {
            word: barcode,
            price_category_id: price_category,
        },
        success: function (res) {
            // console.log("Res Data:", res);
            if (res && res.data) {
                addProductToCart(res.data[0]);
            } else {
                notify("Product not found", "top", "right", "danger");
            }
        },
        error: function () {
            notify("Error fetching product", "top", "right", "danger");
        },
    });
}

function addProductToCart(product) {
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

$("#price_category").change(function () {
    var check_store = $("#is_all_store").val();
    if (check_store === "ALL") {
        notify(
            "You can't sell in ALL branches. Please switch to a specific branch to proceed",
            "top",
            "right",
            "warning"
        );

        $("#price_category").val("").trigger("change.select2");
        return;
    }
    var id = $(this).val();
    if (id) {
        $.ajax({
            url: config.routes.selectProducts,
            type: "post",
            data: {
                _token: config.token,
                id: id,
            },
            dataType: "json",
            success: function (result) {
                populateProducts(result.data || []);
                $("#barcode_input").focus();
            },
        });
    }
});

$(document).ready(function () {
    var initialValues = {
        price_category: $("#price_category").val(),
        product_id: $("#products").val(),
        customer_id: $("#customer_id").val(),
    };

    var check_store = $("#is_all_store").val();
    if (check_store === "ALL") {
        notify(
            "You can't sell in ALL branches. Please switch to a specific branch to proceed",
            "top",
            "right",
            "warning"
        );

        $("#products").val("").trigger("change.select2");
        $("#customer_id").val("").trigger("change.select2");
    }
});

function valueCollection() {
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
        var price = parseFloat(cart[idx][2].replace(/,/g, ""));
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
            row[1] =
                numberWithCommas(rawQty) +
                "<span class='text text-danger'> Max</span>";
        } else {
            row[1] = numberWithCommas(newQty);
        }

        // row[2] = formatMoney(price);
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
    $("#barcode_input").focus();
}

$(document).ready(function () {
    var id = document.getElementById("price_category").value;
    if (id) {
        $.ajax({
            url: config.routes.selectProducts,
            type: "post",
            data: {
                _token: config.token,
                id: id,
            },
            dataType: "json",
            success: function (result) {
                // console.log('PRod', result);
                populateProducts(result.data || []);
                $("#barcode_input").focus();
            },
        });
    }
});

function populateProducts(optionsList) {
    const $sel = $("#products");

    // only init/destroy if select2 is present
    if ($sel.data("select2")) {
        $sel.select2("destroy");
    }

    $sel.empty();

    // Add default option once
    $sel.append($("<option>", { value: "", text: "Select product" }));

    if (Array.isArray(optionsList) && optionsList.length) {
        optionsList.forEach(function (p) {
            $sel.append(
                $("<option>", {
                    value: p.id,
                    text: p.name,
                    "data-name": p.name,
                    "data-price": p.price,
                    "data-quantity": p.quantity,
                })
            );
        });
    }

    // Re-init select2 (only if plugin loaded)
    if ($.fn.select2) {
        $sel.select2({ placeholder: "Select Product...", allowClear: false });
    } else {
        console.error("Select2 not loaded");
    }

    // ensure no selection
    $sel.val("").trigger("change");
}

$("#save-customer").click(function () {
    document.getElementById("new-task").value = "New Customer";
});

$("#cancel-customer").click(function () {
    document.getElementById("new-task").value = "Existing Customer";
    document.getElementById("new-customer").style.display = "none";
    document.getElementById("sale-panel").style.display = "block";
    document.getElementById("add-customer").style.display = "block";
});

$("#add-customer").click(function () {
    document.getElementById("new-customer").style.display = "block";
    document.getElementById("sale-panel").style.display = "none";
    document.getElementById("add-customer").style.display = "none";
});

if ($("#can_pay").length) {
    credit_payment_table.column(6).visible(true);
} else {
    credit_payment_table.column(6).visible(false);
}

function getCredits() {
    if ($("#track").length) {
        var status = document.getElementById("payment-status").value;
        var dates = document.querySelector("input[name=date_of_sale]").value;
        dates = dates.split("-");
    }
    var id = document.getElementById("customer_payment").value;
    if (id || status || dates) {
        $.ajax({
            url: config.routes.getCreditSale,
            data: {
                _token: config.token,
                id: id,
                date: dates,
            },
            type: "get",
            dataType: "json",
            success: function (data) {
                //Remove Pay Button for Balance < 1
                data.forEach(function (data) {
                    if (data.balance < 1) {
                        data.action =
                            " <span class='badge badge-success'>Paid</span>";
                    }
                });
                if (status == "all") {
                    data = data;
                } else if (status == "full_paid") {
                    data = data.filter(function (el) {
                        return el.balance < 1;
                    });
                } else if (status == "not_paid") {
                    data = data.filter(function (el) {
                        return el.balance == el.total_amount;
                    });
                } else if (status == "partial_paid") {
                    data = data.filter(function (el) {
                        return el.paid_amount > 0 && el.balance > 0;
                    });
                } else {
                    data = data.filter(function (el) {
                        return el.balance > 0;
                    });
                }

                if (id) {
                    credit_payment_table.column(1).visible(false);
                } else {
                    credit_payment_table.column(1).visible(true);
                }

                credit_payment_table.clear();
                credit_payment_table.rows.add(data);
                credit_payment_table.draw();
            },
            complete: function () {},
        });
    }
}

$("#daterange").change(function () {
    getHistory();
});

$("#customer_payment").change(function () {
    getCredits();
});

$("#payment-status").change(function () {
    getCredits();
});

$("#sales_date").change(function () {
    getCredits();
});

$("#credit_payment_table tbody").on("click", "#pay_btn", function () {
    var index = credit_payment_table.row($(this).parents("tr")).index();
    var data = credit_payment_table.row($(this).parents("tr")).data();
    $("#credit-sale-payment").modal("show");
    $("#credit-sale-payment").find(".modal-body #id_of_sale").val(data.sale_id);
    $("#credit-sale-payment")
        .find(".modal-body #customer-id")
        .val(data.customer_id);
    $("#credit-sale-payment")
        .find(".modal-body #receipt-number")
        .val(data.receipt_number);
    $("#credit-sale-payment")
        .find(".modal-body #balance-amount")
        .val(data.balance);
    $("#credit-sale-payment")
        .find(".modal-body #outstanding")
        .val(formatMoney(data.balance));
    document.getElementById("save_btn").style.display = "block";
    $("#credit-sale-payment").on("change", "#rtn_qty", function () {
        var quantity = document.getElementById("rtn_qty").value;
        if (quantity > data[2]) {
            document.getElementById("save_btn").disabled = "true";
            document.getElementById("qty_error").style.display = "block";
            $("#credit-sale-payment")
                .find(".modal-body #qty_error")
                .text("The maximum quantity is " + data[2]);
        } else {
            document.getElementById("qty_error").style.display = "none";
            $("#save_btn").prop("disabled", false);
        }
    });
});

/*local storage of sale type*/
$("#sales_form").on("submit", function (e) {
    $("#loading").show();
    e.preventDefault();
    var cart = document.getElementById("order_cart").value;
    var is_backdate_enabled = document.getElementById(
        "is_backdate_enabled"
    ).value;

    if (cart === "" || cart === "undefined") {
        notify("Sale list empty", "top", "right", "warning");
        $("#loading").hide();
        return false;
    }
    if (is_backdate_enabled === "YES") {
        var saleDate = document.getElementById("cash_sale_date").value;
        if (saleDate === "" || saleDate == null) {
            notify("Sales date is required", "top", "right", "warning");
            $("#loading").hide();
            return false;
        }
    }

    $("#save_btn").attr("disabled", true);

    saveCashSale();
    $("#barcode_input").focus();
});

function saveCashSale() {
    var form = $("#sales_form").serialize();

    $.ajax({
        url: config.routes.storeCashSale,
        type: "post",
        dataType: "json",
        cache: "false",
        data: form,
        success: function (response) {
            window.open(response.redirect_to);
            $("#save_btn").attr("disabled", false);
            printReceipt(response.redirect_to);
        },
        complete: function () {
            notify("Sale recorded successfully", "top", "right", "success");
            deselect();
            $("#save_btn").attr("disabled", false);
            $("#barcode_input").focus();
            $("#loading").hide();
        },
        timeout: 20000,
    });
}

function printReceipt(pdfUrl) {
    fetch(pdfUrl)
        .then((res) => res.blob())
        .then((blob) => {
            const reader = new FileReader();
            reader.onload = function () {
                const pdfData = new Uint8Array(this.result);
                var config = qz.configs.create("Your Thermal Printer Name");

                var printData = [{ type: "raw", format: "pdf", data: pdfData }];

                qz.print(config, printData).catch(function (e) {
                    console.error(e);
                });
            };
            reader.readAsArrayBuffer(blob);
        });
}

$("#credit_sales_form").on("submit", function (e) {
    // $("#loading").show();
    e.preventDefault();

    var cart = document.getElementById("order_cart").value;

    if (cart === "" || cart === "undefined") {
        notify("Credit Sales list empty", "top", "right", "warning");
        return false;
    }

    $("#save_btn").attr("disabled", true);

    saveCreditSale();
});

function saveCreditSale() {
    var form = $("#credit_sales_form").serialize();

    $.ajax({
        url: config.routes.storeCreditSale,
        type: "post",
        dataType: "json",
        cache: "false",
        data: form,
        success: function (data) {
            notify(
                "Credit Sales recorded successfully",
                "top",
                "right",
                "success"
            );
            deselect1();
            window.open(data.redirect_to);
            $("#save_btn").attr("disabled", false);
        },
        complete: function () {
            // $("#loading").hide();
        },
    });
}

$("#quote_sale_form").on("submit", function (e) {
    e.preventDefault();

    var cart = document.getElementById("order_cart").value;

    if (cart === "" || cart === "undefined") {
        notify("Sale quote list empty", "top", "right", "warning");
        return false;
    }

    $("#save_btn").attr("disabled", true);

    saveQuoteForm();
});

function saveQuoteForm() {
    var form = $("#quote_sale_form").serialize();

    $.ajax({
        url: config.routes.storeQuote,
        type: "post",
        dataType: "json",
        cache: "false",
        data: form,
        success: function (data) {
            notify("Quote recorded successfully", "top", "right", "success");
            deselectQuote();
            window.open(data.redirect_to);
            $("#save_btn").attr("disabled", false);
        },
    });
}

function isNumberKey(evt, obj) {
    var charCode = evt.which ? evt.which : event.keyCode;
    var value = obj.value;
    var dotcontains = value.indexOf(".") !== -1;
    if (dotcontains) if (charCode === 46) return false;
    if (charCode === 46) return true;
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}

function numberWithCommas(digit) {
    return String(parseFloat(digit))
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
