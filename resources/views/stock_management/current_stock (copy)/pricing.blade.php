@extends('layouts.master')

@section('content-title')
    Price Management
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Price Management </a></li>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Price Management</h5>
            </div>
            <div class="card-body">
                <form id="price-form" method="POST" action="{{ route('update-price') }}">
                    @csrf
                    <input type="hidden" name="stock_id" value="{{ $stock->id }}">
                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Product</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $stock->product->name }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Unit Cost</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ number_format($stock->unit_cost, 2) }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Price Category</label>
                        <div class="col-sm-10">
                            <select name="price_category_id" class="form-control" id="price-category">
                                @foreach($priceCategories as $category)
                                    <option value="{{ $category->id }}" 
                                            data-markup="{{ $category->default_markup_percentage }}"
                                            {{ $priceList && $priceList->price_category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->default_markup_percentage }}% markup)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Price Type</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="default-price" name="price_type" class="custom-control-input" value="default" 
                                       {{ !$priceList || !$priceList->is_custom ? 'checked' : '' }}>
                                <label class="custom-control-label" for="default-price">Default Price</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="custom-price" name="price_type" class="custom-control-input" value="custom"
                                       {{ $priceList && $priceList->is_custom ? 'checked' : '' }}
                                       {{ auth()->user()->can('override product prices') ? '' : 'disabled' }}>
                                <label class="custom-control-label" for="custom-price">Custom Price</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row default-price-section" {{ $priceList && $priceList->is_custom ? 'style=display:none' : '' }}>
                        <label class="col-sm-2 col-form-label">Default Markup %</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="markup-percentage" step="0.01" readonly
                                   value="{{ $priceList ? $priceList->default_markup_percentage : ($priceCategory ? $priceCategory->default_markup_percentage : 0) }}">
                            <small class="form-text text-muted">This is set at the price category level</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Price</label>
                        <div class="col-sm-10">
                            <input type="number" name="price" class="form-control" id="price" step="0.01" required
                                   value="{{ $priceList ? $priceList->price : 0 }}"
                                   {{ !$priceList || !$priceList->is_custom ? 'readonly' : '' }}>
                            <small class="form-text text-muted calculated-price">
                                Calculated price: {{ $calculatedPrice ? number_format($calculatedPrice, 2) : 'N/A' }}
                            </small>
                        </div>
                    </div>

                    <div class="form-group row custom-price-section" {{ !$priceList || !$priceList->is_custom ? 'style=display:none' : '' }}>
                        <label class="col-sm-2 col-form-label">Override Reason</label>
                        <div class="col-sm-10">
                            <textarea name="override_reason" class="form-control" rows="3"
                                      {{ auth()->user()->can('override product prices') ? '' : 'readonly' }}>{{ $priceList ? $priceList->override_reason : '' }}</textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="{{ route('current-stock') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>

                @if($priceHistory && auth()->user()->can('view price history'))
                    <div class="mt-4">
                        <h5>Price History</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Changed By</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($priceHistory as $history)
                                    <tr>
                                        <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ number_format($history->price, 2) }}</td>
                                        <td>{{ $history->priceCategory->name }}</td>
                                        <td>{{ $history->is_custom ? 'Custom' : 'Default' }}</td>
                                        <td>{{ $history->overriddenBy ? $history->overriddenBy->name : 'System' }}</td>
                                        <td>{{ $history->override_reason }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
<script>
$(document).ready(function() {
    function updatePriceFields() {
        var priceType = $('input[name="price_type"]:checked').val();
        var isCustom = priceType === 'custom';
        
        if (isCustom) {
            $('.custom-price-section').show();
            $('.default-price-section').hide();
            $('#price').prop('readonly', false);
        } else {
            $('.custom-price-section').hide();
            $('.default-price-section').show();
            $('#price').prop('readonly', true);
            calculateDefaultPrice();
        }
    }

    function calculateDefaultPrice() {
        var unitCost = parseFloat('{{ $stock->unit_cost }}');
        var markup = parseFloat($('#price-category option:selected').data('markup'));
        
        if (!isNaN(unitCost) && !isNaN(markup)) {
            var calculatedPrice = unitCost * (1 + (markup / 100));
            $('#price').val(calculatedPrice.toFixed(2));
            $('.calculated-price').text('Calculated price: ' + calculatedPrice.toFixed(2));
        }
    }

    $('input[name="price_type"]').change(updatePriceFields);
    $('#price-category').change(function() {
        $('#markup-percentage').val($(this).find(':selected').data('markup'));
        if ($('input[name="price_type"]:checked').val() === 'default') {
            calculateDefaultPrice();
        }
    });

    // Initial setup
    updatePriceFields();
});
</script>
@endpush
