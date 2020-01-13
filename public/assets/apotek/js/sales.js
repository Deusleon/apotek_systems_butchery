var cart = [];//for data displayed.
var default_cart = [];//for default values.
var details = [];
var sale_items = [];
var edit_btn_set = 0;
var category_option = document.getElementById('category').value;
var customer_option = document.getElementById('customers').value;


var tax = Number(document.getElementById("vat").value);


var items_table = $('#items_table').DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    data: sale_items,
    columns: [
        {title: "ID"},
        {title: "Product Name"},
        {
            title: "Quantity", render: function (data) {
                return numberWithCommas(data);
            }
        },
        {
            title: "Price", render: function (Price) {
                return formatMoney(Price);
            }
        },
        {
            title: "VAT", render: function (vat) {
                return formatMoney(vat);
            }
        },
        {
            title: "Discount", render: function (discount) {
                return formatMoney(discount);
            }
        },
        {
            title: "Amount", render: function (amount) {
                return formatMoney(amount);
            }
        },
        {
            title: "Action",
            defaultContent: "<input type='button' value='Return' id='rtn_btn' class='btn btn-primary btn-rounded btn-sm'/>"
        }
    ]
});
var cart_table = $('#cart_table').DataTable({
    searching: false,
    bPaginate: false,
    bInfo: false,
    ordering: false,
    data: cart,
    columns: [
        {title: "Product Name"},
        {title: "Quantity"},
        {title: "Price"},
        {title: "VAT"},
        {title: "Amount"},
        {title: "Stock Qty"},
        {title: "productID"},
        {
            title: "Action",
            defaultContent: "<div><input type='button' value='Edit' id='edit_btn' class='btn btn-info btn-rounded btn-sm'/><input type='button' value='Delete' id='delete_btn' class='btn btn-danger btn-rounded btn-sm'/></div>"
        }
    ]
});
cart_table.columns([5, 6]).visible(false);//this columns are just for manipulations
var details_table = $('#details_table').DataTable({
    searching: true,
    bPaginate: false,
    bInfo: true,
    data: details,
    columns: [
        {title: "Product Name"},
        {title: "Quantity"},
        {title: "price"},
        {title: "VAT"},
        {title: "Amount"}
    ]
});
var credit_payment_table = $('#credit_payment_table').DataTable({
    searching: true,
    bPaginate: true,
    bInfo: true,
    columns: [
        {data: 'receipt_number'},
        {data: 'name'},
        {
            data: 'date', render: function (date) {
                return moment(date).format('D-M-YYYY');
            }
        },
        {
            data: 'total_amount', render: function (total_amount) {
                return formatMoney(total_amount);
            }
        },
        {
            data: 'paid_amount', render: function (paid_amount) {
                return formatMoney(paid_amount);
            }
        },
        {
            data: 'balance', render: function (balance) {
                return formatMoney(balance);
            }
        },
        {
            data: "action",
            defaultContent: "<button type='button' id='pay_btn' class='btn btn-sm btn-rounded btn-primary'>Pay</button>"
        }
    ]
});

var sale_history_table = $('#sale_history_table').DataTable({
    bPaginate: true,
    bInfo: true,
    ordering: false,
// dom: 't',
    columns: [
        {data: 'receipt_number'},
        {
            data: 'date', render: function (date) {
                return moment(date).format('D-M-YYYY');
            }
        },
        {data: 'cost.name'},
        {
            data: 'cost', render: function (cost) {
                return formatMoney(((cost.amount - cost.discount) / (1 + (cost.vat / cost.sub_total))));
            }
        },

        {
            data: 'cost', render: function (cost) {
                return formatMoney(((cost.amount - cost.discount) * (cost.vat / cost.sub_total)));
            }
        },
        {
            data: 'cost.discount', render: function (discount) {
                return formatMoney(discount);
            }
        },
        {
            data: 'cost', render: function (cost) {
                return formatMoney(((cost.amount - cost.discount)));
            }
        },
        {
            data: "action",
            defaultContent: "<button type='button' id='sale_details' class='btn btn-sm btn-rounded btn-success'>Show</button><button type='submit' id='sale_receipt_reprint' class='btn btn-sm btn-rounded btn-secondary'><span class='fa fa-print' aria-hidden='true'></span>Print</button>"
        }
    ]
});
var sale_list_Table = $('#sale_list_Table').DataTable({
    order: [[1, "desc"]],
    dom: 't',
    bPaginate: false,
    bInfo: true,
    fixedHeader: true,
});

