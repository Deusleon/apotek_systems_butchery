@extends("layouts.master")

@section('content-title')
    General Settings

@endsection

@section('content-sub-title')
    Masters / General Settings

@endsection
@section("content")
<style type="text/css">
  .iti__flag {background-image: url("{{asset("assets/plugins/intl-tel-input/img/flags.png")}}");}

@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
 .iti__flag {background-image: url("{{asset("assets/plugins/intl-tel-input/img/flags@2x.png")}}");}
}
.iti { width: 100%; }
</style>
    @foreach($generalSettings as $setting)
        <div class="col-sm-12">
           <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="details-tab" data-toggle="pill" href="#details" role="tab" aria-controls="details" aria-selected="true">Pharmacy Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="general-setting-tab" data-toggle="pill" href="#general-settings" role="tab" aria-controls="general-settings" aria-selected="false">General Settings</a>
                                        </li>
                                          <li class="nav-item">
                                            <a class="nav-link" id="receipt-setting-tab" data-toggle="pill" href="#receipt-settings" role="tab" aria-controls="receipt-settings" aria-selected="false">Receipt Settings</a>
                                        </li>

                                    </ul>


            <div class="tab-content" id="myTabContent">
              
             <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <form action="{{route('general-settings.updateInfo','id')}}" method="post" enctype="multipart/form-data">
                        @csrf()
                        @method("PUT")
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                        <label for="code">Pharmacy Name</label>
                                        <input type="text" class="form-control" id="business_name" name="business_name"
                                               placeholder="Enter pharmacy name" value="{{$setting->business_name}}">
                                </div>
                                <div class="col-md-6 form-group">
                                        <label for="code">Registration Number</label>
                                        <input type="text" class="form-control" id="registration_number"
                                               name="registration_number"
                                               placeholder="Enter registration number" value="{{$setting->registration_number}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                        <label for="code">TIN Number</label>
                                        <input type="text" class="form-control"name="tin_number"
                                               placeholder="Enter TIN Number" value="{{$setting->tin_number}}">
                                </div>
                                <div class="col-md-6 form-group">
                                        <label for="code">VRN Number</label>
                                        <input type="text" class="form-control mob_no" name="vrn_number" data-mask="999-999-999"
                                               placeholder="Enter VRN Number" value="{{$setting->vrn_number}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                        <label for="code">Phone</label>
                                        <input type="text" class="form-control" id="phone_number" name="phone" 
                                       value="{{$setting->phone}}">
                                        <span id="valid-msg" class="hide"></span>
                                        <span id="error-msg" class="text text-danger"></span>
                                </div>

                                <div class="col-md-6 form-group">
                                        <label for="code">Email</label>
                                        <input type="text" class="form-control" id="email" name="email"
                                              
                                               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$"
                                               title="Eg:info@softlink.tz"
                                               placeholder="Enter your Email" value="{{$setting->email}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                        <label for="code">Website</label>
                                        <input type="text" class="form-control" id="website" name="website"
                                              
                                               pattern="https?://.+" title="Include http://"
                                               placeholder="Enter your website" value="{{$setting->website}}">
                                </div>
                                <div class="col-md-6 form-group">
                                        <label for="code">Logo</label>
                                           <input id="logo" type="file" class="form-control" name="logo">

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                        <label for="code">Address</label>
                                        <textarea type="text" class="form-control" id="address" name="address"
                                                 
                                                  placeholder="Enter your Address">{{$setting->address}}</textarea>
                                </div>

                                <div class="col-md-6 form-group">
                                        <label for="code">Slogan</label>
                                        <textarea type="text" class="form-control" id="slogan" name="slogan"
                                                 
                                                  placeholder="Enter your Slogan">{{$setting->slogan}}</textarea>
                                </div>
                            </div>
                            <input type="hidden" name="id" value="1">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="save_changes">Save Changes</button>
                        </div>
                    </form>
                </div>
                  <div class="tab-pane fade" id="general-settings" role="tabpanel" aria-labelledby="general-setting-tab">
                    <form action="{{route('general-settings.updateSetting','id')}}" method="post">
                        @csrf()
                        @method("PUT")
                        <div class="modal-body">
                         
                            <div class="row">
                              @if("$setting->make_batch_number_mandatory"=="Yes")
                                 <div class="col-md-6 form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-1" name="make_batch_number_mandatory" checked>
                                            <label for="switch-p-1" class="cr"></label>
                                        </div>
                                        <label>Make Batch Number Mandatory</label>
                                </div>
                              @else
                                 <div class="col-md-6 form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-1" name="make_batch_number_mandatory">
                                            <label for="switch-p-1" class="cr"></label>
                                        </div>
                                        <label>Make Batch Number Mandatory</label>
                                </div>
                              @endif
                             @if($setting->make_customer_name_mandatory=="Yes")
                               <div class="col-md-6 form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-2" name="make_customer_name_mandatory" checked>
                                            <label for="switch-p-2" class="cr"></label>
                                        </div>
                                        <label>Make Customer Name Mandatory</label>
                                </div>
                             @else
                              <div class="col-md-6 form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-2" name="make_customer_name_mandatory">
                                            <label for="switch-p-2" class="cr"></label>
                                        </div>
                                        <label>Make Customer Name Mandatory</label>
                                </div>
                             @endif
                            </div>
                            <div class="row">
                              @if($setting->enable_back_date_sale=="Yes")
                              <div class="col-md-6 form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-4" name="enable_back_date_sale" checked>
                                            <label for="switch-p-4" class="cr"></label>
                                        </div>
                                        <label>Enable Back Date Sale</label>
                                </div>
                              @else
                               <div class="col-md-6 form-group" >
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-4" name="enable_back_date_sale">
                                            <label for="switch-p-4" class="cr"></label>
                                        </div>
                                         <label>Enable Back Date Sale</label>
                                </div>
                              @endif

                              @if($setting->enable_cashflow_feature=="Yes")
                                  <div class="col-md-6 form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-3" name="enable_cashflow_feature" checked>
                                            <label for="switch-p-3" class="cr"></label>
                                        </div>
                                        <label>Enable Cashflow Feature</label>
                                </div>
                              @else
                              <div class="col-md-6 form-group" >
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-3" name="enable_cashflow_feature">
                                            <label for="switch-p-3" class="cr"></label>
                                        </div>
                                         <label>Enable Cashflow Feature</label>
                                </div>
                              @endif
                            </div>
                           
                            <div class="row">
                                @if($setting->make_invoice_number_mandatory=="Yes")
                              <div class="col-md-6 form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-5" name="make_invoice_number_mandatory" checked>
                                            <label for="switch-p-5" class="cr"></label>
                                        </div>
                                        <label>Make Invoice Number Mandatory</label>
                                  </div>
                             @else
                              <div class="col-md-6 form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-5" name="make_invoice_number_mandatory">
                                            <label for="switch-p-5" class="cr"></label>
                                        </div>
                                        <label>Make Invoice Number Mandatory</label>
                                  </div>
                             @endif

                              @if($setting->support_multstore=="Yes")
                              <div class="col-md-6 form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-6" name="support_multstore" checked>
                                            <label for="switch-p-6" class="cr"></label>
                                        </div>
                                        <label>Support Multi-Stores</label>
                                  </div>
                             @else
                              <div class="col-md-6 form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="switch-p-7" name="support_multstore">
                                            <label for="switch-p-7" class="cr"></label>
                                        </div>
                                        <label>Support Multi-Stores</label>
                                  </div>
                             @endif

                            </div>
                            <hr>
                            <div class="row">
                                        <div class="col-md-6 form-group">
                                        <label for="code">Good Receiving Option</label>
                                        <select  class="js-example-basic-single form-control" name="good_receiving_option">
                                            <option selected="selected">{{$setting->good_receiving_option}}</option>
                                            <option value="Total Cost/Qty">Total Cost/Qty</option>
                                            <option value="Total Cost*Qty">Total Cost*Qty</option>
                                        </select>
                                    </div>
                          
                                    <div class="col-md-6 form-group">
                                        <label for="code" id="store_label">Default Store</label>
                                            <select  class="js-example-basic-single form-control" id="store">
                                                @foreach($store as $store)
                                                 <option value="{{$store->id}}">{{$store->name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                            </div>

                            <input type="hidden" name="default_store_id" id="store_id">
                            <input type="hidden" name="id" value="1">
                            <input type="hidden" id="default_store"  value="{{$setting->default_store_id}}">
                            <input type="hidden" id="multstore"  value="{{$setting->support_multstore}}">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
               <div class="tab-pane fade" id="receipt-settings" role="tabpanel" aria-labelledby="receipt-setting-tab">
                    <form action="{{route('general-settings.updateReceipt','id')}}" method="post">
                        @csrf()
                        @method("PUT")
                        <div class="modal-body">
                            <div class="row">
                              @if($setting->receipt_printing=="Yes")
                              <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="receipt" name="receipt_printing" checked>
                                            <label for="receipt" class="cr"></label>
                                        </div>
                                        <label>Receipt Printing</label>
                                    </div>
                              </div>
                              @else
                               <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="receipt" name="receipt_printing">
                                            <label for="receipt" class="cr"></label>
                                        </div>
                                        <label>Receipt Printing</label>
                                    </div>
                              </div>
                              @endif
                              @if($setting->location_printing=="Yes")
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="location" name="location_printing" checked>
                                            <label for="location" class="cr"></label>
                                        </div>
                                        <label>Location Printing</label>
                                    </div>
                                </div>
                              @else
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="checkbox" id="location" name="location_printing">
                                            <label for="location" class="cr"></label>
                                        </div>
                                        <label>Location Printing</label>
                                    </div>
                                </div>
                              @endif

                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Number Of Copies</label>
                                        <input type="number" class="form-control" id="number_of_copies" name="number_of_copies"
                                               placeholder="Enter pharmacy name" value="{{$setting->number_of_copies}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Receipt Size </label>
                                        <select class="form-control" name="receipt_size">
                                         <option value="A4 / Letter">A4 / Letter</option>
                                          <option value="Thermal Paper">Thermal Paper</option>
                                          <option value="None">None</option>
                                          <option selected="{{$setting->receipt_size}}">{{$setting->receipt_size}}</option>
                                        </select>
                                    </div>
                                </div>
                              
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Percentage VAT (%)</label>
                                        <div class="switch switch-primary d-inline m-r-10">
                                            <input type="text" class="form-control autonumber"
                                                   name="vat_or_tax" value="{{$setting->vat_or_tax}}">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <input type="hidden" name="id" value="1">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endforeach
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
      var input = document.querySelector("#phone_number");
      var errorMsg = document.querySelector("#error-msg");
      var validMsg = document.querySelector("#valid-msg");
      var errorMap = ["Invalid Phone Number", "Invalid Country Code", "Too Short", "Too Long", "Invalid Phone Number"];
      var reset = function() {
      input.classList.remove("error");
      errorMsg.innerHTML = "";
      errorMsg.classList.add("hide");
      validMsg.classList.add("hide");
      };
      var iti = window.intlTelInput(input,{
      customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
      return "e.g. " + selectedCountryPlaceholder;
      },
      initialCountry: "auto",
      geoIpLookup: function(callback) {
      $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
      var countryCode = (resp && resp.country) ? resp.country : "";
      callback(countryCode);
      });
      },
      utilsScript: "../assets/plugins/intl-tel-input/js/utils.js?1562189064761",
      onlyCountries: ["tz","ug","ke","rw","bi","sd"],
      nationalMode:false,
      });
      input.addEventListener('keyup', reset);


      // on blur: validate
      input.addEventListener('blur', function() {
      reset();
      if (input.value.trim()) {
      if (iti.isValidNumber()) {
      $('#save_changes').prop('disabled',false);
      validMsg.classList.remove("hide");
      document.getElementById('phone_number').value = iti.getNumber();
      } 
      else
      {
      input.classList.add("error");
      $('#save_changes').prop('disabled',true);
      var errorCode = iti.getValidationError();
      errorMsg.innerHTML = errorMap[errorCode];
      errorMsg.classList.remove("hide");
      }
      }
      });

      // on keyup / change flag: reset
      input.addEventListener('change', reset);


//STORE SETTING STARTS
   var a=1;
   var multstore=document.getElementById('multstore').value;
   if(multstore=="No"){
    a=-1;
     document.getElementById('store_label').style.color='red';  
   $('#store').prop('disabled',true);
   }
   var default_store=document.getElementById('default_store').value;
   var store_id =document.getElementById('store').value;
   document.getElementById('store_id').value=store_id;
   document.getElementById('store').value=default_store;
   $("#switch-p-6").on('change', function() {
       a=-a;
      allowStore(a);
     });
    
   $("#switch-p-7").on('change', function() {
      a=-a;
    allowStore(a);
 
     });
   function allowStore(a){
       if(a>0){
        document.getElementById('store_label').style.color='black';
        $('#store').prop('disabled',false);
     }
     else{
        document.getElementById('store_label').style.color='red';  
        $('#store').prop('disabled',true);
     }

}



//Maintain the current Pill on reload
$(function() { 
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
