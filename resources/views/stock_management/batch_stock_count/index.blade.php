@extends("layouts.master")

@section('content-title')
    Batch Stock Count
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Batch Stock Count </a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('batch-stock-count.preview') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label for="file" class="col-md-3 col-form-label text-md-right">Upload Excel/CSV File <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input id="file" type="file" class="form-control @error('file') is-invalid @enderror" name="file" required>
                            @error('file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">Accepted formats: .xlsx, .xls, .csv (Max 20MB)</small>
                            <small class="form-text text-info">Template: Product ID | Physical Stock</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="store_id" class="col-md-3 col-form-label text-md-right">Select Store <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <select name="store_id" id="store_id" class="form-control select2 @error('store_id') is-invalid @enderror" required>
                                <option value="">Select Store</option>
                                @foreach(\App\Store::all() as $store)
                                    <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('store_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-3">
                            <button type="submit" class="btn btn-primary">
                                Preview & Upload
                            </button>
                        </div>
                    </div>
                </form>

                @if(session('success'))
                    <div class="alert alert-success mt-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning mt-4">
                        {{ session('warning') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger mt-4">
                        {{ session('error') }}
                    </div>
                @endif

                <h4 class="mt-4">Recent Batch Stock Counts</h4>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Store</th>
                                <th>Total Records</th>
                                <th>Successful</th>
                                <th>Failed</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be populated here via AJAX or direct pass if we implement a history table --}}
                            <tr>
                                <td colspan="7" class="text-center">No recent batch stock counts to display.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    @include('partials.notification')
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush 