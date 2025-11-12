var cart = [];
var default_cart = [];
var order_cart = [];
var option_data;
const $to = $("#to_id");
const TO_OPTIONS_HTML = $to.html();

var t = 0;

var cart_table = $("#cart_table").DataTable({
    searching: false,
    bPaginate: false,
    bInfo: false,
    bSort: false,
    order: [[0, "desc"]],
    language: {
        emptyTable: "No data available in table",
    },
    data: cart,
    columns: [
        { title: "Product Name" },
        {
            title: "QOH",
            render: function (num) {
                console.log("cart:", cart);
                return numberWithCommas(num);
            },
        },
        { title: "Transfer Quantity" },
        { title: "Product Id" },
        { title: "Stock Id" },
        {
            title: "Action",
            defaultContent:
                "<div>" +
                "<input type='button' value='Edit' id='edit_btn' class='btn btn-info btn-rounded btn-sm'/>" +
                "<input type='button' value='Delete' id='delete_btn' class='btn btn-danger btn-rounded btn-sm'/>" +
                "</div>",
        },
    ],
    columnDefs: [
        {
            targets: [3],
            visible: false,
            searchable: false,
        },
        {
            targets: [4],
            visible: false,
            searchable: false,
        },
    ],
});

function calculateCart() {
    if (cart.length === 0) {
        $("#from_id").prop("disabled", false);
    } else {
        $("#from_id").prop("disabled", true);
    }

    var total = 0;
    var order_cart = [];

    var stringified_cart;
    if (cart[0]) {
        var reduced__obj_cart = {},
            incremental_cart;

        for (var i = 0, c; (c = cart[i]); ++i) {
            if (undefined === reduced__obj_cart[c[0]]) {
                reduced__obj_cart[c[0]] = c;
            } else {
                reduced__obj_cart[c[0]][2] =
                    Number(
                        reduced__obj_cart[c[0]][2].toString().replace(",", "")
                    ) + Number(c[2]);

                if (reduced__obj_cart[c[0]][2] > Number(c[1])) {
                    reduced__obj_cart[c[0]][2] = Number(c[1]);
                }

                reduced__obj_cart[c[0]][2] = numberWithCommas(
                    reduced__obj_cart[c[0]][2]
                );
            }
        }

        incremental_cart = Object.keys(reduced__obj_cart).map(function (val) {
            return reduced__obj_cart[val];
        });

        cart = incremental_cart;
        cart.forEach(function (item, index, arr) {
            var sale_products = {};
            sale_products.quantity = item[2];
            sale_products.product_id = item[3];
            sale_products.stock_id = item[4];
            sale_products.quantityIn = item[1];
            if (
                !(
                    Number(item[2].toString().replace(",", "")) >
                        Number(item[1]) ||
                    isNaN(item[2].toString().replace(",", ""))
                )
            ) {
                sale_products.quantityTran = item[2]
                    .toString()
                    .replace(",", "");
            }
            if (isNaN(item[2].toString().replace(",", ""))) {
                t = 0;
            }
            order_cart.push(sale_products);
        });
        // return false;
        stringified_cart = JSON.stringify(order_cart);
    }
    document.getElementById("order_cart").value = stringified_cart;
}

function rePopulateSelect2() {
    $("#select_id option").remove();
    $("#select_id").append(
        $("<option>", {
            value: "",
            text: "Select Product",
            selected: true,
        })
    );
    $.each(option_data, function (id, detail) {
        var datas = JSON.stringify([
            detail.product.name,
            detail.product.brand,
            detail.product.pack_size,
            detail.product.sales_uom,
            detail.quantity,
            detail.product_id,
            detail.stock_id,
        ]);

        $("#select_id").append(
            $("<option>", {
                value: datas,
                text:
                    detail.product.name +
                    " " +
                    (detail.brand ?? '') +
                    " " +
                    (detail.pack_size ?? '') +
                    (detail.sales_uom ?? ''),
            })
        );
    });
}

$(document).ready(function () {
    const current_store_id = parseInt($("#current_store_id").val(), 10) || 0;

    if (current_store_id && current_store_id !== 1) {
        $("#select_id").prop("disabled", false);
        filterTransferByStore(current_store_id);
    }

    resetToOptions(current_store_id);
});

