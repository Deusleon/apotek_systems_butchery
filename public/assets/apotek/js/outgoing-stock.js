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
        { data: "stock_id" },
        { data: "out_total" },
        { data: "qoh" },
    ],
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

//daily stock count date picker
$("#d_auto_8")
    .datepicker({
        todayHighlight: true,
        format: "yyyy-mm-dd",
        changeYear: true,
    })
    .on("change", function () {
        filterByDate();
        $(".datepicker").hide();
    })
    .attr("readonly", "readonly");

function getOutgoingDate() {
    var dates = document.querySelector("input[name=outgoing-date]").value;
    dates = dates.split("-");
    outgoingFilter(dates);
}

//daily stock count filter by date
function filterByDate() {
    var date = document.getElementById("d_auto_8").value;

    if (date == "") {
        return false;
    }

    var ajaxurl = config.routes.filterShow;

    $("#loading").show();
    $.ajax({
        url: ajaxurl,
        type: "get",
        dataType: "json",
        data: {
            date: date,
        },
        success: function (data) {
            bindStockCountData(data);
        },
        complete: function () {
            $("#loading").hide();
        },
    });
}

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
            console.log("Response is:", response);
            // document.getElementById('detailedTable').style.display = 'block';
            // document.getElementById('tbody').style.display = 'none';
            bindData(response.products);
            bindStockCountData(response.stocks);
        },
        complete: function () {
            $("#loading").hide();
        },
    });
}

function bindData(data) {
    // Filter tu zile items zenye out_total > 0
    const filteredData = data.filter(item => Number(item.out_total) > 0);

    filteredData.forEach((item) => {
        // Hesabu QoH kama ulivyo
        let qoh = Number(item.current_stock) - Number(item.in_total) + Number(item.out_total);
        item.qoh = Math.max(qoh, 0);
        item.out_total = Number(item.out_total).toFixed(0);
    });

    summary_table.clear();
    summary_table.rows.add(filteredData);
    summary_table.draw();
}

function bindStockCountData(data) {
    const filteredData = data.filter(item => Number(item.out_total) > 0);
    filteredData.forEach((item) => {
        let qoh =
            Number(item.current_stock) -
            Number(item.in_total) +
            Number(item.out_total);
        item.qoh = Math.max(qoh, 0);
    });
    console.log("Binding data:", filteredData);
    table_daily_stock.clear();
    table_daily_stock.rows.add(filteredData);
    table_daily_stock.draw();
}
