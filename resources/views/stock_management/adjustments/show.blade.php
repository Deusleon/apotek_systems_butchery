@extends('layouts.master')

@section('content-title')
    View Stock Adjustment
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('stock-adjustments-history') }}">Stock Adjustments</a></li>
    <li class="breadcrumb-item"><a href="#">View</a></li>
@endsection

@section('content')
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <div class="float-right">
                <a href="{{ route('stock-adjustments-history') }}" class="btn btn-primary">
                    <i class="feather icon-list"></i> Back to List
                </a>
                <a href="{{ route('stock-adjustments.create') }}" class="btn btn-success">
                    <i class="feather icon-plus"></i> New Adjustment
                </a>
            </div>
            <h5>Stock Adjustment Details</h5>
            @if($adjustment->reference_number)
                <div class="text-muted">Reference: {{ $adjustment->reference_number }}</div>
            @endif
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-{{ $adjustment->adjustment_type === 'increase' ? 'success' : 'danger' }}">
                        <h5><i class="icon fas fa-{{ $adjustment->adjustment_type === 'increase' ? 'arrow-up' : 'arrow-down' }}"></i> 
                            {{ ucfirst($adjustment->adjustment_type) }} Adjustment
                        </h5>
                        Stock was {{ $adjustment->adjustment_type === 'increase' ? 'increased' : 'decreased' }} by 
                        <strong>{{ abs($adjustment->adjustment_quantity) }}</strong> units on 
                        <strong>{{ $adjustment->created_at->format('Y-m-d H:i') }}</strong>.
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Adjustment Information</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Date</th>
                                    <td>{{ $adjustment->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Product</th>
                                    <td>{{ optional($adjustment->currentStock)->product->name ?? 'Unknown Product' }}</td>
                                </tr>
                                <tr>
                                    <th>Store</th>
                                    <td>{{ optional($adjustment->store)->name ?? 'Unknown Store' }}</td>
                                </tr>
                                <tr>
                                    <th>Adjusted By</th>
                                    <td>{{ optional($adjustment->user)->name ?? 'Unknown User' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-outline card-info">
                        <div class="card-header">
                            <h3 class="card-title">Adjustment Details</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Type</th>
                                    <td>
                                        <span class="badge badge-{{ $adjustment->adjustment_type === 'increase' ? 'success' : 'danger' }}">
                                            {{ ucfirst($adjustment->adjustment_type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Adjusted Quantity</th>
                                    <td>{{ abs($adjustment->adjustment_quantity) }}</td>
                                </tr>
                                <tr>
                                    <th>Previous Quantity</th>
                                    <td>{{ $adjustment->previous_quantity }}</td>
                                </tr>
                                <tr>
                                    <th>New Quantity</th>
                                    <td>{{ $adjustment->new_quantity }}</td>
                                </tr>
                                <tr>
                                    <th>Reason</th>
                                    <td>{{ $adjustment->reason }}</td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td>{{ $adjustment->notes ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 