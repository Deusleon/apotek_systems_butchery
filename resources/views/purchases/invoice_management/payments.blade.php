@extends("layouts.master")

@section('content-title')
Invoices

@endsection

@section('content-sub-title')
<li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
<li class="breadcrumb-item"><a href="#">Accounting / Invoices/ Payments </a></li>
@endsection

@section("content")
<div class="col-sm-12">
    <ul class="nav nav-pills mb-3" id="myTab">
            @if (auth()->user()->checkPermission('View Purchase Return'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" href="{{ url('accounting/invoices') }}">Invoices
                    </a>
                </li>
            @endif
            @if (auth()->user()->checkPermission('View Purchase Returns Approval'))
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" href="{{ url('accounting/invoices/payments') }}">Payments
                    </a>
                </li>
            @endif
    </ul>
    <div class="card">
        <div class="card-body">

            


        </div>
    </div>
</div>


@endsection