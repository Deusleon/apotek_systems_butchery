function checkStock() {
    $.ajax({
        url: config.routes.task,
        type: "get",
        dataType: "json",
        success: function (data) {

        },
        complete: function () {

        }
    });
}
