function checkStock() {
    $.ajax({
        url: config.routes.task,
        type: "get",
        dataType: "json",
        success: function (data) {
            $('#span_counter').empty();
            $('#span_counter').append(data.length);
            $("#notification li").remove();
            data.forEach(function (index, item) {
                var notifications = JSON.parse(index.data);
                $('#notification').append($('<li><b>OutofStock -</b> <span class="text-c-red">' + notifications['data'][0] + '</span>; <b>Expired -</b> <span class="text-c-red">' + notifications['data'][1] + '</span></li>'));
            });
        },
        complete: function () {

        }
    });
}
