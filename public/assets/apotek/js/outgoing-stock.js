var summary_table = $("#fixedHeader").DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    columns: [{ data: "product_name" }, { data: "out_total" }, { data: "qoh" }],
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
        { data: "batch_number" },
        { data: "date" },
        { data: "qoh" },
    ],
    columnDefs: [{ type: "date", targets: 4 }],
    order: [[4, "desc"]],
});

$(function () {
    var start = moment();
    var end = moment();

    function cb(start, end) {
        $("#outgoing-date span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
    }

    $("#outgoing-date").daterangepicker(
        {
            startDate: moment().startOf("month"),
            endDate: moment().endOf("month"),
            maxDate: end,
            autoUpdateInput: true,
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

// outgoing stock filter ajax call
function outgoingFilter(dates) {
    var ajaxurl = config.routes.ledgerShow;
    $("#loading").show();
    $.ajax({
        url: ajaxurl,
        type: "get",
        dataType: "json",
        data: {
            date: dates[1],
            date_from: dates[0],
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
        item.qoh = Number(item.current_stock).toFixed(0);
        item.out_total = Number(item.out_total).toFixed(0);
        item.product_name =
            item.product.name +
            " " +
            item.product.brand +
            " " +
            item.product.pack_size +
            item.product.sales_uom;
    });
    // console.log("Binding data:", filteredData);
    summary_table.clear();
    summary_table.rows.add(filteredData);
    summary_table.draw();
}

function bindStockCountData(data) {
    // flatten out_movements
    let flattened = [];
    data.forEach((item) => {
        if (item.out_movements && item.out_movements.length > 0) {
            item.out_movements.forEach((mv) => {
                flattened.push({
                    product_name:
                        item.product_name +
                        " " +
                        item.product.brand +
                        " " +
                        item.product.pack_size +
                        item.product.sales_uom,
                    out_mode: mv.out_mode || "",
                    out_total: mv.qty,
                    batch_number: item.batch_number || "",
                    date: mv.date,
                    qoh:
                        item.current_stock_batch != null
                            ? Number(item.current_stock_batch).toFixed(0)
                            : 0,
                });
            });
        }
    });

    let grouped = {};
    flattened.forEach((item) => {
        const key = `${item.product_name}|${item.out_mode}|${item.batch_number}|${item.date}`;
        if (!grouped[key]) {
            grouped[key] = { ...item };
        } else {
            grouped[key].out_total =
                Number(grouped[key].out_total) + Number(item.out_total);
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