$('#searching').on('keyup', function () {
    sale_history_table.search(this.value).draw();
});
$("#products").on('change', function () {
    valueCollection();
});
$("#customer").on('change', function () {
    discount();
});
$('#cart_table tbody').on('click', '#edit_btn', function () {
    var quantity;
    if (edit_btn_set === 0) {
        var row_data = cart_table.row($(this).parents('tr')).data();

        var index = cart_table.row($(this).parents('tr')).index();
        quantity = row_data[1].toString().replace(',', '');
        row_data[1] = "<input type='text' min='1' class='form-control' id='edit_quantity' required  onkeypress='return isNumberKey(event,this)'>";
        cart[index] = row_data;
        cart_table.clear();
        cart_table.rows.add(cart);
        cart_table.draw();

        var quantity_ = quantity.split('<');

        document.getElementById("edit_quantity").value = quantity_[0];

        edit_btn_set = 1;

    } else {
        // document.getElementById("edit_quantity").value
        $('#edit_quantity').change();
    }
});


$('#cart_table tbody').on('change', '#edit_quantity', function () {
    edit_btn_set = 0;
    var row_data = cart_table.row($(this).parents('tr')).data();
    var index = cart_table.row($(this).parents('tr')).index();

    row_data[1] = numberWithCommas(document.getElementById("edit_quantity").value);

    // row_data[1] = Number((document.getElementById("edit_quantity").value));
    if (Number(parseFloat(row_data[1].replace(/\,/g, ''), 10)) < 1) {
        row_data[1] = 1
    }
    dif = row_data[5] - row_data[1].toString().replace(/,/g, '');

    if ($('#quotes_page').length) {//Qoutes has no maximum quantity
        row_data[2] = formatMoney(parseFloat(default_cart[index][0].replace(/\,/g, ''), 10));
        row_data[3] = formatMoney(parseFloat(default_cart[index][1].replace(/\,/g, ''), 10) * row_data[1].toString().replace(',', ''));
        row_data[4] = formatMoney(parseFloat(default_cart[index][2].replace(/\,/g, ''), 10) * row_data[1].toString().replace(',', ''));
    } else if (dif < 0) {
        row_data[1] = row_data[5];
        row_data[2] = formatMoney(parseFloat(default_cart[index][0].replace(/\,/g, ''), 10));
        row_data[3] = formatMoney(parseFloat(default_cart[index][1].replace(/\,/g, ''), 10) * row_data[5]);
        row_data[4] = formatMoney(parseFloat(default_cart[index][2].replace(/\,/g, ''), 10) * row_data[5]);
        row_data[1] = numberWithCommas(row_data[5]) + " " + "<span class='text text-danger'>Max</span>";

    } else {
        row_data[2] = formatMoney(parseFloat(default_cart[index][0].replace(/\,/g, ''), 10));
        row_data[3] = formatMoney(parseFloat(default_cart[index][1].replace(/\,/g, ''), 10) * row_data[1].toString().replace(',', ''));
        row_data[4] = formatMoney(parseFloat(default_cart[index][2].replace(/\,/g, ''), 10) * row_data[1].toString().replace(',', ''));
    }//replace the quantity with max stock qty available

    cart[index] = row_data;
    discount();
});

$('#cart_table tbody').on('click', '#delete_btn', function () {
    edit_btn_set = 0;
    var index = cart_table.row($(this).parents('tr')).index();
    var price = parseFloat(cart[index][2].replace(/\,/g, ''), 10);
    var unit_total = parseFloat(cart[index][4].replace(/\,/g, ''), 10);
    cart.splice(index, 1);
    default_cart.splice(index, 1);
    discount();
});

