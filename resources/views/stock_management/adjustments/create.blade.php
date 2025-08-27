@extends('layouts.master')

@section('content-title')
    Create Stock Adjustment
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('stock-adjustments-history') }}">Stock Adjustments</a></li>
    <li class="breadcrumb-item"><a href="#">Create</a></li>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<style>
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
    }
</style>
@endsection

@section('content')
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>New Stock Adjustment</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('stock-adjustments.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="current_stock_id">Product <span class="text-danger">*</span></label>
                            <select name="current_stock_id" id="current_stock_id" class="form-control select2 @error('current_stock_id') is-invalid @enderror" required>
                                <option value="">Select Product</option>
                                @foreach($stocks as $stock)
                                    <option value="{{ $stock->id }}">
                                        {{ optional($stock->product)->name ?? 'Unknown Product' }} (Current: {{ $stock->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('current_stock_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="adjustment_type">Adjustment Type <span class="text-danger">*</span></label>
                            <select name="adjustment_type" id="adjustment_type" class="form-control @error('adjustment_type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                <option value="increase">Increase</option>
                                <option value="decrease">Decrease</option>
                            </select>
                            @error('adjustment_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="adjustment_quantity">Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="adjustment_quantity" id="adjustment_quantity" 
                                   class="form-control @error('adjustment_quantity') is-invalid @enderror" 
                                   value="{{ old('adjustment_quantity') }}" required>
                            @error('adjustment_quantity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reason">Reason <span class="text-danger">*</span></label>
                            <select name="reason" id="reason" class="form-control select2 @error('reason') is-invalid @enderror" required>
                                <option value="">Select Reason</option>
                                @foreach($reasons as $reason)
                                    <option value="{{ $reason->reason }}" {{ old('reason') == $reason->reason ? 'selected' : '' }}>
                                        {{ $reason->reason }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reason')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather icon-save"></i> Save Adjustment
                    </button>
                    <a href="{{ route('stock-adjustments-history') }}" class="btn btn-light">
                        <i class="feather icon-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select an option'
        });

        // Update the quantity sign based on adjustment type
        $('#adjustment_type').change(function() {
            var quantityInput = $('#adjustment_quantity');
            if (this.value === 'decrease') {
                quantityInput.val(Math.abs(quantityInput.val()));
            } else {
                quantityInput.val(Math.abs(quantityInput.val()));
            }
        });
    });
</script>
@endpush 