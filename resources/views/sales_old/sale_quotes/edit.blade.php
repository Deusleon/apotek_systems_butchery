@extends("layouts.master")

@section('content-title')
    Sales Order
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Edit Sales Order</a></li>
@endsection


@section('content')
    <style>
        .ms-container {
            background: transparent url('../assets/plugins/multi-select/img/switch.png') no-repeat 50% 50%;
            width: 100%;
        }

        .ms-selectable,
        .ms-selection {
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
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <form id="quote_sale_form">
                    @csrf()
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label id="cat_label">Sales Type</label>
                                <select id="price_category" class="js-example-basic-single form-control">
                                    <option value="">Select Sales Type</option>
                                    @foreach ($price_category as $price)
                                        <!-- <option value="{{ $price->id }}">{{ $price->name }}</option> -->
                                        <option value="{{ $price->id }}"
                                            {{ $default_sale_type === $price->id ? 'selected' : '' }}>{{ $price->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Customer Name <font color="red">*</font></label>
                                <select id="customer_id" name="customer_id" class="js-example-basic-single form-control"
                                    required>
                                    <option value="" disabled selected="true">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ ($customer->id == $customer_id ? "selected":"") }}>{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="detail">
                        <hr>
                        <div class="table-responsive" style="width: 100%;">
                            <table id="edit_sales_order" class="table nowrap table-striped table-hover dataTable no-footer" width="100%" role="grid" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>VAT</th>
                                    <th>Amount</th>
                                    <th hidden>Discount</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sales_details as $user)
                                    <tr>
                                        <td>{{$user->name}} </td>
                                        <td id="quantity_{{ $user->id }}">{{$user->quantity}}</td>
                                        <td>{{number_format($user->price,0)}}</td>
                                        <td>{{number_format($user->vat,0)}}</td>
                                        <td>{{number_format($user->amount)}}</td>
                                        <td hidden>{{number_format($user->discount,0) }} </td>
                                        <td> <a href="#">
                                                <button class="btn btn-primary btn-sm btn-rounded"
                                                        data-name="{{$user->name}}"
                                                        data-id="{{$user->id}}"
                                                        data-quantity="{{$user->quantity}}"
                                                        data-price="{{$user->price}}"
                                                        data-vat="{{$user->vat}}"
                                                        data-amount="{{$user->amount}}"
                                                        data-discount="{{$user->discount}}"
                                                        type="button" data-toggle="modal" data-target="#editOrder">Edit
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            @if ($enable_discount === 'YES')
                                <div style="width: 99%">
                                    <label>Discount</label>
                                    <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                        value="0.00" />
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">

                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Sub Total:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="sub_total" class="form-control-plaintext text-md-right" readonly
                                        value="{{ number_format($sub_total,2) ?? '0.0' }}" />
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total_vat" class="form-control-plaintext text-md-right" readonly
                                        value="{{ number_format($vat,2) ?? '0.0' }}" />
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Total
                                        Amount:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total" class="form-control-plaintext text-md-right" readonly
                                        value="{{ number_format($total,2) ?? '0.0' }}" />
                                </div>
                                <span class="help-inline text text-danger" style="display: none; margin-left: 63%"
                                    id="discount_error">Invalid Amount</span>
                            </div>
                        </div>


                        <input type="hidden" value="{{ $vat }}" id="vat">
                        <input type="hidden" value="0.00" id="sale_paid">
                        <input type="hidden" value="Yes" id="quotes_page">
                        <input type="hidden" value="0.00" id="change_amount">
                        <input type="hidden" id="price_cat" name="price_category_id">
                        <input type="hidden" id="discount_value" name="discount_amount">
                        <input type="hidden" id="order_cart" name="cart">
                        <input type="hidden" value="" id="fixed_price">

                        <input type="hidden" value="" id="category">
                        <input type="hidden" value="" id="customers">
                        <input type="hidden" value="{{$quote_id}}" id="quote_id">
                        <input type="hidden" value="{{$customer_id}}" id="customer_id">
                        <input type="hidden" value="" id="print">
                        <input type="hidden" value="{{ $enable_discount }}" id="enable_discount">

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="btn-group" style="float: right;">
                                <a href="{{ url('sales/sales-order-list') }}" class="btn btn-danger">Back</a>
                                <button data-id="{{$quote_id}}"
                                        data-customer="{{$customer_id}}"
                                        class="btn btn-primary" id="convert_to_sales" data-target="convert_to_sales" >Save</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @include('sales.sale_quotes.modal.update')

@endsection

@push('page_scripts')
    @include('partials.notification')
    <script type="text/javascript">

        //Captures data of the specific order item to be updated
        $('#editOrder').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var name = button.data('name');
            var id = button.data('id');
            var quantity = button.data('quantity');
            var price = button.data('price')
            var modal = $(this);


            modal.find('.modal-body #name').val(name);
            modal.find('.modal-body #quantity').val(quantity);
            modal.find('.modal-body #price').val(price);
            modal.find('.modal-body #id').val(id);

            {{--var _token = $('input[name="_token"]').val();--}}
            {{--$.ajax({--}}
            {{--    url: "{{route('getRoleID')}}",--}}
            {{--    method: "POST",--}}
            {{--    data: {role: role, _token: _token},--}}
            {{--    success: function (result) {--}}
            {{--        $('#role1').val(result).change();--}}
            {{--    }--}}
            {{--})--}}

        });//end edit

        $('#convert_to_sales').on('click', function(e) {
            e.preventDefault();
            var quote_id =  document.getElementById("quote_id").value;
            var customer_id = document.getElementById("customer_id").value;

            var _token = $('input[name="_token"]').val();
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    "X-Requested-With": "XMLHttpRequest"
                },
                url: "{{ route('convert-to-sales') }}",
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    quote_id: quote_id,
                    customer_id: customer_id}
                ,
                success: function (result) {
                    console.log('Results',result);

                    window.location.href = "{{ route('sale-quotes.index') }}";

                    notify('Order converted successfully', 'top', 'right', 'success');

                },
                error: function (error) {
                    console.error('Error fetching users:', error);

                    notify('Failed to convert!', 'top', 'right', 'danger');
                }
            })
        });

        function backToOrders() {
            window.location.href = "{{ route('sale-quotes.index') }}";  // Replace 'orders' with your route name
        }


        /*hide barcode search*/
        $.fn.toggleSelect2 = function(state) {
            return this.each(function() {
                $.fn[state ? 'show' : 'hide'].apply($(this).next('.select2-container'));
            });
        };

        $(document).ready(function() {

            var sale_type_id = localStorage.getItem('sale_type');
            $('#products_b').toggleSelect2(false);

            if (sale_type_id) {
                $('#products_b').select2('close');
                setTimeout(function() {
                    $('input[name="input_products_b"]').focus()
                }, 30);
            }

            $('#price_category').on('change', function() {
                setTimeout(function() {
                    $('input[name="input_products_b"]').focus()
                }, 30);
            });

        });

        $('#customer_id').on('change', function() {
            setTimeout(function() {
                $('input[name="input_products_b"]').focus()
            }, 30);
        });

        //setup before functions
        var typingTimer; //timer identifier
        var doneTypingInterval = 500; //time in ms (5 seconds)

        //on keyup, start the countdown
        $('#input_products_b').keyup(function() {
            clearTimeout(typingTimer);
            if ($('#input_products_b').val()) {
                typingTimer = setTimeout(doneTyping, doneTypingInterval);
            }
        });

    </script>
    <script src="{{ asset('assets/apotek/js/notification.js') }}"></script>
    <script src="{{ asset('assets/apotek/js/edit_sales.js') }}"></script>
    <script src="{{ asset('assets/apotek/js/customer.js') }}"></script>


@endpush
