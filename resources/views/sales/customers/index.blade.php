@extends("layouts.master")

@section('content-title')
    Customers
@endsection
@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Customers</a></li>
@endsection

@section("content")

    <style type="text/css">
        .iti__flag {
            background-image: url("{{asset("assets/plugins/intl-tel-input/img/flags.png")}}");
        }

        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .iti__flag {
                background-image: url("{{asset("assets/plugins/intl-tel-input/img/flags@2x.png")}}");
            }
        }

        .iti {
            width: 100%;
        }
    </style>

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">
                    @can('Manage Customers')
                        <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-sm btn-secondary"
                                data-toggle="modal"
                                data-target="#create">
                            Add Customer
                        </button>
                    @endcan
                    <div class="table-responsive">
                        <table id="fixed-header" class="display table nowrap table-striped table-hover"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Total Credit</th>
                                <th>Credit Limit</th>
                                <th>Phone</th>
                                <!-- <th>Email</th> -->
                                @can('Manage Customers')
                                    <th>Action</th>
                                @endcan

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{$customer->name}}</td>
                                    <td>{{number_format($customer->total_credit,2)}}</td>
                                    <td>{{number_format($customer->credit_limit,2)}}</td>
                                    <td>{{$customer->phone}}</td>
                                    {{--

                                      @if($customer->email)
                                    <td>{{$customer->email}}</td>
                                    @else
                                    <td><div class="text text-danger">{{"Empty"}}</div></td>
                                    @endif

                                     --}}
                                    @can('Manage Customers')
                                        <td>
                                            <a href="#">
                                                <button class="btn btn-sm btn-rounded btn-primary"
                                                        data-id="{{$customer->id}}"
                                                        data-name="{{$customer->name}}"
                                                        data-credit_limit="{{$customer->credit_limit}}"
                                                        data-address="{{$customer->address}}"
                                                        data-phone="{{$customer->phone}}"
                                                        data-email="{{$customer->email}}"

                                                        type="button"
                                                        data-toggle="modal" data-target="#edit">Edit
                                                </button>
                                            </a>
                                            <a href="#">
                                                <button class="btn btn-sm btn-rounded btn-danger"
                                                        data-id="{{$customer->id}}"
                                                        data-name="{{$customer->name}}"
                                                        type="button"
                                                        data-toggle="modal"
                                                        data-target="#delete">
                                                    Delete
                                                </button>
                                            </a>
                                        </td>
                                    @endcan
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>

                        <input type="hidden" value="" id="category">
                        <input type="hidden" value="" id="customers">
                        <input type="hidden" value="" id="print">


                    </div>

                </div>
            </div>
        </div>

        @include('sales.customers.create')
        @include('sales.customers.delete')
        @include('sales.customers.edit')

        @endsection


        @push("page_scripts")
            @include('partials.notification')
            <script src="{{asset("assets/apotek/js/customer.js")}}"></script>
    @endpush
