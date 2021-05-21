@extends("layouts.master")

@section('content-title')
    Sales Quotes
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Sales Quotes</a></li>
@endsection


@section("content")
    <style>
        .ms-container {
            background: transparent url('../assets/plugins/multi-select/img/switch.png') no-repeat 50% 50%;
            width: 100%;
        }

        .ms-selectable, .ms-selection {
            background: #fff;
            color: #555555;
            float: left;
            width: 45%;
        }

        #input_products_b {
            position: absolute;
            opacity: 0;
            z-index: 1;
        }

    </style>
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab"
                   aria-controls="pills-home" aria-selected="true">New</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab"
                   aria-controls="pills-profile" aria-selected="false">Quotes List</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <form id="quote_sale_form">
                    @csrf()
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label id="cat_label">Sales Type</label>
                                <select id="price_category" class="js-example-basic-single form-control">
                                    <option value="">Select Sales Type</option>
                                    @foreach($price_category as $price)
                                        <option value="{{$price->id}}">{{$price->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Products</label>
                                <select id="products" class="form-control">
                                    <option value="" disabled selected style="display:none;">Select Product</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="code">Customer Name <font color="red">*</font></label>
                                <select id="customer_id" name="customer_id" class="js-example-basic-single form-control"
                                        required>
                                    <option value="" disabled selected="true">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{$customer->id}}">{{$customer->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="detail">
                        <hr>
                        <div class="table teble responsive" style="width: 100%;">
                            <table id="cart_table" class="table nowrap table-striped table-hover" width="100%"></table>
                        </div>

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            @if($enable_discount === "YES")
                                <div style="width: 99%">
                                    <label>Discount</label>
                                    <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                           value="0"/>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">

                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Sub Total:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="sub_total"
                                           class="form-control-plaintext text-md-right"
                                           readonly value="0.0"/>
                                </div>
                            </div>
                            <div class="row">
                                <label
                                    class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total_vat"
                                           class="form-control-plaintext text-md-right"
                                           readonly value="0.0"/>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Total
                                        Amount:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total" class="form-control-plaintext text-md-right"
                                           readonly value="0.0"/>
                                </div>
                                <span class="help-inline text text-danger" style="display: none; margin-left: 63%"
                                      id="discount_error">Invalid Amount</span>
                            </div>
                        </div>


                        <input type="hidden" value="{{$vat}}" id="vat">
                        <input type="hidden" value="0" id="sale_paid">
                        <input type="hidden" value="Yes" id="quotes_page">
                        <input type="hidden" value="0" id="change_amount">
                        <input type="hidden" id="price_cat" name="price_category_id">
                        <input type="hidden" id="discount_value" name="discount_amount">
                        <input type="hidden" id="order_cart" name="cart">
                        <input type="hidden" value="" id="fixed_price">

                        <input type="hidden" value="" id="category">
                        <input type="hidden" value="" id="customers">
                        <input type="hidden" value="" id="print">
                        <input type="hidden" value="{{$enable_discount}}" id="enable_discount">

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remark" id="remark" class="form-control"></textarea>
                            </div>

                        </div>
                    </div>
                    <hr>

                    {{--barcode input boxes--}}
                    <select id="products_b">
                        <option value="" disabled selected style="display:none;">Select Product</option>
                    </select>
                    <input type="text" id="input_products_b" name="input_products_b"
                           value=""/>
                    {{--end barcode input boxes--}}

                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="btn-group" style="float: right;">
                                <button type="button" class="btn btn-danger" id="deselect-all-quote">Cancel</button>
                                <button id="save_btn" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>

                    <form>
            </div>
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

                <div class="d-flex justify-content-end mb-2 align-items-center">
                    <label class="mr-2" for="">Date:</label>
                    <input type="text" id="date_range" class="form-control w-auto" onchange="getQuotes()">
                </div>
                <div class="table-responsive" id="sales">
                    <table id="sale_quotes-Table" class="display table nowrap table-striped table-hover"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Sale Type</th>
                            <th>Sub Total</th>
                            <th>VAT</th>
                            <th>Discount</th>
                            <th>Amount</th>
                            <th>Action</th>
                            <th>id</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('sales.sale_quotes.details')
@endsection

@push("page_scripts")
    @include('partials.notification')
    <script type="text/javascript">

        var page_no = 1;//sales page
        var normal_search = 0;//search by word

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let quotes_table = null;

        var config = {
            token: '{{ csrf_token() }}',
            routes: {
                selectProducts: '{{route('selectProducts')}}',
                storeQuote: '{{ route('storeQuote') }}',
                filterProductByWord: '{{route('filter-product-by-word')}}'
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

        $('#customer_id').on('change', function () {
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

        function getQuotes(){
            $.ajax({
                url: "{{route('sale-quotes.get-quotes')}}",
                dataType: "json",
                data: {
                    date: $('#date_range').val()
                },
                type: 'GET',
                success: function(data){
                    quotes_table.clear();
                    quotes_table.rows.add(data)
                    quotes_table.draw();
                }
            });
        }

        $(document).ready(function () {
            
            $(function () {

                var start = moment();
                var end = moment();

                function cb(start, end) {
                    $('#date_range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }

                $('#date_range').daterangepicker({
                    startDate: start,
                    endDate: end,
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
            
            
            quotes_table = $('#sale_quotes-Table').DataTable({
                columns:[
                    {
                        data: 'date', 
                        render: function(date){
                            return moment(date).format('DD-MM-YYYY');
                        }
                    },
                    {data: 'cost.name'},
                    {data: 'cost.sub_total', render: function(data){
                        return formatMoney(data);
                    }},
                    {data: 'cost.vat', render: function(data){
                        return formatMoney(data);
                    }},
                    {data: 'cost.discount', render: function(data){
                        return formatMoney(data);
                    }},
                    {data: 'cost', render: function(cost){
                        return formatMoney(Number(cost.amount) - Number(cost.discount));
                    }},
                    {data: null, render: function(data, type, row){
                        let receipt_url = '{{route('receiptReprint','receipt_id')}}';
                        receipt_url = receipt_url.replace('receipt_id', row.id);
                        return `
                                <button class="btn btn-sm btn-rounded btn-success" type="button"
                                        onclick='showQuoteDetails(event)'
                                        id="quote_details">Show
                                </button>

                                <a href="${receipt_url}" target="_blank">
                                    <button class="btn btn-sm btn-rounded btn-secondary" type="button"><span
                                            class='fa fa-print' aria-hidden='true'></span>
                                        Print
                                    </button>
                                </a>
                        `;
                    }},
                    {data: 'id'},
                ],
                language: {
                    emptyTable: "No Sales Quote Data Available in the Table"
                },
                aaSorting: [[7, 'desc']],
                columnDefs: [
                    {targets:[7], visible:false}
                ]
            });
        });


        function showQuoteDetails(event){
            let data = quotes_table.row($(event.target).parents('tr')).data();
            quoteDetails(data.remark, data.details);
        }

        //Maintain the current Pill on reload
        $(function () {
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                localStorage.setItem('lastPill', $(this).attr('href'));
            });
            var lastPill = localStorage.getItem('lastPill');


            if (lastPill) {
                $('[href="' + lastPill + '"]').tab('show');
            }
        });

    </script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>
    <script src="{{asset("assets/apotek/js/sales.js")}}"></script>


@endpush