$('#deselect-all').on('click', function () {
    edit_btn_set = 0;
    var cart_data = document.getElementById("order_cart").value;
    if (!(cart_data === '' || cart_data === 'undefined')) {
        var r = confirm('Cancel sale?');
        if (r === true) {
            /*continue*/
            deselect();
        } else {
            /*return false*/
            return false;
        }
    }
    // deselect();
});

$('#deselect-all-credit-sale').on('click', function () {
    edit_btn_set = 0;
    var cart_data = document.getElementById("order_cart").value;
    if (!(cart_data === '' || cart_data === 'undefined')) {
        var r = confirm('Cancel credit sale?');
        if (r === true) {
            /*continue*/
            deselect1();
        } else {
            /*return false*/
            return false;
        }
    }
    // deselect();
});

$('#deselect-all-quote').on('click', function () {
    edit_btn_set = 0;
    var cart_data = document.getElementById("order_cart").value;
    if (!(cart_data === '' || cart_data === 'undefined')) {
        var r = confirm('Cancel sale quote?');
        if (r === true) {
            /*continue*/
            deselectQuote();
        } else {
            /*return false*/
            return false;
        }
    }
    // deselect();
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
    }
}

function discount() {
    var dis = (document.getElementById("sale_discount").value);
    sale_discount = (parseFloat(dis.replace(/\,/g, ''), 10) || 0);
    var sub_total, total_vat, total = 0;
    if (cart[0]) {
        var result = [];
        var order_cart = [];//for data sent into database.
        cart.reduce(function (reducedCart, value) {
            if (!reducedCart[value[6]]) {
                reducedCart[value[6]] = value;
                result.push(reducedCart[value[6]])
            } else {
                p = parseFloat(reducedCart[value[6]][2].replace(/\,/g, ''), 10);

                reducedCart[value[6]][1] = reducedCart[value[6]][1].toString();

                reducedCart[value[6]][1] = reducedCart[value[6]][1].split('<');

                reducedCart[value[6]][1] = Number(reducedCart[value[6]][1][0].toString().replace(',', ''));

                if (typeof reducedCart[value[6]][1] != 'number') {
                    reducedCart[value[6]][1] = reducedCart[value[6]][5] //avoid Max string
                }
                reducedCart[value[6]][1] += value[1];
                dif = reducedCart[value[6]][5] - reducedCart[value[6]][1];
                if ($('#quotes_page').length) {//Qoutes has no maximum quantity
                    reducedCart[value[6]][2] = formatMoney(p);
                    reducedCart[value[6]][3] = formatMoney(p * reducedCart[value[6]][1] * tax);
                    reducedCart[value[6]][4] = formatMoney(p * reducedCart[value[6]][1] * (1 + tax));
                    reducedCart[value[6]][1] = numberWithCommas(reducedCart[value[6]][1]);

                } else if (dif < 0) {
                    reducedCart[value[6]][1] = reducedCart[value[6]][5];
                    reducedCart[value[6]][2] = formatMoney(p);
                    reducedCart[value[6]][3] = formatMoney(p * reducedCart[value[6]][5] * tax);
                    reducedCart[value[6]][4] = formatMoney(p * reducedCart[value[6]][5] * (1 + tax));
                    reducedCart[value[6]][1] = numberWithCommas(reducedCart[value[6]][1]) + " " + "<span class='text text-danger'>Max</span>";
                } else {
                    reducedCart[value[6]][2] = formatMoney(p);
                    reducedCart[value[6]][3] = formatMoney(p * reducedCart[value[6]][1] * tax);
                    reducedCart[value[6]][4] = formatMoney(p * reducedCart[value[6]][1] * (1 + tax));
                    reducedCart[value[6]][1] = numberWithCommas(reducedCart[value[6]][1]);

                }//replace the quantity with max qty on stock


            }
            return reducedCart;
        }, []);
        cart = result;
        var price_category = document.getElementById("price_category").value;
        $('#price_category').prop('disabled', true);
        document.getElementById('cat_label').style.color = 'red';
        cart.forEach(function (item, index, arr) {

            var bought_product = {};
            bought_product.price = parseFloat(item[2].replace(/\,/g, ''), 10);

            if (typeof item[1] != 'number') {
                if (isNaN(Number(item[1].toString().replace(',', '')))) {
                    bought_product.quantity = item[5]; //avoid Max string
                } else {
                    bought_product.quantity = numberWithCommas(item[1].toString().replace(',', '')); //avoid Max string
                }
            } else {
                bought_product.quantity = item[1];

            }
            bought_product.amount = parseFloat(item[4].replace(/\,/g, ''), 10);
            bought_product.product_id = item[6];
            sub_total += bought_product.price;
            total_vat += parseFloat(item[3].replace(/\,/g, ''), 10);
            total += bought_product.amount;
            order_cart.push(bought_product);
        });
//SUBTOTAL WITH DISCOUNT
        total -= sale_discount;
        sub_total = total / (1 + tax);
        total_vat = total - sub_total;
    } else {
        $('#price_category').prop('disabled', false);
        document.getElementById('cat_label').style.color = 'black';
    }

    if (total < 0) {
        document.getElementById('save_btn').disabled = 'true';
        document.getElementById('discount_error').style.display = 'block';
    } else {
        document.getElementById('discount_error').style.display = 'none';
        $('#save_btn').prop('disabled', false);
    }

//Change Calculator
    var change = 0;
    var paid = document.getElementById("sale_paid").value;
    sale_paid_amount = (parseFloat(paid.replace(/\,/g, ''), 10) || 0);
    change = sale_paid_amount - total;

//Credit Sales
    var customer;
    var max_credit;
    var balance;
    if ($('#credit_sale').length) {
        var customer_x = document.getElementById("customer").value;
        if (customer_x === '') {
            customer = JSON.parse('{}');
        } else {
            customer = JSON.parse(document.getElementById("customer").value);
        }

        max_credit = ((customer.credit_limit - customer.total_credit) || 0);
        balance = total - sale_paid_amount;
        if (balance > max_credit) {
            document.getElementById('save_btn').disabled = 'true';
            $('div.credit_max').text(formatMoney(max_credit)).css({"font-weight": "Bold", "color": "red"});
        } else {
            $('#save_btn').prop('disabled', false);
            $('div.credit_max').text(formatMoney(max_credit)).css({"font-weight": "Bold", "color": "green"});
        }
        document.getElementById("paid_value").value = sale_paid_amount;
        $('div.sub-total').text(formatMoney(sub_total)).css("font-weight", "Bold");
        $('div.tax-amount').text(formatMoney(total_vat)).css("font-weight", "Bold");
        $('div.total-amount').text(formatMoney(total)).css("font-weight", "Bold");
        $('div.balance-amount').text(formatMoney(balance)).css("font-weight", "Bold");
    } else {
        document.getElementById('change_amount').value = formatMoney(change);
    }


    stringified_cart = JSON.stringify(order_cart);
    document.getElementById("order_cart").value = stringified_cart;
    document.getElementById("price_cat").value = price_category;
    document.getElementById("discount_value").value = sale_discount;
    document.getElementById("total").value = formatMoney(total);
    document.getElementById("sub_total").value = formatMoney(sub_total);
    var t = document.getElementById("total").value;
    var st = document.getElementById("sub_total").value;

    document.getElementById("total_vat").value =
        formatMoney(parseFloat(t.replace(/\,/g, ''), 10) - parseFloat(st.replace(/\,/g, ''), 10));

    cart_table.clear();
    cart_table.rows.add(cart);
    cart_table.draw();
}


