@extends('layouts.master')

@section('page_css')
    <style>
        .table td, .table th {
            padding: .5rem;
        }
    </style>
@endsection

@section('content-title')
    Stock Transfer Details
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('stock-transfer-history') }}">Stock Transfer History</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Transfer Details for #{{ $transfers->first()->transfer_no }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>From Store:</strong> {{ $transfers->first()->fromStore->name }}</p>
                        <p><strong>To Store:</strong> {{ $transfers->first()->toStore->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Transfer Date:</strong> {{ $transfers->first()->created_at->format('d-m-Y') }}</p>
                        <p><strong>Status:</strong> <span class="badge badge-secondary">{{ $transfers->first()->status_text }}</span></p>
                    </div>
                </div>
                <hr>
                <h6>Transferred Products</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity Transferred</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transfers as $transfer)
                                <tr>
                                    <td>{{ $transfer->currentStock->product->name ?? 'N/A' }}</td>
                                    <td>{{ $transfer->transfer_qty }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <a href="{{ route('stock-transfer-history') }}" class="btn btn-secondary">Back to History</a>
                </div>
            </div>
        </div>
    </div>
@endsection
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" class="form-control" id="description_edit"
                                               name="description"
                                               placeholder="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" class="form-control" id="status_edit" readonly>
                                            <option value="">Drafted</option>
                                            <option value="">Open</option>
                                            <option value="">Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="id" id="id">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

