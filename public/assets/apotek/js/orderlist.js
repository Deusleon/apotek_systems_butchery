var details = [];
var order_items = [];
// Track currently opened order in the modal
var currentOrderData = null;
var currentRowIndex = null;

var order_list_table = $("#order_list_table").DataTable({
    searching: true,
    bPaginate: false,
    bInfo: true,
    ordering: false,
});

var order_history_datatable = $("#order_history_datatable").DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    ordering: true,
    order: [[0, "desc"]],
    columns: [
        { data: "id", visible: false }, // hidden ID column for internal use
        { data: "order_number" },
        { data: "supplier.name" },
        {
            data: "ordered_at",
            render: function (ordered_at) {
                return moment(ordered_at).format("YYYY-MM-DD");
            },
        },
        {
            data: "total_amount",
            render: function (total_amount) {
                return formatMoney(total_amount);
            },
        },
        {
            data: "status",
            render: function (status, type, row) {
                // "Approved" state sources:
                // - Backend flags: status === '2' or status === '3' or status === 'Approved'
                // - Front-end approval (set by clicking Approve in modal): row.clientApproved === true
                var isApprovedByBackend =
                    status === "2" ||
                    status === "3" ||
                    status == "4" ||
                    status === "Approved";
                var isApprovedByClient = !!row.clientApproved;

                var showBtn = config2.showPurchaseOrder
                    ? "<input type='button' value='Show' id='dtl_btn' class='btn btn-success btn-rounded btn-sm'/>"
                    : "";
                var printBtnEnabled =
                    "<button id='print_btn' class='btn btn-primary btn-rounded btn-sm'><span class='fa fa-print' aria-hidden='true'></span> Print</button>";
                var printBtnDisabled =
                    "<button id='print_btn' class='btn btn-secondary btn-rounded btn-sm' disabled><span class='fa fa-print' aria-hidden='true'></span> Print</button>";
                var printBtn = config2.printPurchaseOrder
                    ? isApprovedByBackend || isApprovedByClient
                        ? printBtnEnabled
                        : printBtnDisabled
                    : "";

                // Cancelled: never printable
                if (status === "Cancelled") {
                    return (
                        "" +
                        showBtn +
                        " " +
                        (config.printPurchaseOrder ? printBtnDisabled : "") +
                        " " +
                        "<span class='badge badge-warning badge-lg'>Rejected</span>"
                    );
                }

                // Approved (backend or client) => print enabled; else disabled
                if (isApprovedByBackend || isApprovedByClient) {
                    return "" + showBtn + " " + printBtn;
                }

                // Not approved yet => print disabled
                return "" + showBtn + " " + printBtn;
            },
        },
    ],
});

var order_details_table = $("#order_details_table").DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    data: order_items,
    columns: [
        { title: "ID" },
        { title: "Product Name" },
        { title: "Quantity" },
        { title: "Price" },
        { title: "VAT" },
        { title: "Amount" },
        {
            title: "Action",
            defaultContent:
                "<input type='button' value='Receive' id='rtn_btn' class='btn btn-primary btn-rounded btn-sm'/>",
        },
    ],
});

$(function () {
    var start = moment().subtract(6, "days");
    var end = moment();

    function cb(start, end) {
        $("#date_filter span").html(
            start.format("YYYY/MM/DD") + " - " + end.format("YYYY/MM/DD")
        );
    }

    $("#date_filter").daterangepicker(
        {
            startDate: start,
            endDate: end,
            autoUpdateInput: true,
            locale: {
                format: "YYYY/MM/DD", // <-- Add this line
            },
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                "Last 30 Days": [moment().subtract(29, "days"), moment()],
                "This Month": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Last Month": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
                "This Year": [moment().startOf("year"), moment()],
            },
        },
        cb
    );

    cb(start, end);
});

function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
    try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";
        const absoluteAmount = Math.abs(Number(amount) || 0).toFixed(
            decimalCount
        );

        // Split integer and decimal parts
        let [integerPart, decimalPart] = absoluteAmount.split(".");

        // Add thousands separator
        let i = integerPart;
        let j = i.length > 3 ? i.length % 3 : 0;
        let formattedInt =
            (j ? i.substr(0, j) + thousands : "") +
            i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands);

        if (decimalCount === 0) {
            return negativeSign + formattedInt;
        } else {
            return negativeSign + formattedInt + decimal + decimalPart;
        }
    } catch (e) {
        console.log(e);
        return "0";
    }
}

$("#order_history_datatable tbody").on("click", "#print_btn", function () {
    var data = order_history_datatable.row($(this).parents("tr")).data();

    document.getElementById("order_no").value = data.details[0].order_id;
});

function getOrderHistory() {
    var range = document.getElementById("date_filter").value;
    var date = range.split("-");
    $.ajax({
        url: config2.routes.getOrderHistory,
        data: {
            _token: "{{ csrf_token() }}",
            date: date,
        },
        type: "get",
        dataType: "json",
        success: function (data) {
            console.log("Data received:", data);
            data.forEach(function (data) {
                if (data.status === "Cancelled") {
                    data.action =
                        "<input type='button' value='Show' id='dtl_btn' class='btn btn-success btn-rounded btn-sm' size='2'/><button id='print_btn' class='btn btn-secondary btn-rounded btn-sm'><span class='fa fa-print' aria-hidden='true'></span> Print</button><span class='badge badge-warning badge-lg'>Cancelled</span>";
                }
            });
            order_history_datatable.clear();
            order_history_datatable.rows.add(data);
            order_history_datatable.draw();
            order_history_datatable.order([0, "desc"]).draw();
        },
    });
}

$("#order_history_datatable tbody").on("click", "#dtl_btn", function () {
    var row = order_history_datatable.row($(this).parents("tr"));
    var data = row.data();
    console.log("RowData", data);
    currentOrderData = data; // keep reference for Approve/Cancel
    currentRowIndex = row.index(); // we’ll use this to update the row after approve
    updateModalButtons(data.status);
    orderDetails(data.details);
    $("#purchases-details").modal("show");
});

$("#order_history_datatable tbody").on("click", "#dtl_btn", function () {
    var row = order_history_datatable.row($(this).parents("tr"));
    var data = row.data();
    console.log("RowData", data);
    currentOrderData = data;
    currentRowIndex = row.index();

    // Update buttons based on status - THIS IS THE KEY LINE
    updateModalButtons(data.status);

    orderDetails(data.details);
    $("#purchases-details").modal("show");
});

// Your perfect function
function updateModalButtons(status) {
    var cancelBtn = $("#cancel_btn_modal");
    var approveBtn = $("#approve_btn");
    var statusMessage = $("#status_message");

    // Reset all elements
    cancelBtn.show();
    approveBtn.show();
    statusMessage.addClass("d-none");

    // If status is not '1', hide buttons and show message
    if (status !== "1") {
        cancelBtn.hide();
        approveBtn.hide();
        statusMessage.removeClass("d-none");

        // Set appropriate message based on status
        if (status === "2" || status === "3" || status === "4") {
            statusMessage.text(
                "This order has already been approved and cannot be modified."
            );
            statusMessage.removeClass("alert-info").addClass("alert-success");
        } else if (status === "Cancelled") {
            statusMessage.text(
                "This order has been Rejected and cannot be modified."
            );
            statusMessage.removeClass("alert-info").addClass("alert-warning");
        } else {
            statusMessage.text(
                "This order cannot be modified in its current status."
            );
        }
    }
}

function orderDetails(items) {
    order_items = [];
    items.forEach(function (item) {
        var item_data = [];

        // ID column (hidden)
        item_data.push(item.order_item_id);

        // Product Name with optional properties
        var fullProductName = item.name;
        if (item.brand) fullProductName += " " + item.brand;
        if (item.pack_size) fullProductName += " " + item.pack_size;
        if (item.sales_uom) fullProductName += "" + item.sales_uom;
        item_data.push(fullProductName);

        // Quantity
        item_data.push(formatMoney(item.ordered_qty, 0));

        // Price, VAT, Amount
        item_data.push(formatMoney(item.price));
        item_data.push(formatMoney(item.vat));
        item_data.push(formatMoney(item.amount));

        // Action column (hidden)
        item_data.push("");

        order_items.push(item_data);
    });

    order_details_table.clear();
    order_details_table.rows.add(order_items);
    order_details_table.column(0).visible(false); // hide ID
    order_details_table.column(6).visible(false); // hide Action
    order_details_table.draw();
}