function valueCollection() {
    $('#edit_quantity').change();
    var item = [];
    var cart_data = [];
    var bought_product = {};
    product = document.getElementById("products").value;
    document.getElementById("products").value = "";
    if (product) {
        var selected_fields = product.split('#@');
        var item_name = selected_fields[0];
        var price = Number(selected_fields[1]);
        var productID = Number(selected_fields[2]);
        var qty = Number(selected_fields[3]);
        var vat = Number((price * tax).toFixed(2));
        var unit_total = Number(price + vat);
        var quantity = 1;
        item.push(item_name);
        item.push(quantity);
        item.push(formatMoney(price));
        item.push(formatMoney(vat));
        item.push(formatMoney(unit_total));
        item.push(qty);
        item.push(productID);
        cart_data.push(formatMoney(price));
        cart_data.push(formatMoney(vat));
        cart_data.push(formatMoney(unit_total));
        cart.push(item);
        default_cart.push(cart_data);
        discount();


    }

}


function deselect() {
    // document.getElementById("sales_form").reset();
    // rePopulateSelect2();
    // rePopulateSelect2Customer();
    $('#customer_id').val('').change();
    document.getElementById('sale_discount').value = 0.0;
    document.getElementById('sub_total').value = 0.0;
    document.getElementById('total_vat').value = 0.0;
    document.getElementById('total').value = 0.0;
    document.getElementById('sale_paid').value = 0.0;
    document.getElementById('change_amount').value = 0.0;

    sub_total = 0;
    total = 0;
    cart = [];
    order_cart = [];
    default_cart = [];
    discount();
}

