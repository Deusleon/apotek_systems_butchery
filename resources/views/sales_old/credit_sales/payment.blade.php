@extends("layouts.master")

@section('content-title')
Credits Payment
@endsection

@section('content-sub-title')
<li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
<li class="breadcrumb-item"><a href="#">Sales Management / Credits Payment</a> </li>
@endsection


@section("content")

<div class="col-sm-12">
    <div class="card-block">
        <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">
                                    <div class="row">
                                      <div class="col-md-3"></div>
                                        <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="code">Customer Name</label>
                                                  <select name="customer_id" id="customer_payment" class="js-example-basic-single form-control">
                                                <option value="">Select or Search Customer Name</option>
                                                @foreach($customers as $customer)
                                                 <option value="{{$customer->id}}">{{$customer->name}}</option>
                                                @endforeach
                                            </select>
                                                </div>

                                        </div>
                                     <div class="col-md-3"></div>
                                    </div>

                                     <div class="row" id="detail"><hr>
                                         <div class="table teble responsive" style="width: 100%;">
                                     <table id="credit_payment_table" class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <thead>
                                <tr>
                                    <th>Receipt#</th>
                                    <th>Customer Name</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Oustanding Amount</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>
                            <input type="hidden" id="vat" value="0.18">
                                    </div>

                                     </div>
                                   </div>
                                     @include('sales.credit_sales.create_payment')

    </div>
</div>


@endsection


@push("page_scripts")
@include('partials.notification')
 <script type="text/javascript">
            var config = {
                token:'{{ csrf_token() }}',
                routes: {
                    getCreditSale: '{{route('getCreditSale')}}'

                }
            };

        </script> 

<script src="{{asset("assets/plugins/moment/js/moment.js")}}"></script>
<script src="{{asset("assets/apotek/js/sales.js")}}"></script>
@endpush
