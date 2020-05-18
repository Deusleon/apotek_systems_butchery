@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Transfer History
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Transfer History </a></li>
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
                    <form id="transfer" action="{{ route('stock-transfer-pdf-regen') }}" method="post"
                          enctype="multipart/form-data" target="_blank">
                        @csrf()
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" hidden>
                                    <!--  <label for="code">Products</label>
                                     <select onchange="val()" id="select_id"
                                             class="js-example-tokenizer form-control">
                                         <option value="">Select Product</option>

                                     </select> -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">From</label>
                                    <select id="from_id" name="from_id"
                                            class="js-example-basic-single form-control drop"
                                            onchange="storeSelectRePrint()">
                                        <option selected="true" disabled="disabled" value="0">Select store...</option>
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
                                            onchange="storeSelectRePrint()" disabled>
                                        <option selected="true" disabled="disabled" value="0">Select store..</option>
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

                        <div hidden>
                            <input type="text" name="transfer_no" id="transfer_no">
                        </div>

                        <div id="tbodyRePrint" style="display: block;" class="table-responsive">
                            <table style="table-layout:fixed;" id="fixed-header-re-print"
                                   class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <thead>
                                <tr>
                                    <th>Transfer #</th>
                                    <th>Date</th>
                                    {{--                                    <th>Quantity</th>--}}
                                    <th>Action</th>
                                    <th hidden>id</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($all_transfers as $transfer)
                                    <tr>
                                        <td>{{$transfer['transfer_no']}}</td>
                                        <td>{{$transfer['date']}}</td>
                                        {{--                                        <td>{{number_format(floatval($transfer['quantity']))}}</td>--}}
                                        <td>
                                            <button id='show-info' class='btn btn-sm btn-rounded btn-success'
                                                    type='button'>Show
                                            </button>
                                            @if(auth()->user()->checkPermission('View Stock Transfer Re-Print'))
                                                <button id='print' class='btn btn-sm btn-rounded btn-secondary'><span
                                                        class='fa fa-print' aria-hidden='true'></span>Print
                                                </button>
                                            @endif
                                        </td>
                                        <td hidden>{{$transfer['id']}}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>


                        <div id="tbodyRePrint1" style="display: none;" class="table-responsive">
                            <table style="table-layout:fixed;" id="fixed-header-re-print1"
                                   class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <thead>
                                <tr>
                                    <th>Transfer #</th>
                                    <th>Date</th>
                                    {{--                                    <th>Quantity</th>--}}
                                    <th>Action</th>
                                </tr>
                                </thead>

                            </table>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>


    @include('stock_management.re_print_transfer.show')

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
                stockTransferRegen: '{{route('stock-transfer-pdf-regen')}}'
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

    </script>

    <script src="{{asset("assets/apotek/js/stock-transfer-acknowledge.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>


@endpush
