@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Stock Transfer
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Transfer </a></li>
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

        #loading {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            display: none;
            opacity: 0.7;
            background-color: #fff;
            z-index: 99;
            text-align: center;
        }

        #loading-image {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 100;
        }

    </style>
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-uppercase"
                    href="{{ route('stock-transfer.index') }}" role="tab" aria-controls="quotes_list"
                    aria-selected="true">New Transfer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase"
                    href="{{ route('stock-transfer-history') }}" role="tab" aria-controls="new_quotes"
                    aria-selected="false">Transfer History
                </a>
            </li>
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="new_sale" role="tabpanel" aria-labelledby="new_sale-tab">
                    <form id="transfer" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">

                                    <label for="code">From</label>
                                    <div id="border" style="border: 2px solid white; border-radius: 6px;">
                                        <select id="from_id" name="from_id"
                                                class="js-example-basic-single form-control drop">
                                            <option selected="true" value="0" disabled="disabled">Select branch...
                                            </option>

                                            @foreach($stores as $store)
                                                <option value="{{$store->id}}">{{$store->name}}</option>
                                            @endforeach



                                        </select>
                                    </div>

                                    <span id="from_danger" style="display: none; color: red">Please choose branch</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">To</label>
                                    <div id="to_border" style="border: 2px solid white; border-radius: 6px;">
                                        <select id="to_id" name="to_id"
                                                class="js-example-basic-single form-control drop" disabled>
                                            <option selected="true" value="0" >Select branch..
                                            </option>
                                            @if(Auth::user()->checkPermission('Manage All Branches'))
                                                @foreach($stores as $store)
                                                    <option value="{{$store->id}}">{{$store->name}}</option>
                                                @endforeach
                                            @endif

                                            @if(!Auth::user()->checkPermission('Manage All Branches'))
                                                @foreach($stores as $store)
                                                    <option value="{{$store->id}}" {{ $store->id == Auth::user()->store_id ? 'selected' : '' }}>{{$store->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <span id="to_danger" style="display: none; color: red">Please choose branch</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Products</label>
                                    <select id="select_id" class="form-control" disabled>
                                    <option selected="true" value="" disabled>Select product...</option>
                                        @foreach($products as $stock)
                                            <option
                                                value="{{$stock->product['name'].' '.$stock->product['pack_size'].','.$stock->quantity.','.$stock->product_id.','.$stock->stock_id}}">{{$stock->product['name']}} {{$stock->product['pack_size']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ajax loading gif -->
                        <div id="loading">
                            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                        </div>

                        <div class="row" id="detail">
                            <hr>
                            <div class="table teble responsive" style="width: 100%;">
                                <table id="cart_table" class="table nowrap table-striped table-hover"
                                       width="100%"></table>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="remarks">Remarks</label>
                                <textarea type="text" class="form-control" id="remarks" name="remark"
                                          maxlength="100"></textarea>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                <label for="evidence" class="form-label"><span style="color: red;">* </span>Evidence</label>
                                <input type="file"  class="form-control" id="evidence" name="evidence">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" id="order_cart" name="cart">
                            </div>
                            <div class="col-md-6">
                                <div class="btn-group" style="float: right;">
                                    <button class="btn btn-primary" hidden>Transfer</button>
                                    <a href="{{ route('stock-transfer-history') }}">
                                        <button type="button" class="btn btn-danger">Back</button>
                                    </a>
                                    <button class="btn btn-warning" id="deselect-all">Clear</button>
                                    <button id="transfer_preview" class="btn btn-secondary">
                                         Transfer
                                    </button>
                                </div>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection


@push("page_scripts")

    @include('partials.notification')




    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    <script src="{{asset("assets/apotek/js/stock-transfer.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>

    <script type="text/javascript">

        //dropdown in one remove in another
        //dropdown in one remove in another
        var $from = $('#from_id');
        var $to = $('#to_id');
        var $to_options = $to.html();

        $from.on('change', function () {
            // Trigger product filtering
            filterTransferByStore();

            // Update 'To' dropdown options
            var selected_from = $(this).val();
            $to.html($to_options);
            if (selected_from !== '0') {
                $to.find('option[value="' + selected_from + '"]').remove();
            }
        });

        var config = {
            routes: {
                filterByStore: '{{route('filter-by-store')}}',
                filterByWord: '{{route('filter-by-word')}}',
                stockTransferSave: '{{route('stock_transfer.store')}}'

            }
        };

    </script>

    <script>
        $(document).ready(function() {
            // The product list will now populate only after a 'From' branch is selected.
        });

        function filterTransferByStore() {
            var from_id = $('#from_id').val();

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
                        text: 'Select Product...',
                        selected: true,
                        disabled: true
                    }));
                    $.each(data, function (id, detail) {
                        var displayText = detail.name + ' ' + detail.pack_size;
                        var valueData = displayText + ',' + detail.quantity + ',' + detail.product_id + ',' + detail.stock_id;

                        $('#select_id').append($('<option>', {
                            value: valueData,
                            text: displayText
                        }));
                    });
                },
                complete: function () {
                    $('#loading').hide();
                }
            });

        }


    </script>



@endpush
