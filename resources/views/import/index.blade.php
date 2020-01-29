@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Import
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Import </a></li>
@endsection

@section("content")


    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('record-import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="file" class="form-control" name="file" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" hidden>
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category-name" name="category_id"
                                        class="js-example-basic-single form-control drop">
                                    <option value="" selected="true" disabled="disabled">Select Category...</option>
                                    @foreach($categories as $c)
                                        <option value="{{$c->id}}">{{$c->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price-category">Price Category</label>
                                <select id="price-category-name" name="price_category_id"
                                        class="js-example-basic-single form-control drop">
                                    <option value="" selected="true" disabled="disabled">Select Category...</option>
                                    @foreach($price_categories as $pc)
                                        <option value="{{$pc->id}}">{{$pc->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="store">Store</label>
                                <select id="store-name" name="store_id"
                                        class="js-example-basic-single form-control drop">
                                    <option value="" selected="true" disabled="disabled">Select store</option>
                                    @foreach($stores as $store)
                                        <option value="{{$store->id}}">
                                            {{$store->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="supplier">Supplier</label>
                                <select id="supplier-name" name="supplier_id"
                                        class="js-example-basic-single form-control drop">
                                    <option value="" selected="true" disabled="disabled">Select Supplier...</option>
                                    @foreach($suppliers as $sp)
                                        <option value="{{$sp->id}}">{{$sp->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{route('import-template')}}">Download Template</a>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-5">

                        </div>
                        <div class="col-md-2">
                            {{--<a href="" target="_blank">--}}
                            <button class="btn btn-secondary" style="width: 100%">
                                Upload
                            </button>
                            {{--</a>--}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection


@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    @include('partials.notification')

@endpush