$("#order_history_datatable tbody").on("click", "#cancel_btn", function () {
    var data = order_history_datatable.row($(this).parents("tr")).data();
    var index = order_history_datatable.row($(this).parents("tr")).index();
    $("#cancel-order").modal("show");
    var message = "Are you sure you want to Reject Order '".concat(
        data.order_number,
        "'?"
    );
    var modal = $(this);
    $("#cancel-order").find(".modal-body #message").text(message);
    $("#cancel-order").find(".modal-body #delete_id").val(data.id);
});
// --- Extract approval action so we can call it after confirmation ---
function applyClientApprove() {
    if (!currentOrderData) {
        console.error("No current order data!");
        return;
    }

    console.log("Approving order ID:", currentOrderData.id);

    // Build the URL with the actual order ID
    var approveUrl = config2.routes.approveOrder.replace(
        ":id",
        currentOrderData.id
    );
    console.log("API URL:", approveUrl);

    // Make API call to update order status in database
    $.ajax({
        url: approveUrl,
        type: "POST",
        data: {
            _token: config2.csrfToken, // ← USE THE TOKEN FROM CONFIG2
        },
        success: function (response) {
            console.log("API Response:", response);
            if (response.success) {
                // Update frontend only after successful backend update
                currentOrderData.clientApproved = true;
                currentOrderData.status = response.status; // Update status to match backend

                if (currentRowIndex !== null) {
                    order_history_datatable
                        .row(currentRowIndex)
                        .data(currentOrderData)
                        .draw(false);
                }

                // Show success message immediately
                if (typeof success_noti === "function") {
                    success_noti(response.message);
                } else if (typeof notify === "function") {
                    notify(response.message, "top", "right", "success");
                } else {
                    alert(response.message);
                }

                // Close the approval modal
                $("#approve-order").modal("hide");

                // Update modal buttons to reflect new status
                updateModalButtons(response.status);
            } else {
                console.error("API Error:", response.message);
                alert("Error: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", status, error);
            console.error("Response:", xhr.responseText);
            alert("Error approving order. Please check console for details.");
        },
    });
}

// When user clicks Approve button inside details modal → open confirm modal
$(document).on("click", "#approve_btn", function () {
    if (!currentOrderData) return;

    $("#approve_message").text(
        "Are you sure you want to Approve Order '" +
            currentOrderData.order_number +
            "'?"
    );

    $("#approve-order").modal("show"); // <-- show confirm modal
    $("#purchases-details").modal("hide");
});

// If confirm → apply approve
$(document)
    .off("click", "#approve_yes_btn")
    .on("click", "#approve_yes_btn", function () {
        applyClientApprove(); // This now handles the success message internally
    });

// If cancel → just close confirm, (optionally reopen details modal)
$(document)
    .off("click", "#approve_no_btn")
    .on("click", "#approve_no_btn", function () {
        $("#approve-order").modal("hide");
        // If you prefer to reopen details modal:
        // $("#purchases-details").modal("show");
    });

// CANCEL inside modal (open your existing cancel confirmation modal)
$(document).on("click", "#cancel_btn_modal", function () {
    if (!currentOrderData) return;

    // Open the already existing cancel confirm modal and reuse your original population logic
    $("#cancel-order").modal("show");
    var message =
        "Are you sure you want to Reject Order '" +
        currentOrderData.order_number +
        "'?";
    $("#cancel-order").find(".modal-body #message").text(message);
    $("#cancel-order").find(".modal-body #delete_id").val(currentOrderData.id);

    // Close details modal while confirmation is shown
    $("#purchases-details").modal("hide");
});
