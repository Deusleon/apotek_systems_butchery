@extends("layouts.master")

@section('page_css')
    <style>
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        #select1 {
            z-index: 10050;
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

        .select2-container {
            width: 103% !important;
        }
    </style>
@endsection

@section('content-title')
    Current Stock
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Current Stock </a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="current-stock-tablist" data-toggle="pill"
                   href="{{ url('inventory/stock-adjustment') }}" role="tab"
                   aria-controls="current-stock" aria-selected="true">Current Stock</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="old-stock-tablist" data-toggle="pill"
                   href="#old-stock" role="tab"
                   aria-controls="stock_list" aria-selected="false">Old Stock</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="all-stock-tablist" data-toggle="pill"
                   href="#all-stock" role="tab"
                   aria-controls="stock_list" aria-selected="false">All Stock</a>
            </li>
        </ul>
        <div class="card">
            <div class="card-body">
                <div class="form-group row d-flex">
                    <div class="col-md-4">
                        <label for="stock_status" class="col-form-label text-md-right">Store:</label>
                        <select name="stores_id" class="js-example-basic-single form-control" id="stores_id">
                            @foreach($stores as $store)
                                <option value="{{$store->id}}" {{$default_store_id === $store->id  ? 'selected' : ''}}>{{$store->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="stock_status" class="col-form-label text-md-right">Status:</label>
                        <select name="stock_status" class="js-example-basic-single form-group" id="stock_status_id">
                            <option value="1">In Stock</option>
                            <option value="0">Out Of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="category" class="col-form-label text-md-left">Category:</label>
                        <select name="category" class="js-example-basic-single form-control" id="category_id">
                            <option readonly value="0" disabled selected>Select Category...</option>
                            @foreach($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- main table -->
                <div id="tbody1" class="table-responsive">
                    <table id="fixed-header-main" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Pack Size</th>
                                <th>Quantity</th>
                                <th>Stock Value</th>
                                <th>Expire Date</th>
                                <th>Batch Number</th>
                                <th>Stock Status</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- ajax loading image -->
                <div id="loading">
                    <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>

    @include('partials.notification')

    <script>
        $(document).ready(function() {
            var table = $('#fixed-header-main').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ url('inventory/current-stock-api') }}",
                    data: function(d) {
                        d.stores_id = $('#stores_id').val();
                        d.status = $('#stock_status_id').val();
                        d.category = $('#category_id').val();
                    }
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'pack_size', name: 'pack_size'},
                    {
                        data: 'quantity',
                        name: 'quantity',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US');
                        }
                    },
                    {
                        data: 'stock_value',
                        name: 'stock_value',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            });
                        }
                    },
                    {data: 'expiry_date', name: 'expiry_date'},
                    {data: 'batch_number', name: 'batch_number'},
                    {
                        data: 'stock_status',
                        name: 'stock_status',
                        render: function(data) {
                            var badge = '';
                            switch(data) {
                                case 'In Stock':
                                    badge = 'badge-success';
                                    break;
                                case 'Out of Stock':
                                    badge = 'badge-danger';
                                    break;
                                case 'Low Stock':
                                    badge = 'badge-warning';
                                    break;
                            }
                            return '<span class="badge ' + badge + '">' + data + '</span>';
                        }
                    },
                    {data: 'category_name', name: 'category_name'}
                ],
                order: [[0, 'asc']],
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#stores_id, #stock_status_id, #category_id').change(function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush 