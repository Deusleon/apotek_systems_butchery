<?php $__env->startSection('content-title'); ?>
    Credit Sales
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-sub-title'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Credit Sales</a></li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection("content"); ?>

    <style>
        .iti__flag {
            background-image: url("<?php echo e(asset("assets/plugins/intl-tel-input/img/flags.png")); ?>");
        }

        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .iti__flag {
                background-image: url("<?php echo e(asset("assets/plugins/intl-tel-input/img/flags@2x.png")); ?>");
            }
        }

        .iti {
            width: 100%;
        }

        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        #input_products_b {
            position: absolute;
            opacity: 0;
            z-index: 1;
        }

    </style>

    <div class="col-sm-12">
        <div class="card-block">

            <div class="col-sm-12">
                <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active text-uppercase" id="credit-sales-tablist" data-toggle="pill"
                           href="#credit-sale-receiving" role="tab"
                           aria-controls="credit_sales" aria-selected="true">New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="credit-tracking-tablist" data-toggle="pill"
                           href="#credit-tracking" role="tab"
                           aria-controls="credit_tracking" aria-selected="false">Tracking
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="credit-payment-tablist" data-toggle="pill"
                           href="#credit-payment" role="tab"
                           aria-controls="credit_payment" aria-selected="false">Payments
                        </a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    
                    <div class="tab-pane fade show active" id="credit-sale-receiving" role="tabpanel" aria-labelledby="credit_sales-tab">
                    <form id="credit_sales_form">
                        <?php echo csrf_field(); ?>
                        <?php if(auth()->user()->checkPermission('Manage Customers')): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <button style="float: right;margin-bottom: 2%;" type="button"
                                            class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#create"> Add
                                        New Customer
                                    </button>
                                </div>

                            </div>
                        <?php endif; ?>

                        <div id="sale-panel">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label id="cat_label">Sales Type<font color="red">*</font></label>
                                        <select id="price_category" class="js-example-basic-single form-control"
                                                required>
                                            <option value="">Select Type</option>
                                            <?php $__currentLoopData = $price_category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <!-- <option value="<?php echo e($price->id); ?>"><?php echo e($price->name); ?></option> -->
                                                <option
                                                value="<?php echo e($price->id); ?>" <?php echo e($default_sale_type === $price->id  ? 'selected' : ''); ?>><?php echo e($price->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Products<font color="red">*</font></label>
                                        <select id="products" class="form-control">
                                            <option value="" disabled selected style="display:none;">Select Product
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="code">Customer Name<font color="red">*</font></label>
                                        <select name="customer_id" id="customer"
                                                class="js-example-basic-single form-control" title="Customer" required>
                                            <option value="">Select Customer</option>
                                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($customer); ?>"><?php echo e($customer->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="detail">
                                <hr>
                                <div class="table teble responsive" style="width: 100%;">
                                    <table id="cart_table" class="table nowrap table-striped table-hover"
                                           width="100%"></table>
                                </div>

                            </div>
                            <hr>
                            <?php if($back_date=="NO"): ?>
                                <div class="row">
                                    <?php if($enable_discount === "YES"): ?>
                                        <div class="col-md-4">
                                            <div style="width: 99%">
                                                <label>Discount</label>
                                                <input type="text" onchange="discount()" id="sale_discount"
                                                       class="form-control" value="0"/>
                                            </div>
                                            <span class="help-inline">
<div class="text text-danger" style="display: none;" id="discount_error">Invalid Discount!</div>
</span>
                                        </div>
                                        <div class="col-md-4">
                                            <div style="width: 99%">
                                                <label>Paid</label>
                                                <input type="text" onchange="discount()" id="sale_paid"
                                                       class="form-control"
                                                       value="0"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div style="width: 99%">
                                                <label>Grace Period</label>
                                                <select class="js-example-basic-single form-control"
                                                        name="grace_period" id="grace_period">
                                                    <option value="1">1 Day</option>
                                                    <option value="7">7 Days</option>
                                                    <option value="14">14 Days</option>
                                                    <option value="21">21 Days</option>
                                                    <option value="30">30 Days</option>
                                                    <option value="60">60 Days</option>
                                                    <option value="90">90 Days</option>
                                                </select>

                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-md-6">
                                            <div style="width: 99%">
                                                <label>Paid</label>
                                                <input type="text" onchange="discount()" id="sale_paid"
                                                       class="form-control"
                                                       value="0"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div style="width: 99%">
                                                <label>Grace Period</label>
                                                <select class="js-example-basic-single form-control"
                                                        name="grace_period" id="grace_period">
                                                    <option value="1">1 Day</option>
                                                    <option value="7">7 Days</option>
                                                    <option value="14">14 Days</option>
                                                    <option value="21">21 Days</option>
                                                    <option value="30">30 Days</option>
                                                    <option value="60">60 Days</option>
                                                    <option value="90">90 Days</option>
                                                </select>

                                            </div>
                                        </div>
                                    <?php endif; ?>


                                    <input type="hidden" id="price_cat" name="price_category_id">
                                    <input type="hidden" id="discount_value" name="discount_amount">
                                    <input type="hidden" id="paid_value" name="paid_amount">
                                    <input type="hidden" id="credit_sale" name="credit" value="Yes">
                                    <input type="hidden" id="order_cart" name="cart">
                                    <input type="hidden" value="<?php echo e($vat); ?>" id="vat">
                                    <input type="hidden" value="<?php echo e($fixed_price); ?>" id="fixed_price">
                                    <input type="hidden" value="<?php echo e($enable_discount); ?>" id="enable_discount">


                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div style="width: 99%">
                                            <label>Sale Date<font color="red">*</font></label>
                                            <input type="text" name="sale_date" class="form-control"
                                                   id="credit_sale_date" autocomplete="off" required="true">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="width: 99%">
                                            <label>Discount</label>
                                            <input type="text" onchange="discount()" id="sale_discount"
                                                   class="form-control" value="0"/>
                                        </div>
                                        <span class="help-inline">
<div class="text text-danger" style="display: none;" id="discount_error">Invalid Discount!</div>
</span>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="width: 99%">
                                            <label>Paid</label>
                                            <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                                   value="0"/>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="width: 99%">
                                            <label>Grace Period (Days)</label>
                                            <input type="number" min="0" name="grace_period" id="grace_period"
                                                   class="form-control"
                                                   value="0"/>
                                        </div>
                                    </div>

                                    <input type="hidden" id="price_cat" name="price_category_id">
                                    <input type="hidden" id="discount_value" name="discount_amount">
                                    <input type="hidden" id="paid_value" name="paid_amount">
                                    <input type="hidden" id="credit_sale" name="credit" value="Yes">
                                    <input type="hidden" id="order_cart" name="cart">
                                    <input type="hidden" value="<?php echo e($vat); ?>" id="vat">
                                    <input type="hidden" value="<?php echo e($fixed_price); ?>" id="fixed_price">
                                    <input type="hidden" value="<?php echo e($enable_discount); ?>" id="enable_discount">
                                </div>
                            <?php endif; ?>
                            <hr>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group"><textarea id="remark" name="remark" class="form-control"
                                                                      rows="3"
                                                                      placeholder="Enter Remarks Here"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Sub Total:</b>
                                        </div>
                                        <div class="sub-total col-md-6"
                                             style="display: flex; justify-content: flex-end">0.00
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>VAT:</b>
                                        </div>
                                        <div class="tax-amount col-md-6"
                                             style="display: flex; justify-content: flex-end">0.00
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Total Amount:</b>
                                        </div>
                                        <div class="total-amount col-md-6"
                                             style="display: flex; justify-content: flex-end">0.00
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Balance:</b>
                                        </div>
                                        <div class="balance-amount col-md-6"
                                             style="display: flex; justify-content: flex-end">0.00
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <b>Max. Credit:</b>
                                        </div>
                                        <div class="credit_max col-md-6"
                                             style="display: flex; justify-content: flex-end">0.00
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="total">
                                <input type="hidden" id="sub_total">
                                <input type="hidden" id="total_vat" value="0">
                            </div>
                            <hr>

                            
                            <select id="products_b">
                                <option value="" disabled selected style="display:none;">Select Product</option>
                            </select>
                            <input type="text" id="input_products_b" name="input_products_b"
                                   value=""/>
                            

                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <div class="btn-group" style="float: right;">
                                        <button type="button" class="btn btn-danger" id="deselect-all-credit-sale">
                                            Cancel
                                        </button>
                                        <button class="btn btn-primary" id="save_btn">Save</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" value="" id="category">
                            <input type="hidden" value="" id="customers">
                            <input type="hidden" value="" id="print">

                        </div>
                    </form>
                    </div>
                    

                    
                    <div class="tab-pane fade" id="credit-tracking" role="tabpanel" aria-labelledby="credit_tracking-tab">
                            <div class="form-group d-flex align-items-end row">
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-3 form-group">
                                    <label style="margin-left: 62%" for=""
                                           class="col-form-label text-md-right">Customer:</label>
                                </div>
                                <div class="col-md-3 form-group">
                                    <select name="customer_id" id="customer_payment"
                                            class="js-example-basic-single form-control">
                                        <option value="" selected="true" disabled>Select Customer</option>
                                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-end row">
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-3 form-group">
                                    <label style="margin-left: 74%" for=""
                                           class="col-form-label text-md-right">Status:</label>
                                </div>
                                <div class="col-md-3 form-group">
                                    <select name="status" id="payment-status" class="js-example-basic-single form-control">
                                        <option value="" selected="true" disabled>Select Status</option>
                                        <option value="all">All</option>
                                        <option value="not_paid">Not Paid</option>
                                        <option value="partial_paid">Partial Paid</option>
                                        <option value="full_paid">Full Paid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-end row">
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-3 form-group">
                                    <label style="margin-left: 80%" for=""
                                           class="col-form-label text-md-right">Date:</label>
                                </div>
                                <div class="col-md-3 form-group">
                                    <input style="width: 110%;" type="text" name="date_of_sale" class="form-control"
                                           id="sales_date" value=""/>
                                </div>
                            </div>
                            <input type="hidden" id="track" value="1">
                            <input type="hidden" id="vat" value="">
                            <input type="hidden" value="" id="category">
                            <input type="hidden" value="" id="customers">
                            <input type="hidden" value="" id="print">
                            <input type="hidden" value="" id="fixed_price">

                            <div class="row" id="detail">
                                <hr>
                                <?php if(auth()->user()->checkPermission('Credit Payment')): ?>
                                    <div id="can_pay"></div>
                                <?php endif; ?>
                                <div class="table teble responsive" style="width: 100%;">
                                    <table id="credit_payment_table" class="display table nowrap table-striped table-hover"
                                           style="width:100%">

                                        <thead>
                                        <tr>
                                            <th>Receipt#</th>
                                            <th>Customer</th>
                                            <th>Sale Date</th>
                                            <th>Total</th>
                                            <th>Paid</th>
                                            <th>Balance</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>

                                    </table>
                                </div>

                            </div>
                        <?php echo $__env->make('sales.credit_sales.create_payment', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    

                    
                    <div class="tab-pane fade" id="credit-payment" role="tabpanel" aria-labelledby="credit_payment-tab">
                        <div class="form-group row">
                            <div class="col-md-6">

                            </div>
                            <div class="col-md-3" style="margin-left: 2.5%">
                                <label style="margin-left: 62%" for=""
                                       class="col-form-label text-md-right">Customer:</label>
                            </div>
                            <div class="col-md-3" style="margin-left: -3.2%;">
                                <select name="customer_id" id="customer_payment"
                                        class="js-example-basic-single form-control" onchange="filterPaymentHistory()">
                                    <option value="" selected="true" disabled>Select Customer</option>
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">

                            </div>
                            <div class="col-md-3" style="margin-left: 1.4%">
                                <label style="margin-left: 80%" for=""
                                       class="col-form-label text-md-right">Date:</label>
                            </div>
                            <div class="col-md-3" style="margin-left: -3%;">
                                <input style="width: 107%;" type="text" name="date_of_sale" class="form-control"
                                       id="sales_date" value="" autocomplete="off"/>
                            </div>
                        </div>

                        <div class="table-responsive" id="main_table">
                            <table id="fixed-header-main" class="display table nowrap table-striped table-hover"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>


                        <div class="table-responsive" id="filter_history" style="display: none">
                            <table id="fixed-header-filter" class="display table nowrap table-striped table-hover"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>

                            </table>
                        </div>

                        <input type="hidden" value="" id="category">
                        <input type="hidden" value="" id="customers">
                        <input type="hidden" value="" id="print">
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('sales.customers.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush("page_scripts"); ?>

    
    <?php echo $__env->make('partials.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <script type="text/javascript">

        var page_no = 1;//sales page
        var normal_search = 0;//search by word

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var config = {
            token: '<?php echo e(csrf_token()); ?>',
            routes: {
                selectProducts: '<?php echo e(route('selectProducts')); ?>',
                storeCreditSale: '<?php echo e(route('credit-sales.storeCreditSale')); ?>',
                filterProductByWord: '<?php echo e(route('filter-product-by-word')); ?>'


            }
        };

        /*normal product search box*/
        $('#products').on('select2:open', function (e) {
            // select2 is opened, handle event
            normal_search = 1;
        });
        $('#products').on('select2:close', function (e) {
            // select2 is opened, handle event
            normal_search = 0;
        });

        /*hide barcode search*/
        $.fn.toggleSelect2 = function (state) {
            return this.each(function () {
                $.fn[state ? 'show' : 'hide'].apply($(this).next('.select2-container'));
            });
        };

        $(document).ready(function () {

            var sale_type_id = localStorage.getItem('sale_type');
            $('#products_b').toggleSelect2(false);

            if (sale_type_id) {
                $('#products_b').select2('close');
                setTimeout(function () {
                    $('input[name="input_products_b"]').focus()
                }, 30);
            }

            $('#price_category').on('change', function () {
                setTimeout(function () {
                    $('input[name="input_products_b"]').focus()
                }, 30);
            });

        });

        $('#customer').on('change', function () {
            setTimeout(function () {
                $('input[name="input_products_b"]').focus()
            }, 30);
        });

        $('#grace_period').on('change', function () {
            setTimeout(function () {
                $('input[name="input_products_b"]').focus()
            }, 30);
        });

        //setup before functions
        var typingTimer;                //timer identifier
        var doneTypingInterval = 500;  //time in ms (5 seconds)

        //on keyup, start the countdown
        $('#input_products_b').keyup(function () {
            clearTimeout(typingTimer);
            if ($('#input_products_b').val()) {
                typingTimer = setTimeout(doneTyping, doneTypingInterval);
            }
        });

        function doneTyping() {
            $("#products_b").data('select2').$dropdown.find("input").val(document.getElementById('input_products_b').value).trigger('keyup');
            $('#products_b').select2('close');
            document.getElementById('input_products_b').value = '';
        }

    </script>
    <script src="<?php echo e(asset("assets/plugins/moment/js/moment.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/apotek/js/notification.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/apotek/js/sales/credit.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/apotek/js/customer.js")); ?>"></script>
    <script
        src="<?php echo e(asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")); ?>"></script>
    <script src="<?php echo e(asset("assets/js/pages/ac-datepicker.js")); ?>"></script>

    
    <script type="text/javascript">
        $(document).ready(function () {
            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#sales_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#sales_date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: end,
                autoUpdateInput: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);




        });

    </script>
    <script type="text/javascript">

        var config = {
            token: '<?php echo e(csrf_token()); ?>',
            routes: {
                getCreditSale: '<?php echo e(route('getCreditSale')); ?>'

            }
        };

        $(document).ready(function () {
            config = {
                token: '<?php echo e(csrf_token()); ?>',
                routes: {
                    getCreditSale: '<?php echo e(route('getCreditSale')); ?>'

                }
            };
        });
    </script>

    <script>
        $(document).ready(function() {
            var config = {
                token: '<?php echo e(csrf_token()); ?>',
                routes: {
                    getCreditSale: '<?php echo e(route('getCreditSale')); ?>'

                }
            };
            // Listen for the click event on the tab
            $('#credit-tracking-tablist').on('click', function () {
                console.log('Credit Tracking tab clicked');

                config = {
                    token: '<?php echo e(csrf_token()); ?>',
                    routes: {
                        getCreditSale: '<?php echo e(route('getCreditSale')); ?>'

                    }
                };

                // You can put additional code here to run when the tab is clicked
            });
        });
    </script>

    

    <script>
        $('#fixed-header-main').DataTable({
            columnDefs: [
                {
                    type: 'date',
                    targets: [1]
                }
            ],
            ordering: false
        });

        let payment_history_filter_table = $('#fixed-header-filter').DataTable({
            columns: [
                {'data': 'name'},
                {
                    'data': 'created_at', render: function (date) {
                        return moment(date).format('D-M-YYYY');
                    }
                },
                {
                    'data': 'paid_amount', render: function (amount) {
                        return formatMoney(amount);
                    }
                }
            ],
            columnDefs: [
                {
                    type: 'date',
                    targets: [1]
                }
            ],
            ordering: false,
            // aaSorting: [[1, "desc"]]
        });

        function filterPaymentHistory() {
            let customer_id = document.getElementById('customer_payment').value;
            let date = document.getElementById('sales_date').value;

            if (customer_id === '') {
                customer_id = null;
            }

            if (date === '') {
                date = null;
            }

            /*make ajax call for more*/
            $.ajax({
                url: '<?php echo e(route('payment-history-filter')); ?>',
                type: "get",
                dataType: "json",
                data: {
                    customer_id: customer_id,
                    date: date
                },
                success: function (data) {
                    document.getElementById('main_table').style.display = 'none';
                    document.getElementById('filter_history').style.display = 'block';

                    data = data.filter(function (el) {
                        return Number(el.paid_amount) !== Number(0);
                    });

                    payment_history_filter_table.clear();
                    payment_history_filter_table.rows.add(data);
                    payment_history_filter_table.draw();


                }
            });


        }

        $(function () {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#receive_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#sales_date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                autoUpdateInput: true,

                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);
            cb(start, end);

            $('input[name="date_of_sale"]').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                filterPaymentHistory();
            });

            $('input[name="date_of_sale"]').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

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

    </script>




<?php $__env->stopPush(); ?>

<?php echo $__env->make("layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/jamesburetta/PhpstormProjects/ProjectApotek/resources/views/sales/credit_sales/index.blade.php ENDPATH**/ ?>