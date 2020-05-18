@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Stock Transfer Acknowledge
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Transfer Acknowledge </a></li>
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
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="new_sale" role="tabpanel" aria-labelledby="new_sale-tab">
                    <form id="transfer" action="{{ route('stock-transfer.store') }}" method="post"
                          enctype="multipart/form-data">
                        @csrf()
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" hidden>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">From</label>
                                    <select id="from_id" name="from_id"
                                            class="js-example-basic-single form-control drop" onchange="storeSelect()">
                                        <option selected="true" disabled="disabled">Select store...</option>
                                        @foreach($stores as $store)
                                            <option value="{{$store->id}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">To</label>
                                    <select id="to_id" name="to_id" class="js-example-basic-single form-control drop"
                                            onchange="storeSelect()" disabled>
                                        <option selected="true" disabled="disabled">Select store..</option>
                                        @foreach($stores as $store)
                                            <option value="{{$store->id}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ajax loading gif -->
                        <div id="loading">
                            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                        </div>

                        <div id="tbody" style="display: none;" class="table-responsive">
                            <table id="fixed-header1"
                                   class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <thead>
                                <tr>
                                    <th>Transfer #</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>
                        </div>

                        <div id="tbody-main" style="display: block;" class="table-responsive">
                            <table id="fixed-header-main"
                                   class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <thead>
                                <tr>
                                    <th>Transfer #</th>
                                    {{--                                    <th>Date</th>--}}
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($all_transfers as $all_transfer)
                                    <tr>
                                        <td>{{$all_transfer->transfer_no}}</td>
                                        {{--                                        <td>{{date('Y-m-d',strtotime($all_transfer->created_at))}}</td>--}}
                                        <td>{{$all_transfer->currentStock['product']['name']}}</td>
                                        <td>{{number_format(floatval($all_transfer->transfer_qty))}}</td>
                                        <td>
                                            <button id="complete_tran" class="btn btn-sm btn-rounded btn-success"
                                                    data-id="{{$all_transfer->id}}"
                                                    data-stock_id="{{$all_transfer->stock_id}}"
                                                    data-transfer_qty="{{$all_transfer->transfer_qty}}"
                                                    data-from="{{$all_transfer->fromStore['name']}}"
                                                    data-to="{{$all_transfer->toStore['name']}}"
                                                    data-transfer_no="{{$all_transfer->transfer_no}}"
                                                    type="button"
                                                    data-toggle="modal"
                                                    data-target="#completes">Acknowledge
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>


    @include('stock_management.stock_transfer_acknowledge.complete')

@endsection


@push("page_scripts")

    @include('partials.notification')
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>


    <script type="text/javascript">
        var config = {
            routes: {
                stockTransferFilter: '{{route('stock-transfer-filter')}}',
                stockTransferUpdate: '{{route('stock-transfer-complete')}}',
                stockTransferShow: '{{route('stock-transfer-show')}}',
                stockTransferFilterDetail: '{{route('stock-transfer-filter-detail')}}'


            }
        };

        //dropdown in one remove in another
        var $drops = $('.drop'),
            $options = $drops.eq(1).children().clone();

        $drops.change(function () {
            var $other = $drops.not(this),
                otherVal = $other.val(),
                newVal = $(this).val(),
                $opts = $options.clone().filter(function () {
                    return this.value !== newVal;
                });
            $other.html($opts).val(otherVal);
        });

        $('#completes').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body #id').val(button.data('id'));
            modal.find('.modal-body #stock_id').val(button.data('stock_id'));
            modal.find('.modal-body #quantity_trn').val(numberWithCommas(button.data('transfer_qty')));
            modal.find('.modal-body #from').val(button.data('from'));
            modal.find('.modal-body #to').val(button.data('to'));
            modal.find('.modal-body #transfer_no').val(button.data('transfer_no'));
        });


    </script>

    <script src="{{asset("assets/apotek/js/stock-transfer-acknowledge.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>


@endpush
