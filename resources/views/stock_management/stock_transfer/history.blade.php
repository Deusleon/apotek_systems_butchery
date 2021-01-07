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
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

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
                <a href="{{ route('stock-transfer.index') }}"></a>
                <div class="tab-pane fade show active" id="new_sale" role="tabpanel" aria-labelledby="new_sale-tab">
                    <div class="row">
                        <div class="col-md-8">

                        </div>
                        <div class="col-md-4">
                            @if(auth()->user()->checkPermission('Manage Stock Transfer'))
                                <a href="{{ route('stock-transfer.index') }}">
                                    <button style="float: right;margin-bottom: 2%;" type="button"
                                            class="btn btn-secondary btn-sm">
                                        New Transfer
                                    </button>
                                </a>
                            @endif
                        </div>
                    </div>
                    <form id="transfer" method="post" enctype="multipart/form-data">
                        @csrf()
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">

                                </div>
                            </div>
                        </div>
                        <!-- ajax loading gif -->
                        <div id="loading">
                            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                        </div>


                        <div class="row" id="detail">
                            <div style="display: block;" class="table-responsive">
                                <table id="fixed-header1" class="display table nowrap table-striped table-hover"
                                       style="width:100%; ">

                                    <thead>
                                    <tr>
                                        <th>Transfer #</th>
                                        <th>Date</th>
                                        <th>Total Products</th>
                                        <th>Transferred Qty</th>
                                        <th>Received Qty</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($transfers as $all_transfer)
                                        <tr>
                                            <td>{{$all_transfer->transfer_no}}</td>
                                            <td>{{date('d-m-Y', strtotime($all_transfer->created_at))}}</td>
                                            <td>{{$all_transfer->total_products}}</td>
                                            <td align="right">
                                                <div style="margin-right: 50%">
                                                    {{number_format(floatval($all_transfer->quantity))}}
                                                </div>
                                            </td>
                                            <td align="right">
                                                <div style="margin-right: 50%">
                                                    {{number_format(floatval($all_transfer->received_quantity))}}
                                                </div>
                                            </td>
                                            <td>
                                                @if($all_transfer->status == 1)
                                                    <span class='badge badge-warning'>Pending</span>
                                                    @if(auth()->user()->checkPermission('Transfer Acknowledgement'))
                                                        <a href="{{route('stock-transfer-acknowledge.index')}}"
                                                           class="label btn-success text-white f-12 btn-rounded">
                                                            Acknowledge
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class='badge badge-success'>Completed</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>

                                </table>
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


    <script type="text/javascript">
        $('#fixed-header1').DataTable({
            "aaSorting": []
        });
    </script>


@endpush
