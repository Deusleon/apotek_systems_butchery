var cart = [];
var default_cart = [];
var order_cart = [];
var option_data;

var t = 0;

var cart_table = $('#cart_table').DataTable({
    searching: false,
    bPaginate: false,
    bInfo: false,
    bSort: false,
    order: [[0, 'desc']],
    language: {
        'emptyTable': "Add products to transfer"
    },
    data: cart,
    columns: [
        {title: "Product Name"},
        {
            title: "Quantity on Hand", render: function (num) {
                return numberWithCommas(num);
            }
        },
        {title: "Transfer Quantity"},
        {title: "Product Id"},
        {title: "Stock Id"},
        {
            title: "Action", defaultContent: "<div>" +
                "<input type='button' value='Edit' id='edit_btn' class='btn btn-info btn-rounded btn-sm'/>" +
                "<input type='button' value='Delete' id='delete_btn' class='btn btn-danger btn-rounded btn-sm'/>" +
                "</div>"
        }
    ],
    "columnDefs": [
        {
            "targets": [3],
            "visible": false,
            "searchable": false
        }, {
            "targets": [4],
            "visible": false,
            "searchable": false
        }
    ]
});


function calculateCart() {
    if (cart.length === 0) {
        $('#from_id').prop('disabled', false);
    } else {
        $('#from_id').prop('disabled', true);
    }

    var total = 0;
    var order_cart = [];

    var stringified_cart;
    if (cart[0]) {

        var reduced__obj_cart = {}, incremental_cart;

        for (var i = 0, c; c = cart[i]; ++i) {

            if (undefined === reduced__obj_cart[c[0]]) {
                reduced__obj_cart[c[0]] = c;
            } else {
                reduced__obj_cart[c[0]][2] = Number(reduced__obj_cart[c[0]][2].toString().replace(',', '')) + Number(c[2]);

                if (reduced__obj_cart[c[0]][2] > Number(c[1])) {
                    reduced__obj_cart[c[0]][2] = Number(c[1]);
                }

                reduced__obj_cart[c[0]][2] = numberWithCommas(reduced__obj_cart[c[0]][2]);

            }
        }

        incremental_cart = Object.keys(reduced__obj_cart).map(function (val) {
            return reduced__obj_cart[val]
        });

        cart = incremental_cart;
        cart.forEach(function (item, index, arr) {
            var sale_products = {};
            sale_products.quantity = item[2];
            sale_products.product_id = item[3];
            sale_products.stock_id = item[4];
            sale_products.quantityIn = item[1];
            if (!((Number(item[2].toString().replace(',', '')) > Number(item[1])) || isNaN(item[2].toString().replace(',', '')))) {
                sale_products.quantityTran = item[2].toString().replace(',', '');
            }
            if (isNaN(item[2].toString().replace(',', ''))) {
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
    $('#select_id').append($('<option>', {
        value: '',
        text: 'Select Product',
        selected: true
    }));
    $.each(option_data, function (id, detail) {

        var datas = [detail.product.name, detail.quantity, detail.product_id, detail.stock_id];

        $('#select_id').append($('<option>', {value: datas, text: detail.product.name}));
    });
}

$('#select_id').on('change', function () {
    val();
});

function val() {
    $('#edit_quantity').change();
    var item = [];
    var cart_data = [];
    var sale_product = {};
    var product = document.getElementById("select_id").value;
    // rePopulateSelect2();
    $('#select_id').val('');
    var selected_fields = product.split(',');
    var item_name = selected_fields[0];
    var price = Number(selected_fields[1]);
    var product_id = Number(selected_fields[2]);
    var stock_id = Number(selected_fields[3]);
    var quantity = 1;
    item.push(item_name);
    item.push(price);
    item.push(quantity);
    item.push(product_id);
    item.push(stock_id);
    cart_data.push(price);
    cart.push(item);
    default_cart.push(cart_data);
    calculateCart();
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();

}


function deselect() {
    calculateCart();
    cart = [];
    default_cart = [];
    var stringified_cart = JSON.stringify(cart);
    document.getElementById("order_cart").value = stringified_cart;
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
}

$('#cart_table tbody').on('click', '#edit_btn', function () {
    var quantity;
    if (t === 0) {
        /*not set then set it*/
        var row_data = cart_table.row($(this).parents('tr')).data();
        var index = cart_table.row($(this).parents('tr')).index();
        quantity = row_data[2].toString().replace(',', '');
        row_data[2] = "<div><input style='width: 50%' type='text' min='1' class='form-control' id='edit_quantity' onkeypress='return isNumberKey(event,this)' required/><span id='span_danger' style='display: none; color: red; font-size: 0.9em;'></span></div>";
        cart[index] = row_data;
        cart_table.clear();
        cart_table.rows.add(cart);
        cart_table.draw();
        document.getElementById("edit_quantity").value = quantity;

        t = 1;

    } else {
        $('#edit_quantity').change();
    }

});


//multiply quantity with amount....in stock transfer i don not need it
$('#cart_table tbody').on('change', '#edit_quantity', function () {
    t = 0;
    var row_data = cart_table.row($(this).parents('tr')).data();
    var index = cart_table.row($(this).parents('tr')).index();

    row_data[2] = numberWithCommas(document.getElementById("edit_quantity").value);

    if (Number(parseFloat(row_data[2].replace(/\,/g, ''), 10)) > Number(row_data[1])) {
        document.getElementById("edit_quantity").style.borderColor = 'red';
        document.getElementById("span_danger").style.display = 'block';
        $('#span_danger').text('Maximum quantity is ' + numberWithCommas(row_data[1]));
        row_data[2] = row_data[2];
        $('#transfer_preview').prop('disabled', true);
        return;
    }

    document.getElementById("span_danger").style.display = 'none';
    $('#transfer_preview').prop('disabled', false);

    cart[index] = row_data;
    calculateCart();
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
});

$('#cart_table tbody').on('click', '#delete_btn', function () {
    t = 0;
    var index = cart_table.row($(this).parents('tr')).index();
    cart.splice(index, 1);
    default_cart.splice(index, 1);
    calculateCart();
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
});

$('#deselect-all').on('click', function () {
    deselect();
});

$('#transfer').on('submit', function () {

    var check_cart = document.getElementById("order_cart").value;

    //if cart is empty, then dont submit form
    if (check_cart === '') {
        $('#from_id').prop('disabled', false);
        notify('Please complete transfer', 'top', 'right', 'warning');
        return false;
    }

    var check_cart_to_array;
    if (check_cart === 'undefined') {
        check_cart_to_array = [];
    } else {
        check_cart_to_array = JSON.parse(check_cart);
    }


    //check if cart is empty []
    if (!(check_cart_to_array && check_cart_to_array.length)) {
        notify('Transfer list is empty', 'top', 'right', 'warning');
        $('#from_id').prop('disabled', false);
        return false;
    }

    try {
        var from = document.getElementById("from_id");
        var from_id = from.options[from.selectedIndex].value;
        var to = document.getElementById("to_id");
        var to_id = to.options[to.selectedIndex].value

    } catch (e) {
        notify('Please select From and To store', 'top', 'right', 'warning');
        $('#from_id').prop('disabled', true);
        return false;
    }


    //check_cart if store are the same
    if (Number(from_id) === 0 && Number(to_id) === 0) {
        document.getElementById('from_danger').style.display = 'block';
        document.getElementById('to_danger').style.display = 'block';
        document.getElementById('border').style.borderColor = 'red';
        document.getElementById('to_border').style.borderColor = 'red';
        $('#from_id').prop('disabled', true);
        return false;
    }

    document.getElementById('from_danger').style.display = 'none';
    document.getElementById('to_danger').style.display = 'none';
    document.getElementById('border').style.borderColor = 'white';
    document.getElementById('to_border').style.borderColor = 'white';


    //check_cart the cart array if quantity tran is missing
    var tran = "quantityTran";

    for (var key in check_cart_to_array) {

        if (check_cart_to_array[key].hasOwnProperty(tran)) {
            //present
            if (check_cart_to_array[key][tran] === '') {
                notify('Minimum transfer quantity is 1', 'top', 'right', 'warning');
                $('#from_id').prop('disabled', true);
                return false;
            } else if (check_cart_to_array[key][tran] === Number(0)) {
                notify('Cannot transfer 0 quantity', 'top', 'right', 'warning');
                $('#from_id').prop('disabled', true);
                return false;
            }
        } else {
            //not present
            notify('Please check your transfer quantities', 'top', 'right', 'warning');
            $('#from_id').prop('disabled', true);
            return false;
        }

    }

    /*enable from select option*/
    $('#from_id').prop('disabled', false);


    // window.open('#', '_blank');
    // window.open(this.href, '_self');

});


$('#to_id').prop('disabled', true);
$('#select_id').prop('disabled', true);

function filterTransferByStore() {
    var from = document.getElementById('from_id');
    var from_id = from.options[from.selectedIndex].value;

    /*ajax filter by store*/
    $('#loading').show();
    $.ajax({
        url: config.routes.filterByStore,
        type: "get",
        dataType: "json",
        data: {
            from_id: from_id
        },
        success: function (data) {
            option_data = data;
            $('#to_id').prop('disabled', false);
            $('#select_id').prop('disabled', false);
            $("#select_id option").remove();
            $('#select_id').append($('<option>', {
                value: '',
                text: 'Select Product',
                selected: false
            }));
            $.each(data, function (id, detail) {

                var datas = [detail.product.name, detail.quantity, detail.product_id, detail.stock_id];

                $('#select_id').append($('<option>', {value: datas, text: detail.product.name}));
            });
        },
        complete: function () {
            $('#loading').hide();
        }
    });

}

$('#select_id').select2({
    language: {
        noResults: function () {
            var search_input = $("#select_id").data('select2').$dropdown.find("input").val();
            var from = document.getElementById('from_id');
            var from_id = from.options[from.selectedIndex].value;

            /*make ajax call for more*/
            $.ajax({
                url: config.routes.filterByWord,
                type: "get",
                dataType: "json",
                data: {
                    word: search_input,
                    from_id: from_id
                },
                success: function (data) {
                    option_data = data;
                    $("#select_id option").remove();
                    $('#select_id').append($('<option>', {
                        value: '',
                        text: 'Select Product'
                    }));
                    $.each(data, function (id, detail) {

                        var datas = [detail.product.name, detail.quantity, detail.product_id, detail.stock_id];

                        $('#select_id').append($('<option>', {value: datas, text: detail.product.name}));
                    });
                }
            });
        }
    }
});


function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
    try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
        console.log(e)
    }
}

function numberWithCommas(digit) {
    return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function isNumberKey(evt, obj) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    var value = obj.value;
    var dotcontains = value.indexOf(".") !== -1;
    if (dotcontains)
        if (charCode === 46) return false;
    if (charCode === 46) return true;
    return !(charCode > 31 && (charCode < 48 || charCode > 57));

}



