@extends('layouts.master')

@section('content-title')
    Lookup Transport Order
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('transport-orders.index') }}">Transport Orders</a></li>
    <li class="breadcrumb-item active">Lookup</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Enter Transport Order Number</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="GET" action="{{ route('payments.create', ['transportOrder' => request('order_number')]) }}">
    <div class="form-group">
        <label for="order_number">Order Number *</label>
        <input type="text" class="form-control" id="order_number" name="order_number" required>
    </div>

    <div class="form-group text-right">
        <button type="submit" class="btn btn-primary">Next</button>
    </div>
</form>
            </div>
        </div>
    </div>
</div>
@endsection
