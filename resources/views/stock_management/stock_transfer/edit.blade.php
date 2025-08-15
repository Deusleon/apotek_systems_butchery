@extends('layouts.master')

@section('page_css')
    <style>
        .table td,
        .table th {
            padding: .5rem;
        }
    </style>
@endsection

@section('content-title')
    Edit Stock Transfer
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('stock-transfer-history') }}">Stock Transfer History</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Edit Transfer #{{ $transfers->first()->transfer_no }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('stock-transfer.update', $transfers->first()->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="from_store">From Store</label>
                                <input type="text" class="form-control" id="from_store" value="{{ $transfers->first()->fromStore->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="to_store">To Store</label>
                                <input type="text" class="form-control" id="to_store" value="{{ $transfers->first()->toStore->name }}" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6>Products</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity Transferred</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $index => $transfer)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="transfers[{{ $index }}][id]" value="{{ $transfer->id }}">
                                            {{ $transfer->currentStock->product->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <input type="number" name="transfers[{{ $index }}][transfer_qty]" class="form-control" value="{{ $transfer->transfer_qty }}" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control">{{ $transfers->first()->remarks }}</textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Transfer</button>
                        <a href="{{ route('stock-transfer-history') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

