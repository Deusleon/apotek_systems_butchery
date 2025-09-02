@extends("layouts.master")

@section('page_css')
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

        .ms-selectable,
        .ms-selection {
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
                <a class="nav-link active text-uppercase" href="{{ route('stock-transfer.index') }}" role="tab"
                    aria-controls="quotes_list" aria-selected="true">New Transfer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" href="{{ route('stock-transfer-history') }}" role="tab"
                    aria-controls="new_quotes" aria-selected="false">Transfer History
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
                                    <label for="from_id">From</label>
                                    <div id="border">
                                        @if (current_store()->id === 1)
                                            <select id="from_id" name="from_id"
                                                class="js-example-basic-single form-control drop">
                                                <option selected="true" value="0" disabled="disabled">Select branch...
                                                </option>
                                                @foreach($stores as $store)
                                                    <option value="{{$store->id}}">{{$store->name}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select id="from_id" name="from_id"
                                                class="js-example-basic-single form-control drop">
                                                <option value="{{ current_store()->id }}">{{ current_store()->name }}
                                                </option>
                                            </select>
                                        @endif
                                    </div>
                                    <span id="from_danger" style="display: none; font-size: 14px; color: red">Please choose
                                        branch</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="to_id">To</label>
                                    <div id="to_border">
                                        <select id="to_id" name="to_id" class="js-example-basic-single form-control drop">
                                            <option selected="true" value="0" disabled="disabled">Select branch...
                                            </option>
                                            @foreach($stores as $store)
                                                <option value="{{$store->id}}">{{$store->name}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="current_store_id" value="{{ current_store()->id }}">
                                    </div>
                                    <span id="to_danger" style="display: none; font-size: 14px; color: red">Please choose
                                        branch</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="select_id">Products</label>
                                    <select id="select_id" name="select_id" class="form-control" disabled>
                                        <option selected="true" value="0" disabled>Select product...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ajax loading gif -->
                        <div id="loading">
                            <img id="loading-image" src="{{asset('assets/images/spinner.gif')}}" />
                        </div>

                        <div class="row p-3" id="detail">
                            <hr>
                            <div class="table teble responsive" style="width: 100%;">
                                <table id="cart_table" class="table nowrap table-striped table-hover" width="100%"></table>
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
                                    <label for="evidence" class="form-label">Evidence</label>
                                    <input type="file" class="form-control" id="evidence" name="evidence">
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
                                    <button id="deselect-all" class="btn btn-danger">Clear</button>
                                    {{-- <a href="{{ route('stock-transfer.index') }}" class="btn btn-danger">Clear</a> --}}
                                    <button id="transfer_preview" type="submit" class="btn btn-primary">
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

        var $from = $('#from_id');

        var config = {
            routes: {
                filterByStore: '{{route('filter-by-store')}}',
                filterByWord: '{{route('filter-by-word')}}',
                stockTransferSave: '{{route('stock_transfer.store')}}'

            }
        };

    </script>

@endpush