function deselect1() {
    document.getElementById("credit_sales_form").reset();
    sub_total = 0;
    total = 0;
    cart = [];
    order_cart = [];
    default_cart = [];
    discount();
}

function deselectQuote() {
    document.getElementById("quote_sale_form").reset();
    sub_total = 0;
    total = 0;
    cart = [];
    order_cart = [];
    default_cart = [];
    discount();
}

function rePopulateSelect2() {
    $("#price_category option").remove();
    $('#price_category').append($('<option>', {
        value: '',
        text: 'Select Type',
        selected: true,
        disabled: true
    }));
    $.each(JSON.parse(category_option), function (id, detail) {
        var datas = [detail.id];
        $('#price_category').append($('<option>', {value: datas, text: detail.name}));
    });
}

function rePopulateSelect2Customer() {
    $("#customer_id option").remove();
    $('#customer_id').append($('<option>', {
        value: '',
        text: 'Select Customer',
        selected: true,
        disabled: true
    }));
    $.each(JSON.parse(customer_option), function (id, detail) {
        var datas = [detail.id];
        $('#customer_id').append($('<option>', {value: datas, text: detail.name}));
    });
}


function saleReturn(items, sale_id) {
    var return_cart = [];
    var id = sale_id;
    localStorage.setItem("id", id);
    document.getElementById('sales').style.display = 'none';
    sale_items = [];
    returned = " <span class='badge badge-warning'>Partial</span>";
    pending = " <span class='badge badge-secondary'>Pending</span>";
    rejected = " <span class='badge badge-danger'>Rejected</span>";
    items.forEach(function (item) {
        var item_data = [];
        if (item.status !== 3) {
            item_data.push(item.id);
            item_data.push(item.name);
            item_data.push(item.quantity);
            item_data.push(item.price);
            item_data.push(item.vat);
            item_data.push(item.discount);
            item_data.push(item.amount);
            if (item.status === 2) {
                item_data.push(pending)
            }
            if (item.status === 4) {
                item_data.push(rejected)
            }
            if (item.status === 5) {
                item_data.push(returned)
            }
            sale_items.push(item_data);
        }


    });
    items_table.clear();
    items_table.rows.add(sale_items);
    items_table.column(0).visible(false);
    items_table.draw();
    document.getElementById('items').style.display = 'block';

    $('#cancel').on('click', function () {
        return_cart = [];
        localStorage.removeItem("id");
        document.getElementById('sales').style.display = 'block';
        document.getElementById('items').style.display = 'none';
    });

}

function quoteDetails(remark, items) {
    $('div.quote_remark').text(remark);
    action = "<input type='button' value='Sale' id='sale_btn' class='btn btn-primary btn-rounded btn-sm'/>";
    sale_items = [];
    items.forEach(function (item) {
        var item_data = [];
        item_data.push(item.id);
        item_data.push(item.name);
        item_data.push(item.quantity);
        item_data.push(item.price / item.quantity);//unit price
        item_data.push(item.vat);
        item_data.push(item.discount);
        item_data.push(item.amount);
        item_data.push(action);//this is not used now (Just an Idea for quote to sale conversion)
        sale_items.push(item_data);
    });
    items_table.clear();
    items_table.rows.add(sale_items);
    items_table.column(7).visible(false);
    items_table.column(0).visible(false);
    items_table.draw();
}

