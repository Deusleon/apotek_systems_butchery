var cart = [];
var default_cart = [];
var received_cart = [];
var details = [];
var order_items = [];
var product_ids = 0;
var edit_btn_set = 0;
var selected_name;

var items_table = $('#items_table').DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    data: order_items,
    columns: [
        {title: "ProductID"},
        {title: "Product Name"},
        {
            title: "Quantity", render: function (data) {
                return numberWithCommas(data);
            }
        },
        {
            title: "Price", render: function (data) {
                return formatMoney(data);
            }
        },
        {
            title: "VAT", render: function (data) {
                return formatMoney(data);
            }
        },
        {
            title: "Amount", render: function (data) {
                return formatMoney(data);
            }
        },
        {title: "Supplier"},
        {title: "OrderItemId"},
        {
            title: "Action",
            defaultContent: "<input type='button' value='Receive' id='receive_btn' class='btn btn-warning btn-rounded btn-sm' size='2' />"
        }
    ]
});

var cart_table = $('#cart_table').DataTable({
    searching: false,
    bPaginate: false,
    bInfo: false,
    data: cart,
    columns: [
        {title: "Item Name"},
        {title: "Quantity"},
        {
            title: "Action",
            defaultContent: "<div><input type='button' value='Edit' id='edit_btn' class='btn btn-info btn-rounded btn-sm'/><input type='button' value='Delete' id='delete_btn' class='btn btn-danger btn-rounded btn-sm'/></div>"
        }
    ]
});

$('#cart_table tbody').on('click', '#edit_btn', function () {
    var quantity;
    if (edit_btn_set === 0) {
        var row_data = cart_table.row($(this).parents('tr')).data();
        var index = cart_table.row($(this).parents('tr')).index();
        quantity = row_data[1];
        row_data[1] = "<input style='width: 45%' type='number'class='form-control' id='edit_quantity' required/>";
        cart[index] = row_data;
        cart_table.clear();
        cart_table.rows.add(cart);
        cart_table.draw();
        document.getElementById("edit_quantity").value = quantity;

        edit_btn_set = 1;

    }

});


$('#cart_table tbody').on('change', '#edit_quantity', function () {
    edit_btn_set = 0;
    var row_data = cart_table.row($(this).parents('tr')).data();
    var index = cart_table.row($(this).parents('tr')).index();
    row_data[1] = document.getElementById("edit_quantity").value;
    item_received.quantity = row_data[1];
    cart[index] = row_data;
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();

    item_receiveds = JSON.stringify(item_received);
    document.getElementById("received_cart").value = item_receiveds;

});

