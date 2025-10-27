$(document).ready(function () {
    $("#loading").show();
    var daterange =
        moment().format("YYYY/MM/DD") + "-" + moment().format("YYYY/MM/DD");
    getHistory(daterange);
    $("#loading").hide();
});

$("#daterange").on("apply.daterangepicker", function (ev, picker) {
    var range = $(this).val();
    getHistory(range);
});

function getHistory(range) {
    range = range.split("-");
    if (range) {
        $.ajax({
            url: config.routes.getSalesHistory,
            type: "POST",
            data: {
                _token: config.token,
                range: range,
            },
            dataType: "json",
            success: function (response) {
                // console.log("response is", response);
                populateTable(response.data);
            },
        });
    }
}

var isDiscountEnabled = $("#discount_enabled").val() === "YES";
var columns = [
    { title: "Receipt #", searchable: true },
    { title: "Customer", searchable: true },
    { title: "Date", searchable: true },
    { title: "Sub Total", searchable: true },
    { title: "VAT", searchable: true },
];

if (isDiscountEnabled) {
    columns.push({ title: "Discount", searchable: true });
}

columns.push(
    { title: "Amount", searchable: true },
    { title: "Created By", searchable: true },
    { title: "Action", orderable: false, searchable: false }
);

var saleHistoryTable = $("#sale_history_dataTable").DataTable({
    responsive: true,
    order: [[2, "desc"]],
    searching: true,
    columns: columns,
});


var saleHistoryDataTable;

if (!$.fn.DataTable.isDataTable("#sales_history_table")) {
    saleHistoryDataTable = $("#sales_history_table").DataTable({
        responsive: true,
        order: [[0, "asc"]],
        searching: false,
        paging: false,
        info: false,
        columns: [
            { title: "Product Name" },
            { title: "Quantity" },
            { title: "Price" },
        ],
    });
} else {
    saleHistoryDataTable = $("#sales_history_table").DataTable();
}

function populateTable(data) {
    // Clear old rows
    saleHistoryTable.clear();

    data.forEach(function (item) {
        let receipt_url = config.routes.receiptBaseUrl.replace(
            ":receipt",
            item.receipt_number
        );

        let actions = `
            <button type='button' class='btn btn-sm show-sales btn-rounded btn-success'
                data-receipt='${item.receipt_number}'
                data-customer='${item.customer.name}'
                data-created-by='${item.user.name}'
                data-sale-id='${item.id}'
                data-date='${moment(item.date).format("YYYY-MM-DD")}'>
                Show
            </button>
        `;

        if (typeof canPrintSalesHistory !== "undefined" && canPrintSalesHistory) {
            actions += `
                <a href="${receipt_url}" target="_blank">
                    <button class="btn btn-sm btn-rounded btn-secondary" type="button">
                        <span class="fa fa-print"></span>
                        Print
                    </button>
                </a>
            `;
        }

        let row = [
            item.receipt_number,
            item.customer.name,
            moment(item.date).format("YYYY-MM-DD"),
            formatMoney(Number(item.total_amount) - Number(item.total_vat)),
            formatMoney(Number(item.total_vat)),
        ];

        if (isDiscountEnabled) {
            row.push(formatMoney(Number(item.total_discount)));
        }

        row.push(
            formatMoney(Number(item.total_amount) - Number(item.total_discount)),
            item.user.name,
            actions
        );

        saleHistoryTable.row.add(row);
    });

    // Redraw the table
    saleHistoryTable.draw();
}

$(document).on("click", ".show-sales", function () {
    var receipt = $(this).data("receipt");
    var customer = $(this).data("customer");
    var date = $(this).data("date");
    var saleId = $(this).data("sale-id");
    var createdBy = $(this).data("created-by");
    $("#sale-details").modal("show");
    $("#receipt_no").text(receipt);
    $("#customer_name").text(customer);
    $("#created_by").text(createdBy);
    $("#sales_date").text(date);
    getHistoryData(saleId);
});

function getHistoryData(receipt) {
    if (receipt) {
        $.ajax({
            url: config.routes.getSalesHistoryData,
            type: "POST",
            data: {
                _token: config.token,
                receipt: receipt,
            },
            dataType: "json",
            success: function (response) {
                // console.log("response is", response);
                populateHistoryTable(response.data);
            },
        });
    }
}

function populateHistoryTable(data) {
    // Clear old rows
    saleHistoryDataTable.clear();

    // Add new rows
    data.forEach(function (item) {
        saleHistoryDataTable.row.add([
            item.name +
                " " +
                (item.brand ? item.brand + " " : "") +
                (item.pack_size ? item.pack_size : "") +
                (item.sales_uom ? item.sales_uom : ""),
            numberWithCommas(Number(item.quantity).toFixed(0)),
            numberWithCommas(Number(item.price).toFixed(2)),
        ]);
    });

    // Redraw the table
    saleHistoryDataTable.draw();
}

function numberWithCommas(digit) {
    return String(parseFloat(digit))
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

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