$('#sale_quotes-Table tbody').on('click', '#quote_details', function () {
    $('#quote-details').modal('show');
});

$('#sale_history_table tbody').on('click', '#sale_details', function () {
    var row_data = sale_history_table.row($(this).parents('tr')).data();
    $('#sale-details').modal('show');
    var items = row_data.details;
    sold = " <span class='badge badge-success'>Sold</span>";
    pending = " <span class='badge badge-secondary'>Pending</span>";
    returned = " <span class='badge badge-danger'>Returned</span>";
    sale_items = [];
    items.forEach(function (item) {
        var item_data = [];
        item_data.push(item.id);
        item_data.push(item.name);
        item_data.push(item.quantity);
        item_data.push((item.price / item.quantity));
        item_data.push(item.vat);
        item_data.push(item.discount);
        item_data.push(item.amount);
        if (item.status == 2) {
            item_data.push(pending)
        } else if (item.status == 3) {
            item_data.push(returned)
        } else {
            item_data.push(sold)
        }
        sale_items.push(item_data);
    });
    items_table.clear();
    items_table.rows.add(sale_items);
    items_table.columns([0]).visible(false);
    items_table.draw();
});

$('#sale_history_table tbody').on('click', '#sale_receipt_reprint', function () {
    var row_data = sale_history_table.row($(this).parents('tr')).data();

    document.getElementById("print").value = row_data.receipt_number;
});


// function saleDetails(items){
// document.getElementById('sales').style.display='none';
// sold=" <h4><span class='badge badge-success'>Sold</span></h4>"
// pending=" <h4><span class='badge badge-warning'>Pending</span></h4>"
// returned=" <h4><span class='badge badge-danger'>Returned</span></h4>"
// sale_items = [];
// items.forEach(function(item){
// var item_data=[];
// item_data.push(item.id);
// item_data.push(item.name);
// item_data.push(item.quantity);
// item_data.push(item.price);
// item_data.push(item.vat);
// item_data.push(item.discount);
// item_data.push(item.amount);
// if (item.status ==2) {
// item_data.push(pending)
// }
// else if (item.status ==3) {
// item_data.push(returned)
// }
// else{
// item_data.push(sold)
// }
// sale_items.push(item_data);
// })
// items_table.clear();
// items_table.rows.add(sale_items);
// items_table.column(0).visible(false);
// items_table.draw();
// document.getElementById('items').style.display='block';
// $('#cancel').on('click', function() {
// return_cart = [];
// document.getElementById('sales').style.display='block';
// document.getElementById('items').style.display='none';
// });

// }


$('#items_table tbody').on('click', '#rtn_btn', function () {
    var index = items_table.row($(this).parents('tr')).index();
    var data = items_table.row($(this).parents('tr')).data();
    $('#sale-return').modal('show');
    $('#sale-return').find('.modal-body #id_of_item').val(data[0]);
    $('#sale-return').find('.modal-body #og_item_qty').val(data[2]);
    $('#sale-return').find('.modal-body #name_of_item').val(data[1]);
    document.getElementById('save_btn').style.display = 'block';
    $('#sale-return').on('change', '#rtn_qty', function () {
        var quantity = document.getElementById('rtn_qty').value;
        if (quantity > data[2] || quantity < 0) {
            document.getElementById('save_btn').disabled = 'true';
            document.getElementById('qty_error').style.display = 'block';
            $('#sale-return').find('.modal-body #qty_error').text('Maximum quantity is ' + data[2]);
        } else {
            document.getElementById('qty_error').style.display = 'none';
            $('#save_btn').prop('disabled', false);
        }
    });
});

$("#sale_discount").on('change', function (evt) {
    if (evt.which != 110) {//not a fullstop
        var n = Math.abs((parseFloat($(this).val().replace(/\,/g, ''), 10) || 0));
        $(this).val(n.toLocaleString("en", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }));
    }
});

$("#paying").on('change', function (evt) {
    if (evt.which != 110) {//not a fullstop
        var n = Math.abs((parseFloat($(this).val().replace(/\,/g, ''), 10) || 0));
        $(this).val(n.toLocaleString("en", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }));
    }
    var paid = (document.getElementById("paying").value);
    paid_amount = (parseFloat(paid.replace(/\,/g, ''), 10) || 0);
    $('#credit-sale-payment').find('.modal-body #paid-amount').val(paid_amount);
});

