@extends('layouts.app')

@section('title', 'Edit Payment')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('transport-orders.index') }}">Transport Orders</a></li>
            <li class="breadcrumb-item"><a href="{{ route('transport-orders.payments.index', $transportOrder) }}">Payments for #{{ $transportOrder->order_number }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Payment</a></li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Payment</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @include('payments.partials.payment_form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
