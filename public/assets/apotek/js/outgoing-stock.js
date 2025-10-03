var summary_table = $("#fixedHeader").DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    columns: [
        { data: "product_name" },
        { data: "out_total" },
        // { data: "qoh" }
    ],
    // order: [[1, "desc"]],
});

var table_daily_stock = $("#fixedHeader2").DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    columns: [
        { data: "product_name" },
        { data: "out_mode" },
        { data: "out_total" },
        { data: "date" },
        { data: "created_by" },
        // { data: "qoh" },
    ],
    columnDefs: [{ type: "date", targets: 3 }],
    order: [[3, "desc"]],
});

$(function () {
    var start = moment();
    var end = moment();

    function cb(start, end) {
        $("#outgoing-date span").html(
            start.format("YYYY/MM/DD") + " - " + end.format("YYYY/MM/DD")
        );
    }

    $("#outgoing-date").daterangepicker(
        {
            startDate: moment().startOf("month"),
            endDate: moment().endOf("month"),
            maxDate: end,
            autoUpdateInput: true,
            locale: {
                format: "YYYY/MM/DD",
            },
            ranges: {
                Today: [moment(), moment()],
                Yesterday: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Last 7 Days": [moment().subtract(6, "days"), moment()],
                // 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
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

$(document).ready(function () {
    $("#tbody").show();
    $("#detailedTable").hide();
});

$("#category_id").on("change", function () {
    var selectedValue = $(this).val();
    if (selectedValue == "1") {
        $("#tbody").show();
        $("#detailedTable").hide();
    } else {
        $("#tbody").hide();
        $("#detailedTable").show();
    }
});

$("#outgoing-date").on("change", function () {
    var date = $(this).val();
    outgoingFilter(date);
});

// outgoing stock filter ajax call
function outgoingFilter(dates) {
    var parts = dates.split(" - ");
    var from = moment(parts[0], "YYYY/MM/DD").format("YYYY-MM-DD");
    var to = moment(parts[1], "YYYY/MM/DD").format("YYYY-MM-DD");

    var ajaxurl = config.routes.ledgerShow;
    $("#loading").show();
    $.ajax({
        url: ajaxurl,
        type: "get",
        dataType: "json",
        data: {
            date_from: from,
            date_to: to,
        },
        success: function (response) {
            // console.log("Response is:", response);
            bindData(response.summary);
            bindStockCountData(response.detailed);
        },
        complete: function () {
            $("#loading").hide();
        },
    });
}

function bindData(data) {
    const filteredData = data.filter((item) => Number(item.out_total) > 0);

    filteredData.forEach((item) => {
        // item.qoh = Number(item.current_stock).toFixed(0);
        item.out_total = numberWithCommas(item.out_total);
        item.product_name =
            (item.product.name ? item.product.name : "") +
            " " +
            (item.product.brand ? item.product.brand : "") +
            " " +
            (item.product.pack_size ? item.product.pack_size : "") +
            (item.product.sales_uom ? item.product.sales_uom : "");
    });
    // console.log("Binding data:", filteredData);
    summary_table.clear();
    summary_table.rows.add(filteredData);
    summary_table.draw();
}

function bindStockCountData(data) {
    // flatten movement
    let flattened = [];
    data.forEach((item) => {
        // console.log('Detailed',item);
        if (item.movement && item.movement.length > 0) {
            item.movement.forEach((mv) => {
                flattened.push({
                    product_name:
                        item.product_name +
                        " " +
                        (item.product.brand ?? "") +
                        " " +
                        (item.product.pack_size ?? "") +
                        (item.product.sales_uom ?? ""),
                    out_mode: mv.out_mode || "",
                    out_total: numberWithCommas(mv.qty),
                    date: mv.date,
                    created_by: mv.created_by || "",
                    // qoh:
                    //     item.current_stock_batch != null
                    //         ? Number(item.current_stock_batch).toFixed(0)
                    //         : 0,
                });
            });
        }
    });

    let grouped = {};
    flattened.forEach((item) => {
        const key = `${item.product_name}|${item.out_mode}|${item.date}`;
        if (!grouped[key]) {
            grouped[key] = { ...item };
        } else {
            grouped[key].out_total = numberWithCommas(
                Number(grouped[key].out_total) + Number(item.out_total)
            );
        }
    });

    let result = Object.values(grouped);

    // sort by date descending
    result.sort((a, b) => new Date(b.date) - new Date(a.date));

    // clear & bind
    table_daily_stock.clear();
    table_daily_stock.rows.add(result);
    table_daily_stock.draw();
}

$(document).ready(function () {
    var savedCategory = localStorage.getItem("category_id");
    if (savedCategory !== null) {
        $("#category_id").val(savedCategory);
    }

    // Trigger change once to load the table using saved values
    $("#category_id").trigger("change");
});

$(document).on("change", "#category_id", function () {
    localStorage.setItem("category_id", $("#category_id").val());
});

function numberWithCommas(digit) {
    return String(parseFloat(digit))
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
