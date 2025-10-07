$("#products").select2({
    placeholder: "Select Product...",
    allowClear: true,
});

$("#price_category").change(function () {
    var id = $(this).val();
    if (id) {
        $.ajax({
            url: config.routes.selectProducts,
            type: "POST",
            data: {
                _token: config.token,
                id: id,
            },
            dataType: "json",
            success: function (result) {
                $("#products").empty().trigger("change");
                if (result.data && result.data.length > 0) {
                    result.data.forEach(function (p) {
                        $("#products").append(
                            $("<option>", { value: "", text: "Select product" })
                        );
                        $("#products").append(
                            $("<option>", {
                                value: p.id,
                                text: p.name,
                                "data-name": p.name,
                                "data-price": p.price,
                                "data-quantity": p.quantity,
                            })
                        );
                    });
                    $("#products").trigger("change");
                    $("#quote_barcode_input").focus();
                }
            },
        });
    }
});

$(document).ready(function () {
    fetchProducts();
    $("#quote_barcode_input").focus();
});

$("#sale_discount").on("blur", function () {
    $("#quote_barcode_input").focus();
});

$("#quote_barcode_input").on("keypress", function (e) {
    if (e.which === 13) {
        e.preventDefault();
        let barcode = $(this).val().trim();
        // console.log("Barcode", barcode);
        if (barcode !== "") {
            fetchProductByBarcode(barcode);
            $(this).val("");
        }
    }
});

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

