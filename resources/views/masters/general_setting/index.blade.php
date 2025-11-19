@extends("layouts.master")

@section('content-title')
    Terms and Conditions

@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Settings / General / Terms and conditions</a></li>
@endsection
@section("content")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                @if(auth()->user()->checkPermission('Edit Terms and Conditions'))
                    <h5>Update Terms and Conditions</h5>
                @elseif(!auth()->user()->checkPermission('Edit Terms and Conditions'))
                    <h5>View Terms and Conditions</h5>
                @endif
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $setting = $generalSettings->first() ?? new \App\GeneralSetting();
                @endphp

                <form action="{{route('general-settings.updateReceipt')}}" method="post">
                    @csrf()
                    @method("PUT")

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cash_sale_terms">Cash Sales</label>
                                <textarea class="form-control" id="cash_sale_terms" name="cash_sale_terms" rows="4"
                                    placeholder="Enter terms & conditions for cash sales"
                                    @if(!auth()->user()->checkPermission('Edit Terms and Conditions')) readonly
                                    @endif>{{old('cash_sale_terms', $setting->cash_sale_terms)}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="credit_sale_terms">Credit Sales</label>
                                <textarea class="form-control" id="credit_sale_terms" name="credit_sale_terms" rows="4"
                                    placeholder="Enter terms & conditions for credit sales"
                                    @if(!auth()->user()->checkPermission('Edit Terms and Conditions')) readonly
                                    @endif>{{old('credit_sale_terms', $setting->credit_sale_terms)}}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="proforma_invoice_terms">Proforma Invoice</label>
                                <textarea class="form-control" id="proforma_invoice_terms" name="proforma_invoice_terms"
                                    rows="4" placeholder="Enter terms & conditions for proforma invoices"
                                    @if(!auth()->user()->checkPermission('Edit Terms and Conditions')) readonly
                                    @endif>{{old('proforma_invoice_terms', $setting->proforma_invoice_terms)}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="purchase_order_terms">Purchase Order</label>
                                <textarea class="form-control" id="purchase_order_terms" name="purchase_order_terms"
                                    rows="4" placeholder="Enter terms & conditions for purchase orders"
                                    @if(!auth()->user()->checkPermission('Edit Terms and Conditions')) readonly
                                    @endif>{{old('purchase_order_terms', $setting->purchase_order_terms)}}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_note_terms">Delivery Note</label>
                                <textarea class="form-control" id="delivery_note_terms" name="delivery_note_terms" rows="4"
                                    placeholder="Enter terms & conditions for delivery note"
                                    @if(!auth()->user()->checkPermission('Edit Terms and Conditions')) readonly
                                    @endif>{{old('delivery_note_terms', $setting->delivery_note_terms)}}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="credit_note_terms">Credit Note</label>
                                <textarea class="form-control" id="credit_note_terms" name="credit_note_terms" rows="4"
                                    placeholder="Enter terms & conditions for credit note"
                                    @if(!auth()->user()->checkPermission('Edit Terms and Conditions')) readonly
                                    @endif>{{old('credit_note_terms', $setting->credit_note_terms)}}</textarea>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->checkPermission('Edit Terms and Conditions'))
                        <div class="row justify-content-end pr-1">
                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection

@push("page_scripts")
    @include('partials.notification')

    <!-- Input mask Js -->
    <script src="{{asset("assets/plugins/inputmask/js/inputmask.min.js")}}"></script>
    <script src="{{asset("assets/plugins/inputmask/js/jquery.inputmask.min.js")}}"></script>
    <script src="{{asset("assets/plugins/inputmask/js/autoNumeric.js")}}"></script>
    <!-- form-picker-custom Js -->
    <script src="{{asset("assets/js/pages/form-masking-custom.js")}}"></script>

    <script type="text/javascript">
        //Maintain the current Pill on reload
        $(function () {
            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                localStorage.setItem('lastPill', $(this).attr('href'));
            });
            var lastPill = localStorage.getItem('lastPill');
            if (lastPill) {
                $('[href="' + lastPill + '"]').tab('show');
            }
        });
    </script>

@endpush