$('#cart_table tbody').on('click', '#delete_btn', function () {
    edit_btn_set = 0;
    var index = cart_table.row($(this).parents('tr')).index();
    // valuesCollection();
    cart.splice(index, 1);
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
    document.getElementById("received_cart").value = JSON.stringify(cart);
    document.getElementById('buy_price').value = '0';
    document.getElementById('sell_price_id').value = '0';


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

function isNumberKey(evt, obj) {

    var charCode = (evt.which) ? evt.which : event.keyCode;
    var value = obj.value;
    var dotcontains = value.indexOf(".") !== -1;
    if (dotcontains)
        if (charCode === 46) return false;
    if (charCode === 46) return true;
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

$('#selected-product').on('change', function () {
    valuesCollection();
});


var item_received = {};

function valuesCollection(qty) {
    qty = (qty || 1);
    var item = [];
    var cart_data = [];
    product = document.getElementById("selected-product").value;
    document.getElementById("selected-product").value = '';
    var selected_fields = product.split('#@');
    var item_name = selected_fields[0];
    var product_id = selected_fields[1];
    /*set the global variable*/
    product_ids = product_id;
    selected_name = item_name;
    item_received.name = selected_fields[0];
    item_received.id = selected_fields[1];
    document.getElementById("pr_id").value = formatMoney(selected_fields[2]);

    var price_category = document.getElementById("price_category").value;
    var e = document.getElementById("supplier_ids");
    var supplier_id = e.options[e.selectedIndex].value;

    getPrice(product_id, price_category, supplier_id);
    item_received.quantity = qty;
    item.push(item_name);
    item.push(qty);
    cart[0] = item;
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();

    item_receiveds = JSON.stringify(item_received);
    document.getElementById("received_cart").value = item_receiveds;
}

$('#cancel-all').on('click', function () {
    deselect();
});

function getPrice(product_id, price_category, supplier_id) {

    $.ajax({
        url: config.routes.goodsreceiving,
        type: "get",
        dataType: "json",
        data: {
            'product_id': product_id,
            'price_category': price_category,
            'supplier_id': supplier_id
        },
        success: function (data) {
            // console.log(data)
            if (data.length === 0) {
                /*empty*/
                $("#sell_price_id").val(formatMoney('0'));
                $("#buy_price").val(formatMoney('0'));
            } else {
                $("#buy_price").val(formatMoney(data[0]['unit_cost']));
                $("#sell_price_id").val(formatMoney(data[0]['price']));
            }
        }

    });
}

function deselect() {
    edit_btn_set = 0;
    cart = [];
    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
    document.getElementById("received_cart").value = JSON.stringify(cart);

}

function retrieveInvoiceBySupplier(supplier_id) {
    $.ajax({
        url: config.routes.filterBySupplier,
        type: "get",
        dataType: "json",
        data: {
            supplier_id: supplier_id
        },
        success: function (data) {
            $('#invoice_ids').prop('disabled', false);
            $("#invoice_ids option").remove();
            $('#invoice_ids').append($('<option>', {
                value: '',
                text: 'Select Invoice...',
                disabled: true,
                selected: true
            }));
            $.each(data, function (id, detail) {
                var datas = [detail.id];

                $('#invoice_ids').append($('<option>', {value: datas, text: detail.invoice_no}));
            });
        },
        complete: function () {
            // $('#loading').hide();
        }
    });
}

function orderReceive(items, supplier) {

    retrieveInvoiceBySupplier(supplier);

    received = "<span class='badge badge-success'>Received</span>";
    var receive_cart = [];
    document.getElementById('purchases').style.display = 'none';
    order_items = [];
    items.forEach(function (item) {
        var item_data = [];
        item_data.push(item.product_id);
        item_data.push(item.name);
        item_data.push(item.quantity);
        item_data.push(item.price);
        item_data.push(item.vat);
        item_data.push(item.amount);
        if (supplier) {
            item_data.push(supplier);
            item_data.push(item.order_item_id);
            if (item.item_status === "Received") {
                item_data.push(received);
            }
            items_table.column(8).visible(true);
        } else {
            item_data.push('Preview');
            item_data.push(item.order_item_id);
            items_table.column(8).visible(false);
        }

        order_items.push(item_data);
    });

    items_table.clear();
    items_table.rows.add(order_items);
    items_table.columns([0, 6, 7]).visible(false);
    items_table.draw();
    document.getElementById('items').style.display = 'block';

    $('#cancel').on('click', function () {
        receive_cart = [];
        document.getElementById('purchases').style.display = 'block';
        document.getElementById('items').style.display = 'none';
    });

}

$('#items_table tbody').on('click', '#receive_btn', function () {
    var index = items_table.row($(this).parents('tr')).index();
    var data = items_table.row($(this).parents('tr')).data();
    $('#receive').modal('show');
    $('#receive').find('.modal-body #rec_qty').val(numberWithCommas(data[2]));
    $('#receive').find('.modal-body #name_of_item').val(data[1]);
    $('#receive').find('.modal-body #pr_id').val(formatMoney(data[3]));
    $('#receive').find('.modal-body #product-id').val(data[0]);
    $('#receive').find('.modal-body #order-item-id').val(data[7]);
    $('#receive').find('.modal-body #id_of_supplier').val(data[6]);
    var supplier_ids = data[7];
    var item_id = data[0];
    var e = document.getElementById("price_cat");
    var price_cat = e.options[e.selectedIndex].value;

    document.getElementById('save_btn').style.display = 'block';

//     function getPrice2(item_id, price_cat,supplier_ids) {
//     $.ajax({
//         url: configurations.routes.goodsreceiving,
//         type: "get",
//         dataType: "json",
//         data: {
//             'item_id': item_id,
//             'price_cat': price_cat,
//             'supplier_ids':supplier_ids
//         },

//         // success: function (data) {
//         //     if (data.length === 0) {
//         //         /*empty*/
//         //         $("#sell_price_i").val(formatMoney('0'));
//         //     } else {
//         //         $("#pr_id").val(formatMoney(data[0]['unit_cost']));
//         //         $("#sell_price_i").val(formatMoney(data[0]['price']));
//         //     }
//         // }

//     });
// }

    $('#receive').on('change', '#rec_qty', function () {
        var quantity_ = document.getElementById('rec_qty').value;
        var quantity = parseFloat(quantity_.replace(/\,/g, ''), 10);
        if (quantity > data[2]) {
            document.getElementById('save_btn').disabled = 'true';
            document.getElementById('qty_error').style.display = 'block';
            $('#receive').find('.modal-body #qty_error').text('The maximum quantity is ' + numberWithCommas(data[2]));
        } else {
            document.getElementById('qty_error').style.display = 'none';
            $('#save_btn').prop('disabled', false);
        }
    });

});

function orderamountCheck() {

    var unit_price = document.getElementById('pr_id').value;
    var sell_price = document.getElementById('sell_price_i').value;
    unit_price_parse = (parseFloat(unit_price.replace(/\,/g, ''), 10));
    sell_price_parse = (parseFloat(sell_price.replace(/\,/g, ''), 10));

    document.getElementById('sell_price_i').value = formatMoney(parseFloat(sell_price.replace(/\,/g, ''), 10));
    document.getElementById('pr_id').value = formatMoney(parseFloat(unit_price.replace(/\,/g, ''), 10));


    if (Number(sell_price_parse) < Number(unit_price_parse)) {

        $('#save_btn').prop('disabled', true);
        $('div.amount_error').text('Cannot be less than Buy Price');
    } else if (sell_price_parse == unit_price_parse) {
        $('#save_btn').prop('disabled', true);
        $('div.amount_error').text('Cannot be equal to Buy Price');
    } else {

        $('#save_btn').prop('disabled', false);
        $('div.amount_error').text('');

    }
}

function priceByCategory() {

    if (product_ids !== 0) {
        var price_category = document.getElementById("price_category").value;
        getPrice(product_ids, price_category);

    } else {
        return false;
    }

}

$('#invoice_id').prop('disabled', true);

function filterInvoiceBySupplier() {
    var supplier = document.getElementById('supplier_ids');
    var supplier_id = supplier.options[supplier.selectedIndex].value;

    /*ajax filter invoice by supplier*/
    $.ajax({
        url: config.routes.filterBySupplier,
        type: "get",
        dataType: "json",
        data: {
            supplier_id: supplier_id
        },
        success: function (data) {
            $('#invoice_id').prop('disabled', false);
            $("#invoice_id option").remove();
            $('#invoice_id').append($('<option>', {
                value: '',
                text: 'Select Invoice...',
                disabled: true,
                selected: true
            }));
            $.each(data, function (id, detail) {
                var datas = [detail.id];

                $('#invoice_id').append($('<option>', {value: datas, text: detail.invoice_no}));
            });
        },
        complete: function () {
            // $('#loading').hide();
        }
    });

}

$('#myFormId').on('submit', function (e) {
    e.preventDefault();

    var cart_data = document.getElementById("received_cart").value;

    if (cart_data === ''){
        notify('Item receive list is empty', 'top', 'right', 'warning');
        return false;
    }

    var item_cart = JSON.parse(cart_data);

    if (item_cart.length === 0) {
        notify('Item receive list is empty', 'top', 'right', 'warning');
        return false;
    }

    saveInvoiceForm();

});

function saveInvoiceForm() {
    var form = $('#myFormId').serialize();

    $.ajax({
        url: config.routes.itemFormSave,
        type: "get",
        dataType: "json",
        cache: "false",
        data: form,
        success: function (data) {
            if (data[0].message === 'success') {
                notify('Item received successfully', 'top', 'right', 'success');
                document.getElementById('buy_price').value = '0';
                document.getElementById('sell_price_id').value = '0';
                var today_date = moment().toDate();
                document.getElementById('expire_date_21').value = moment(today_date).format('D-M-YYYY');
                deselect();
            } else {
                notify('Item name exists', 'top', 'right', 'danger');
                document.getElementById('buy_price').value = '0';
                document.getElementById('sell_price_id').value = '0';
                deselect();
            }
        }
    });
}

$('#order_reveice').on('submit', function () {
    var unit_price = document.getElementById('pr_id').value;
    var sell_price = document.getElementById('sell_price_i').value;
    var unit_price_parse = (parseFloat(unit_price.replace(/\,/g, ''), 10));
    var sell_price_parse = (parseFloat(sell_price.replace(/\,/g, ''), 10));

    if (sell_price_parse === 0 || unit_price_parse === 0) {
        notify('Item cannot have 0 price', 'top', 'right', 'warning');
        return false;
    }

});

function toCommas(number) {
    document.getElementById('rec_qty').value = numberWithCommas(number);
}

function numberWithCommas(digit) {
    return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

