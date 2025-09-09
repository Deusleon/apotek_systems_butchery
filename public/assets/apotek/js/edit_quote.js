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
                }
            },
        });
    }
});

$(document).ready(function () {
    fetchProducts();
});

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
                }
            },
            error: function () {
                notify("Could not load products", "top", "right", "danger");
            },
        });
    }
});

function newDiscount(val){
    var currentTotal = document.getElementById('total').value;
    var sub_total = document.getElementById('sub_total').value;
    var vat = document.getElementById('total_vat').value;
    var newTotal = Number(unformatNumber(sub_total)-unformatNumber(val))+Number(unformatNumber(vat))
    // console.log('NewDisc', currentTotal, sub_total, vat);
    document.getElementById('total').value = formatNumber(newTotal, 2);
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
            <td>${item.name} ${item.brand ? item.brand+' ' : ''} ${item.pack_size} ${item.sales_uom}</td>
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
    document.getElementById("sale_discount").value = formatNumber(Number(data.discount),2);
    document.getElementById("sub_total").value = formatNumber(
        Number(data.sub_total),
        2
    );
    document.getElementById("total_vat").value = formatNumber(
        Number(data.vat),
        2
    );
    document.getElementById("total").value = formatNumber(Number(data.total),
        2
    );
}

function isCartEmpty(value) {
    var priceSelect = document.getElementById("price_category");
    if (value > 0) {
        priceSelect.disabled = true;
    } else {
        priceSelect.disabled = false;
    }
}