$(document).on("change", "#select_id", function () {
    val();
});

$(document).on("change", "#from_id", function () {
    const selected_from = parseInt($(this).val(), 10) || 0;
    filterTransferByStore(selected_from);
    resetToOptions(selected_from);
});

function resetToOptions(storeIdToRemove) {
    $to.html(TO_OPTIONS_HTML);

    if (storeIdToRemove && Number(storeIdToRemove) !== 0) {
        $to.find('option[value="' + String(storeIdToRemove) + '"]').remove();
    }

    if ($.fn.select2 && $to.hasClass("select2-hidden-accessible")) {
        $to.trigger("change.select2");
    } else {
        $to.trigger("change");
    }
}

function val() {
    // if edit input exists, trigger change to commit it
    if ($("#edit_quantity").length) $("#edit_quantity").change();

    var productValue = document.getElementById("select_id").value;
    if (!productValue) return;

    let selected_fields;
    try {
        if (
            typeof productValue === "string" &&
            productValue.trim().startsWith("[")
        ) {
            selected_fields = JSON.parse(productValue);
        } else if (Array.isArray(productValue)) {
            selected_fields = productValue;
        } else {
            selected_fields = String(productValue).split(",");
        }
    } catch (e) {
        console.error("Failed to parse product:", productValue, e);
        return;
    }

    const item_name =
        selected_fields[0] +
        " " +
        (selected_fields[1] ?? '') +
        " " +
        (selected_fields[2] ?? '') +
        (selected_fields[3] ?? '');
    const QoH =
        parseFloat(String(selected_fields[4] || 0).replace(/,/g, "")) || 0;
    const product_id = Number(selected_fields[5]);
    const stock_id = Number(selected_fields[6]);

    console.log("Parsed selected product:", {
        item_name,
        QoH,
        product_id,
        stock_id,
    });

    const quantity = 1;

    const item = [item_name, QoH, quantity, product_id, stock_id];

    cart.unshift(item);
    default_cart.unshift([QoH]);

    // reset select
    $("#select_id").val(null).trigger("change");

    calculateCart();
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
}

function deselect() {
    cart = [];
    default_cart = [];
    calculateCart();

    $("#remarks").val("");
    $("#evidence").val("");
    $("#to_id").val(0).change();

    var current_store_id = $("#current_store_id").val();
    const selected_from = parseInt(current_store_id);
    if (selected_from === 1) {
        $("#from_id").val(0).change();
        $("#select_id").prop("disabled", true);
    }
    filterTransferByStore(selected_from);
    $("#select_id").empty();
    $("#select_id").append(
        $("<option>", {
            value: "",
            text: "Select Product...",
            selected: true,
            disabled: true,
        })
    );

    var stringified_cart = JSON.stringify(cart);
    document.getElementById("order_cart").value = stringified_cart;
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
}

$("#cart_table tbody").on("click", "#edit_btn", function () {
    var quantity;
    if (t === 0) {
        /*not set then set it*/
        var row_data = cart_table.row($(this).parents("tr")).data();
        var index = cart_table.row($(this).parents("tr")).index();
        quantity = row_data[2].toString().replace(",", "");
        row_data[2] =
            "<div><input style='width: 50%' type='text' min='1' class='form-control' id='edit_quantity' onkeypress='return isNumberKey(event,this)' required/><span id='span_danger' style='display: none; color: red; font-size: 0.9em;'></span></div>";
        cart[index] = row_data;
        cart_table.clear();
        cart_table.rows.add(cart);
        cart_table.draw();
        document.getElementById("edit_quantity").value = quantity;

        t = 1;
    } else {
        $("#edit_quantity").change();
    }
});