$("#sale_paid").on('change', function (evt) {
    if (evt.which != 110) {//not a fullstop
        var n = Math.abs((parseFloat($(this).val().replace(/\,/g, ''), 10) || 0));
        $(this).val(n.toLocaleString("en", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }));
    }
});

$('#products').select2({
    language: {
        noResults: function () {
            var search_input = $("#products").data('select2').$dropdown.find("input").val();
            var price_category = document.getElementById('price_category');
            var price_category_id = price_category.options[price_category.selectedIndex].value;

            /*make ajax call for more*/
            $.ajax({
                url: config.routes.filterProductByWord,
                type: "get",
                dataType: "json",
                data: {
                    word: search_input,
                    price_category_id: price_category_id
                },
                success: function (result) {
                    $("#products option").remove();
                    $.each(result, function (detail, name) {
                        $('#products').append($('<option>', {value: detail, text: name}));
                    });
                }
            });
        }
    }
});

function storeLocally(id) {
    localStorage.setItem('sale_type', id);
}

function triggerSaleType() {
    var sale_type_id = localStorage.getItem('sale_type');
    if (sale_type_id) {
        $('#price_category').val(sale_type_id).change();
    }
}

$(document).ready(function () {
    triggerSaleType();
});

$('#price_category').change(function () {
    $("#products option").remove();
    var id = $(this).val();
    storeLocally(id);
    if (id) {
        $.ajax({
            url: config.routes.selectProducts,
            data: {
                "_token": config.token,
                "id": id
            },
            type: 'post',
            dataType: 'json',
            success: function (result) {
                $.each(result, function (detail, name) {
                    $('#products').append($('<option>', {value: detail, text: name}));
                });
            },
        });
    }

});


$("#save-customer").click(function () {
    document.getElementById('new-task').value = 'New Customer';
});
$("#cancel-customer").click(function () {
    document.getElementById('new-task').value = 'Existing Customer';
    document.getElementById('new-customer').style.display = 'none';
    document.getElementById('sale-panel').style.display = 'block';
    document.getElementById('add-customer').style.display = 'block';
});
$("#add-customer").click(function () {
    document.getElementById('new-customer').style.display = 'block';
    document.getElementById('sale-panel').style.display = 'none';
    document.getElementById('add-customer').style.display = 'none';
});


if ($('#can_pay').length) {
    credit_payment_table.column(6).visible(true);
} else {
    credit_payment_table.column(6).visible(false);

}


function getCredits() {
    if ($('#track').length) {
        var status = document.getElementById('payment-status').value;
        var dates = document.querySelector('input[name=date_of_sale]').value;
        dates = dates.split('-');
    }
    var id = document.getElementById('customer_payment').value;
    if (id || status || dates) {
        $.ajax({
            url: config.routes.getCreditSale,
            data: {
                "_token": config.token,
                "id": id,
                'date': dates
            },
            type: 'get',
            dataType: 'json',
            success: function (data) {
//Remove Pay Button for Balance < 1
                data.forEach(function (data) {
                    if (data.balance < 1) {
                        data.action = " <span class='badge badge-success'>Paid</span>";
                    }
                });
                if (status == 'all') {
                    data = data;
                } else if (status == 'full_paid') {
                    data = data.filter(function (el) {
                        return el.balance < 1;
                    });
                } else if (status == 'not_paid') {

                    data = data.filter(function (el) {
                        return el.balance == el.total_amount;
                    });
                } else if (status == 'partial_paid') {

                    data = data.filter(function (el) {
                        return (el.paid_amount > 0 && el.balance > 0);
                    });
                } else {
                    data = data.filter(function (el) {
                        return el.balance > 0;
                    });
                }


                if (id) {
                    credit_payment_table.column(1).visible(false);
                } else {
                    credit_payment_table.column(1).visible(true);
                }


                credit_payment_table.clear();
                credit_payment_table.rows.add(data);
                credit_payment_table.draw();

            },
            complete: function () {
            }
        });
    }

}

