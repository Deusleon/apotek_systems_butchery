@extends("layouts.master")

@section('page_css')
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection

@section('content-title')
    Export Stock
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Settings / Tools / Export Stock</a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('products.export') }}" method="GET">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="store">Branch <span class="text-danger">*</span></label>
                                @if (current_store()->id === 1)
                                    <select id="store" name="store" class="js-example-basic-single form-control drop">
                                        <option selected="true" value="0" disabled="disabled">Select branch
                                        </option>
                                        @foreach(\App\Store::where('name', '<>', 'ALL')->get() as $store)
                                            <option value="{{$store->id}}">{{$store->name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <select id="store" name="store" class="js-example-basic-single form-control drop">
                                        <option value="{{ current_store()->id }}">{{ current_store()->name }}
                                        </option>
                                    </select>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_category">Price Category <span class="text-danger">*</span></label>
                                <select name="price_category" id="price_category" class="form-control" required>
                                    <option value="">Select Price Category</option>
                                    @foreach($priceCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Product Category</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
{{-- 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="format">Export Format <span class="text-danger">*</span></label>
                                <select name="format" id="format" class="form-control" required>
                                    <option value="excel">Excel (.xlsx)</option>
                                    <option value="csv">CSV (.csv)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 justify-content-end d-flex">
                            <a href="{{ route('home') }}" class="btn btn-danger ml-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Export Stock
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push("page_scripts")
    <script>
        $(document).ready(function () {
            $('.form-control').select2({
                placeholder: function () {
                    return $(this).data('placeholder') || 'Select an option';
                }
            });
        });
    </script>
@endpush