$("#cart_table tbody").on("change", "#edit_quantity", function () {
    t = 0;
    var row_data = cart_table.row($(this).parents("tr")).data();
    var index = cart_table.row($(this).parents("tr")).index();

    row_data[2] = numberWithCommas(
        document.getElementById("edit_quantity").value
    );

    if (
        Number(parseFloat(row_data[2].replace(/\,/g, ""), 10)) >
        Number(row_data[1])
    ) {
        document.getElementById("edit_quantity").style.borderColor = "red";
        document.getElementById("span_danger").style.display = "block";
        $("#span_danger").text(
            "Maximum quantity is " + Math.floor(row_data[1])
        );
        row_data[2] = row_data[2];
        $("#transfer_preview").prop("disabled", true);
        return;
    } else if (Number(parseFloat(row_data[2].replace(/\,/g, ""), 10)) <= 0) {
        document.getElementById("edit_quantity").style.borderColor = "red";
        document.getElementById("span_danger").style.display = "block";
        $("#span_danger").text("Minimum quantity is 1");
        row_data[2] = row_data[2];
        $("#transfer_preview").prop("disabled", true);
        return;
    }

    document.getElementById("span_danger").style.display = "none";
    $("#transfer_preview").prop("disabled", false);

    cart[index] = row_data;
    calculateCart();
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
});

$("#cart_table tbody").on("click", "#delete_btn", function () {
    t = 0;
    var index = cart_table.row($(this).parents("tr")).index();
    cart.splice(index, 1);
    default_cart.splice(index, 1);
    calculateCart();
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
});

$("#deselect-all").on("click", function () {
    deselect();
});

$("#transfer").on("submit", function (e) {
    e.preventDefault();

    var check_cart = document.getElementById("order_cart").value;

    var to_id = document.getElementById("to_id").value;
    var from_id = document.getElementById("from_id").value;

    if (from_id === "0") {
        notify("Please select source branch", "top", "right", "warning");
        return;
    }
    if (to_id === "0") {
        notify("Please select destination branch", "top", "right", "warning");
        return;
    }

    //if cart is empty, then dont submit form
    if (check_cart === "") {
        $("#from_id").prop("disabled", false);
        notify(
            "Please select products to complete transfer",
            "top",
            "right",
            "warning"
        );
        return false;
    }

    var check_cart_to_array;
    if (check_cart === "undefined") {
        check_cart_to_array = [];
    } else {
        check_cart_to_array = JSON.parse(check_cart);
    }

    //check if cart is empty []
    if (!(check_cart_to_array && check_cart_to_array.length)) {
        notify(
            "Transfer list is empty! Please select products to complete transfer",
            "top",
            "right",
            "warning"
        );
        $("#from_id").prop("disabled", false);
        $("#to_id").prop("disabled", false);
        return false;
    }

    try {
        var from = document.getElementById("from_id");
        var from_id = from.options[from.selectedIndex].value;
        var to = document.getElementById("to_id");
        var to_id = to.options[to.selectedIndex].value;
    } catch (e) {
        notify("Something went wrong! Try again.", "top", "right", "warning");
        // $("#from_id").prop("disabled", true);
        return false;
    }

    //check_cart if store are the same
    if (
        (Number(from_id) === 0 && Number(to_id) === 0) ||
        Number(from_id) === Number(to_id)
    ) {
        document.getElementById("from_danger").style.display = "block";
        document.getElementById("to_danger").style.display = "block";
        document.getElementById("border").style.borderColor = "red";
        document.getElementById("to_border").style.borderColor = "red";
        $("#from_id").prop("disabled", true);
        return false;
    }

    document.getElementById("from_danger").style.display = "none";
    document.getElementById("to_danger").style.display = "none";
    document.getElementById("border").style.borderColor = "white";
    document.getElementById("to_border").style.borderColor = "white";

    //check_cart the cart array if quantity tran is missing
    var tran = "quantityTran";

    for (var key in check_cart_to_array) {
        if (check_cart_to_array[key].hasOwnProperty(tran)) {
            //present
            if (check_cart_to_array[key][tran] === "") {
                notify(
                    "Minimum transfer quantity is 1",
                    "top",
                    "right",
                    "warning"
                );
                $("#from_id").prop("disabled", true);
                return false;
            } else if (check_cart_to_array[key][tran] === Number(0)) {
                notify("Cannot transfer 0 quantity", "top", "right", "warning");
                $("#from_id").prop("disabled", true);
                return false;
            }
        } else {
            //not present
            notify(
                "Please check your transfer quantities",
                "top",
                "right",
                "warning"
            );
            $("#from_id").prop("disabled", true);
            return false;
        }
    }

    /*enable from select option*/
    $("#from_id").prop("disabled", false);

    // window.open('#', '_blank');
    // window.open(this.href, '_self');
    saveStockTransfer();
});

function saveStockTransfer() {
    $("#loading").show();
    var formData = new FormData($("#transfer")[0]);
    var errorMessage = false;
    $("#transfer_preview").attr("disabled", true);

    $.ajax({
        url: config.routes.stockTransferSave,
        type: "POST",
        dataType: "json",
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log("Stock transfer response:", response);
            if (response.success) {
                notify(
                    response.message || "Stock transferred successfully",
                    "top",
                    "right",
                    "success"
                );
            } else {
                notify(
                    response.message || "Failed to create stock transfer",
                    "top",
                    "right",
                    "danger"
                );
            }
        },
        error: function (xhr, status, error) {
            errorMessage = true;
            var message = "Failed to create stock transfer!";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            notify(message, "top", "right", "danger");
        },
        complete: function () {
            if (errorMessage === false) {
                deselect();
            }
            $("#transfer_preview").attr("disabled", false);
            $("#loading").hide();
        },
        timeout: 20000,
    });
}

function filterTransferByStore(from_id) {
    if (!from_id || from_id == 0) {
        return;
    }

    /*ajax filter by store*/
    $("#loading").show();
    $.ajax({
        url: config.routes.filterByStore,
        type: "get",
        dataType: "json",
        data: {
            from_id: from_id,
        },
        success: function (data) {
            option_data = data;
            // console.log("Filtered products:", data);
            $("#to_id").prop("disabled", false);
            $("#select_id").prop("disabled", false);
            $("#select_id option").remove();
            $("#select_id").append(
                $("<option>", {
                    value: "",
                    text: "Select Product",
                    selected: false,
                })
            );
            $.each(data, function (id, detail) {
                var datas = JSON.stringify([
                    detail.name,
                    detail.brand,
                    detail.pack_size,
                    detail.sales_uom,
                    detail.quantity,
                    detail.product_id,
                    detail.stock_id,
                ]);

                $("#select_id").append(
                    $("<option>", {
                        value: datas,
                        text:
                            detail.name +
                            " " +
                            (detail.brand ?? '') +
                            " " +
                            (detail.pack_size ?? '') +
                            (detail.sales_uom ?? ''),
                    })
                );
            });
        },
        complete: function () {
            $("#loading").hide();
        },
    });
}

$("#select_id").select2({
    language: {
        noResults: function () {
            var search_input = $("#select_id")
                .data("select2")
                .$dropdown.find("input")
                .val();
            var from = document.getElementById("from_id");
            var from_id = from.options[from.selectedIndex].value;

            /*make ajax call for more*/
            $.ajax({
                url: config.routes.filterByWord,
                type: "get",
                dataType: "json",
                data: {
                    word: search_input,
                    from_id: from_id,
                },
                success: function (data) {
                    option_data = data;
                    $("#select_id option").remove();
                    $("#select_id").append(
                        $("<option>", {
                            value: "",
                            text: "Select Product",
                        })
                    );
                    $.each(data, function (id, detail) {
                        var datas = JSON.stringify([
                            detail.product.name,
                            detail.product.brand,
                            detail.product.pack_size,
                            detail.product.sales_uom,
                            detail.quantity,
                            detail.product_id,
                            detail.stock_id,
                        ]);

                        $("#select_id").append(
                            $("<option>", {
                                value: datas,
                                text: detail.product.name,
                            })
                        );
                    });
                },
            });
        },
    },
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
    } catch (e) {
        console.log(e);
    }
}

function numberWithCommas(digit) {
    return String(parseFloat(digit))
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function isNumberKey(evt, obj) {
    var charCode = evt.which ? evt.which : event.keyCode;
    var value = obj.value;
    var dotcontains = value.indexOf(".") !== -1;
    if (dotcontains) if (charCode === 46) return false;
    if (charCode === 46) return true;
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}