function getHistory() {
    var range = document.getElementById('daterange').value;
    range = range.split('-');
    if (range) {
        $('#loading').show();
        $.ajax({
            url: config.routes.getSalesHistory,
            data: {
                "_token": config.token,
                "range": range
            },
            type: 'get',
            dataType: 'json',
            success: function (data) {
                sale_history_table.clear();
                sale_history_table.rows.add(data);
                sale_history_table.draw();

            },
            complete: function () {
                $('#loading').hide();
            }
        });
    }

}

$('#daterange').change(function () {
    getHistory();

});

$('#customer_payment').change(function () {
    getCredits();

});

$('#payment-status').change(function () {
    getCredits();
});
$('#sales_date').change(function () {
    getCredits();
});


$('#credit_payment_table tbody').on('click', '#pay_btn', function () {
    var index = credit_payment_table.row($(this).parents('tr')).index();
    var data = credit_payment_table.row($(this).parents('tr')).data();
    $('#credit-sale-payment').modal('show');
    $('#credit-sale-payment').find('.modal-body #id_of_sale').val(data.sale_id);
    $('#credit-sale-payment').find('.modal-body #customer-id').val(data.customer_id);
    $('#credit-sale-payment').find('.modal-body #receipt-number').val(data.receipt_number);
    $('#credit-sale-payment').find('.modal-body #balance-amount').val(data.balance);
    $('#credit-sale-payment').find('.modal-body #outstanding').val(formatMoney(data.balance));
    document.getElementById('save_btn').style.display = 'block';
    $('#credit-sale-payment').on('change', '#rtn_qty', function () {
        var quantity = document.getElementById('rtn_qty').value;
        if (quantity > data[2]) {
            document.getElementById('save_btn').disabled = 'true';
            document.getElementById('qty_error').style.display = 'block';
            $('#credit-sale-payment').find('.modal-body #qty_error').text('The maximum quantity is ' + data[2]);
        } else {
            document.getElementById('qty_error').style.display = 'none';
            $('#save_btn').prop('disabled', false);
        }
    });
});

/*local storage of sale type*/


$('#sales_form').on('submit', function (e) {
    e.preventDefault();
    // window.open('#', '_blank');
    // window.open(this.href, '_self');
    var cart = document.getElementById("order_cart").value;

    if (cart === '' || cart === 'undefined') {
        notify('Sale list empty', 'top', 'right', 'warning');
        return false;
    }

    saveCashSale();
});

function saveCashSale() {
    var form = $('#sales_form').serialize();

    $.ajax({
        url: config.routes.storeCashSale,
        type: "post",
        dataType: "json",
        cache: "false",
        data: form,
        success: function (data) {
            window.location = data.redirect_to;
            triggerSaleType();
        }, complete: function () {
            notify('Sale recorded successfully', 'top', 'right', 'success');
            deselect();
        }, timeout: 20000
    });
}

$('#credit_sales_form').on('submit', function () {

    var cart = document.getElementById("order_cart").value;

    if (cart === '' || cart === 'undefined') {
        notify('Credit sale list empty', 'top', 'right', 'warning');
        return false;
    }

    saveCreditSale();

});

function saveCreditSale() {
    var form = $('#credit_sales_form').serialize();

    $.ajax({
        url: config.routes.storeCreditSale,
        type: "post",
        dataType: "json",
        cache: "false",
        data: form,
        success: function (data) {
            notify('Credit sale recorded successfully', 'top', 'right', 'success');
            deselect1();
            window.location = data.redirect_to;
        }
    });
}

$('#quote_sale_form').on('submit', function () {

    var cart = document.getElementById("order_cart").value;

    if (cart === '' || cart === 'undefined') {
        notify('Sale quote list empty', 'top', 'right', 'warning');
        return false;
    }

});

function isNumberKey(evt, obj) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    var value = obj.value;
    var dotcontains = value.indexOf(".") !== -1;
    if (dotcontains)
        if (charCode === 46) return false;
    if (charCode === 46) return true;
    return !(charCode > 31 && (charCode < 48 || charCode > 57));

}

function numberWithCommas(digit) {
    return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

