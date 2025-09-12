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
                    status === "2" || status === "3" || status === "Approved";
                var isApprovedByClient = !!row.clientApproved;

                // Cancelled: never printable
                if (status === "Cancelled") {
                    return (
                        "" +
                        "<input type='button' value='Show' id='dtl_btn' class='btn btn-success btn-rounded btn-sm'/> " +
                        "<button id='print_btn' class='btn btn-secondary btn-rounded btn-sm' disabled>" +
                        "<span class='fa fa-print' aria-hidden='true'></span> Print" +
                        "</button> " +
                        "<span class='badge badge-warning badge-lg'>Rejected</span>"
                    );
                }

                // Approved (backend or client) => print enabled; else disabled
                if (isApprovedByBackend || isApprovedByClient) {
                    return (
                        "" +
                        "<input type='button' value='Show' id='dtl_btn' class='btn btn-success btn-rounded btn-sm'/> " +
                        "<button id='print_btn' class='btn btn-primary btn-rounded btn-sm'>" +
                        "<span class='fa fa-print' aria-hidden='true'></span> Print" +
                        "</button>"
                    );
                }

                // Not approved yet => print disabled
                return (
                    "" +
                    "<input type='button' value='Show' id='dtl_btn' class='btn btn-success btn-rounded btn-sm'/> " +
                    "<button id='print_btn' class='btn btn-secondary btn-rounded btn-sm' disabled>" +
                    "<span class='fa fa-print' aria-hidden='true'></span> Print" +
                    "</button>"
                );
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

        return negativeSign + formattedInt + decimal + decimalPart;
    } catch (e) {
        console.log(e);
        return "0.00";
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
    orderDetails(data.details);
    $("#purchases-details").modal("show");
});

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
        item_data.push(item.ordered_qty);

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
    if (!currentOrderData) return;

    // Mark as client-approved (frontend flag)
    currentOrderData.clientApproved = true;

    // Update that specific row in the DataTable so Print becomes enabled
    if (currentRowIndex !== null) {
        order_history_datatable
            .row(currentRowIndex)
            .data(currentOrderData)
            .draw(false);
    }
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
        applyClientApprove();

        //Success message
        try {
            if (window.toastr && typeof toastr.success === "function") {
                toastr.success("Order approved successfully!");
            } else if (typeof success_noti === "function") {
                // if your notification.js exposes success_noti(...)
                success_noti("Order approved successfully!");
            } else if (typeof notify === "function") {
                // some projects use notify(type, msg)
                notify("success", "Order approved successfully!");
            } else {
                alert("Order approved successfully!");
            }
        } catch (e) {
            console.log(e);
        }
        $("#approve-order").modal("hide");
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