function fetchProductByBarcode(barcode) {
    var price_category = $("#price_category").val();
    // var customer_id = $("#customer_id").val();

    $.ajax({
        url: config.routes.filterProductByWord,
        method: "GET",
        data: {
            word: barcode,
            price_category_id: price_category,
            // customer_id: customer_id,
        },
        dataType: "json",
        success: function (res) {
            // console.log("Res Data:", res);
            if (res && Array.isArray(res.data) && res.data.length > 0) {
                var id = res.data[0].id;
                var price = res.data[0].price;
                var qty = 1;
                var quoteId = document.getElementById("quoted_id").value;
                handleProductChange(id, price, qty, quoteId);
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
        let existingQty = Number(existingQtyRaw.replace(/\,/g, "")) || 0;

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
            row[2] = formatMoney(priceNum);
            row[3] = formatMoney(vatUnit * stockQty);
            row[4] = formatMoney(unitTotal * stockQty);
        } else {
            row[1] = numberWithCommas(newQty);
            row[2] = formatMoney(priceNum);
            row[3] = formatMoney(vatUnit * newQty);
            row[4] = formatMoney(unitTotal * newQty);
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
function handleProductChange(id, price, qty, quoteId) {
    if (id && id !== "") {
        $.ajax({
            url: config.routes.addQuoteItem,
            method: "POST",
            data: {
                _token: config.token,
                product_id: id,
                price: price,
                quantity: qty,
                quote_id: quoteId,
            },
            success: function (response) {
                fetchProducts();
                refreshSalesTable(response.data);
                isCartEmpty(response.data.sales_details.length);

                // update totals
                $("#sub_total").val(
                    formatNumber(Number(response.data.sub_total), 2)
                );
                $("#total_vat").val(formatNumber(Number(response.data.vat), 2));
                $("#total").val(formatNumber(Number(response.data.total), 2));

                // optional notify
                // notify(response.message, "top", "right", "success");
            },
            error: function () {
                notify("Failed", "top", "right", "danger");
            },
        });
    }

    // always return focus back
    setTimeout(function () {
        $("#quote_barcode_input").focus();
    }, 150);
}

$("#products").on("change", function () {
    var id = $(this).val();
    var price = $("#products option:selected").data("price");
    var quantity = 1;
    var quoteId = document.getElementById("quoted_id").value;
    if (id != "" && id != null) {
        $.ajax({
            url: config.routes.addQuoteItem,
            method: "POST",
            data: {
                _token: config.token,
                product_id: id,
                price: price,
                quantity: quantity,
                quote_id: quoteId,
            },
            success: function (response) {
                fetchProducts();
                refreshSalesTable(response.data);
                isCartEmpty(response.data.sales_details.length);
                // console.log("response", response);
                // notify(response.message, "top", "right", "success");
                document.getElementById("sub_total").value = formatNumber(
                    Number(response.data.sub_total),
                    2
                );
                document.getElementById("total_vat").value = formatNumber(
                    Number(response.data.vat),
                    2
                );
                document.getElementById("total").value = formatNumber(
                    Number(response.data.total),
                    2
                );
            },
            error: function (xhr) {
                notify("Failed", "top", "right", "danger");
            },
        });
    }
    $("#quote_barcode_input").focus();
});

$("#customer_id").on("change", function () {
    var customer_id = $(this).val();
    var quoteId = document.getElementById("quoted_id").value;
    if (customer_id != "" && customer_id != null) {
        $.ajax({
            url: config.routes.changeCustomer,
            method: "POST",
            data: {
                _token: config.token,
                id: quoteId,
                customer_id: customer_id,
            },
            success: function (response) {
                fetchProducts();
                $("#quote_barcode_input").focus();
                // console.log("response", response);
            },
            error: function (xhr) {
                notify("Failed", "top", "right", "danger");
            },
        });
    }
});

$("#price_category").on("change", function () {
    var price_category_id = $(this).val();
    var quoteId = document.getElementById("quoted_id").value;
    if (price_category_id != "" && price_category_id != null) {
        $.ajax({
            url: config.routes.changePriceCategory,
            method: "POST",
            data: {
                _token: config.token,
                id: quoteId,
                price_category_id: price_category_id,
            },
            success: function (response) {
                fetchProducts();
                // console.log("response", response);
            },
            error: function (xhr) {
                notify("Failed", "top", "right", "danger");
            },
        });

        $.ajax({
            url: config.routes.selectProducts,
            type: "POST",
            data: {
                _token: config.token,
                id: price_category_id,
            },
            dataType: "json",
            success: function (result) {
                $("#products").empty().trigger("change");
                $("#products").append(
                    $("<option>", { value: "", text: "Select product" })
                );
                if (result.data && result.data.length > 0) {
                    result.data.forEach(function (p) {
                        $("#products").append(
                            $("<option>", {
                                value: p.id,
                                text: p.name,
                                "data-name": p.name,
                                "data-price": p.price,
                                "data-quantity": p.quantity,
                            })
                        );
                    });
                    $("#products").trigger("change");
                    $("#quote_barcode_input").focus();
                }
            },
            error: function () {
                notify("Could not load products", "top", "right", "danger");
            },
        });
    }
});

function newDiscount(val) {
    var currentTotal = document.getElementById("total").value;
    var sub_total = document.getElementById("sub_total").value;
    var vat = document.getElementById("total_vat").value;
    var newTotal =
        Number(unformatNumber(sub_total) - unformatNumber(val)) +
        Number(unformatNumber(vat));
    document.getElementById("total").value = formatNumber(newTotal, 2);
    $("#quote_barcode_input").focus();
}

function fetchProducts() {
    var id = document.getElementById("price_category").value;
    if (id) {
        $.ajax({
            url: config.routes.selectProducts,
            type: "POST",
            data: {
                _token: config.token,
                id: id,
            },
            dataType: "json",
            success: function (result) {
                // console.log("Products", result);
                $("#products").empty().trigger("change");
                if (result.data && result.data.length > 0) {
                    $("#products").append(
                        $("<option>", { value: "", text: "Select product" })
                    );
                    result.data.forEach(function (p) {
                        $("#products").append(
                            $("<option>", {
                                value: p.id,
                                text: p.name,
                                "data-name": p.name,
                                "data-price": p.price,
                                "data-quantity": p.quantity,
                            })
                        );
                    });
                    $("#products").trigger("change");
                    $("#quote_barcode_input").focus();
                }
            },
        });
    }
}

function refreshSalesTable(data) {
    var tbody = $("#edit_sales_order tbody");
    tbody.empty(); // clear current rows

    data.sales_details.forEach(function (item) {
        var row = `
        <tr data-id="${item.id}">
            <td>${item.name} ${item.brand ? item.brand + " " : ""} ${
            item.pack_size
        } ${item.sales_uom}</td>
            <td class="quantity">${formatNumber(item.quantity, 0)}</td>
            <td class="price">${formatNumber(item.price, 0)}</td>
            <td>${formatNumber(item.vat, 0)}</td>
            <td class="amount">${formatNumber(item.amount, 0)}</td>
            <td hidden>${formatNumber(item.discount, 0)}</td>
            <td>
                <button class="btn btn-primary btn-sm btn-edit btn-rounded">Edit</button>
                <button class="btn btn-danger btn-sm btn-delete btn-rounded" 
                    data-quote-id="${item.quote_id}" 
                    data-quote-item-id="${item.id}">Delete</button>
            </td>
        </tr>
        `;
        tbody.append(row);
    });
    var is_discount_enabled = document.getElementById(
        "is_discount_enabled"
    ).value;
    if (is_discount_enabled === "YES") {
        document.getElementById("sale_discount").value = formatNumber(
            Number(data.discount),
            2
        );
    }
    document.getElementById("sub_total").value = formatNumber(
        Number(data.sub_total),
        2
    );
    document.getElementById("total_vat").value = formatNumber(
        Number(data.vat),
        2
    );
    document.getElementById("total").value = formatNumber(
        Number(data.total),
        2
    );

    $("#quote_barcode_input").focus();
}

function isCartEmpty(value) {
    var priceSelect = document.getElementById("price_category");
    if (value > 0) {
        priceSelect.disabled = true;
    } else {
        priceSelect.disabled = false;
    }
    $("#quote_barcode_input").focus();
}
