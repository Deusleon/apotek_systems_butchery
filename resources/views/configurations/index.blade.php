@extends("layouts.master")

@section('page_css')
    <style>
        img {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
@endsection

@section('content-title')
    Configurations
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Settings / General / Configurations</a></li>
@endsection

@section("content")
    <style type="text/css">
        .iti {
            width: 100%;
        }
    </style>

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <diwev class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    {{-- <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm" --}}
                        {{-- data-toggle="modal" --}} {{-- data-target="#create">--}}
                        {{-- Add Setting--}}
                        {{-- </button>--}}

                    <div class="table-responsive">
                        <table id="setting_table" class="display table nowrap table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th hidden>ID</th>
                                    <th>Name</th>
                                    <th>Value</th>
                                    @if (auth()->user()->checkPermission('Edit Configurations'))
                                        <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @dd($configurations) --}}
                                <tr>
                                    @foreach($configurations as $setting)
                                            <td hidden>{{$setting->id}}</td>
                                            <td>{{$setting->display_name}}</td>
                                            @if($setting->id == 105)
                                                <td><img src="/fileStore/logo/{{$setting->value}}" /></td>
                                            @elseif($setting->id == 120)
                                                <td>{{$setting->value . '%'}}</td>
                                            @else
                                                <td>{{$setting->value}}</td>
                                            @endif
                                            @if (auth()->user()->checkPermission('Edit Configurations'))
                                                <td>
                                                    <a href="#">
                                                        <button class="btn btn-sm btn-rounded btn-primary" id="edit_btn">Edit
                                                        </button>
                                                    </a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach


                            </tbody>
                        </table>
                    </div>
                    <!-- [ configuration table ] end -->
                </diwev>
            </div>
        </div>
    </div>
    @include('configurations.create')
    @include('configurations.edit')

@endsection

@push("page_scripts")
    @include('partials.notification')

    <!-- Input mask Js -->
    <script src="{{asset("assets/plugins/inputmask/js/inputmask.min.js")}}"></script>
    <script src="{{asset("assets/plugins/inputmask/js/jquery.inputmask.min.js")}}"></script>
    <script src="{{asset("assets/plugins/inputmask/js/autoNumeric.js")}}"></script>
    <!-- form-picker-custom Js -->
    <script src="{{asset("assets/js/pages/form-masking-custom.js")}}"></script>

    <script>


        var setting_table = $('#setting_table').DataTable();

        $('#setting_table tbody').on('click', '#edit_btn', function () {
            var data = setting_table.row($(this).parents('tr')).data();
            $('#edit').modal('show');
            $('#edit').find('.modal-header #heading').text('Edit ' + data[1]);
            $('#edit').find('.modal-body #label').text(data[1]);
            $('#edit').find('.modal-body #id').val(data[0]);
            var element = document.createElement("input");
            var appended = document.getElementById("appended");
            var phone_number = document.getElementById("phone_number");
            document.getElementById("formInput").innerHTML = '';
            switch (Number(data[0])) {
                case 100:
                    element.setAttribute("type", "text");
                    element.setAttribute("id", "appended");
                    element.setAttribute("value", data[2]);
                    element.setAttribute("name", "formdata");
                    element.setAttribute("class", "form-control");
                    element.setAttribute("placeholder", "Enter Business Name");
                    document.getElementById("formInput").appendChild(element);
                    break;
                case 101:
                    element.setAttribute("type", "text");
                    element.setAttribute("id", "appended");
                    element.setAttribute("value", data[2]);
                    element.setAttribute("name", "formdata");
                    element.setAttribute("class", "form-control");
                    element.setAttribute("placeholder", "Enter Registration Number");
                    document.getElementById("formInput").appendChild(element);
                    break;
                case 102:
                    element.setAttribute("type", "text");
                    element.setAttribute("id", "appended");
                    element.setAttribute("data-mask", "999-999-999");
                    element.setAttribute("value", data[2]);
                    element.setAttribute("name", "formdata");
                    element.setAttribute("class", "form-control mob_no");
                    element.setAttribute("placeholder", "Enter TIN Number");
                    document.getElementById("formInput").appendChild(element);
                    break;
                case 103:
                    element.setAttribute("type", "text");
                    element.setAttribute("id", "appended");
                    element.setAttribute("value", data[2]);
                    element.setAttribute("name", "formdata");
                    element.setAttribute("class", "form-control");
                    element.setAttribute("placeholder", "Enter VRN Number");
                    document.getElementById("formInput").appendChild(element);
                    break;
                case 104:
                    element = document.createElement("textarea");
                    element.setAttribute("type", "text");
                    element.setAttribute("id", "slogan");
                    element.setAttribute("value", data[2]);
                    element.setAttribute("name", "formdata");
                    element.setAttribute("class", "form-control");
                    element.setAttribute("placeholder", "Enter Slogan");
                    document.getElementById("formInput").appendChild(element);
                    break;
                case 105:
                    element.setAttribute("type", "file");
                    element.setAttribute("id", "appended");
                    element.setAttribute("name", "logo");
                    element.setAttribute("class", "form-control");
                    element.setAttribute("placeholder", "Select Logo to Upload");
                    document.getElementById("formInput").appendChild(element);
                    break;
                case 106:
                    element = document.createElement("input");
                    element.setAttribute("type", "text");
                    element.setAttribute("id", "appended");
                    element.setAttribute("value", data[2]);
                    element.setAttribute("name", "formdata");
                    element.setAttribute("class", "form-control");
                    element.setAttribute("placeholder", "Enter Address");
                    document.getElementById("formInput").appendChild(element);
                    break;
                case 107:
                    element.setAttribute("type", "text");
                    element.setAttribute("id", "phone_number");
                    element.setAttribute("name", "formdata");
                    element.setAttribute("class", "form-control");
                    document.getElementById("formInput").appendChild(element);
                    var input = document.querySelector("#phone_number");
                    var errorMsg = document.querySelector("#error-msg");
                    var validMsg = document.querySelector("#valid-msg");
                    var errorMap = ["Invalid Phone Number", "Invalid Country Code", "Too Short", "Too Long", "Invalid Phone Number"];
                    var reset = function () {
                        input.classList.remove("error");
                        errorMsg.innerHTML = "";
                        errorMsg.classList.add("hide");
                        validMsg.classList.add("hide");
                    };
                    var iti = window.intlTelInput(input, {
                        customPlaceholder: function (selectedCountryPlaceholder, selectedCountryData) {
                            return "e.g. " + selectedCountryPlaceholder;
                        },
                        initialCountry: "auto",
                        geoIpLookup: function (callback) {
                            $.get('https://ipinfo.io', function () {
                            }, "jsonp").always(function (resp) {
                                var countryCode = (resp && resp.country) ? resp.country : "";
                                callback(countryCode);
                            });
                        },
                        utilsScript: "../assets/plugins/intl-tel-input/js/utils.js?1562189064761",
                        onlyCountries: ["tz", "ug", "ke", "rw", "bi", "sd", "zm", "zw", "mw", "mz", "bw", "za", "na", "ao", "cd", "cg", "ga", "gq", "cm", "td", "cf", "ss", "et", "dj", "er", "so", "mg", "sc", "mu", "km", "yt", "re", "bf", "bj", "ci", "cv", "gh", "gm", "gn", "gw", "lr", "ml", "mr", "ne", "ng", "sh", "sl", "sn", "st", "tg", "eh", "eg", "ly", "ma", "tn", "dz", "jo", "lb", "sy", "iq", "kw", "sa", "ye", "om", "ae", "qa", "bh", "cy", "il", "ps", "tr", "az", "am", "ge", "ru", "by", "ua", "md", "ro", "bg", "mk", "al", "me", "rs", "ba", "hr", "si", "sk", "cz", "pl", "hu", "at", "ch", "li", "de", "be", "nl", "lu", "fr", "mc", "ad", "es", "pt", "gi", "it", "sm", "va", "mt", "gr", "cy", "gb", "ie", "dk", "no", "se", "fi", "is", "sj", "fo", "ax", "ee", "lv", "lt"],
                        nationalMode: false,
                    });
                    input.addEventListener('keyup', reset);


                    // on blur: validate
                    input.addEventListener('blur', function () {
                        reset();
                        if (input.value.trim()) {
                            if (iti.isValidNumber()) {
                                $('#update').prop('disabled', false);
                                validMsg.classList.remove("hide");
                                document.getElementById('phone_number').value = iti.getNumber();
                            } else {
                                input.classList.add("error");
                                $('#update').prop('disabled', true);
                                var errorCode = iti.getValidationError();
                                errorMsg.innerHTML = errorMap[errorCode];
                                errorMsg.classList.remove("hide");
                            }
                        }
                    });

                    // on keyup / change flag: reset
                    input.addEventListener('change', reset);

                    break;
                case 108:
                    element.setAttribute("type", "text");
                    element.setAttribute("id", "appended");
                    element.setAttribute("value", data[2]);
                    element.setAttribute("name", "formdata");
                    element.setAttribute("pattern", "[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$");
                    element.setAttribute("class", "form-control");
                    element.setAttribute("placeholder", "Enter Email Address");
                    document.getElementById("formInput").appendChild(element);
                    break;
                case 109:
                    element.setAttribute("type", "text");
                    element.setAttribute("id", "appended");
                    element.setAttribute("value", data[2]);
                    element.setAttribute("name", "formdata");
                    // element.setAttribute("pattern", "https?://.+");
                    element.setAttribute("title", "Include http://");
                    element.setAttribute("class", "form-control");
                    element.setAttribute("placeholder", "Enter Website");
                    document.getElementById("formInput").appendChild(element);
                    break;
                case 110:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="batch_number"><option value="YES">YES</option><option value="NO">NO</option></select>';
                    break;
                case 111:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="discount"><option value="YES">YES</option><option value="NO">NO</option></select>';
                    break;
                case 112:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="paid"><option value="YES">YES</option><option value="NO">NO</option></select>';
                    break;
                case 114:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="back_date"><option value="YES">YES</option><option value="NO">NO</option></select>';
                    break;
                case 115:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="invoice"><option value="YES">YES</option><option value="NO">NO</option></select>';
                    break;
                case 117:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="reprint_receipt"><option value="YES">YES</option><option value="NO">NO</option></select>';
                    break;
                case 119:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="receipt_size">' +
                        '<option value="A4 / Letter">A4 / Letter</option>' +
                        '<option value="A5 / Half Letter">A5 / Half Letter</option>' +
                        '<option value="80mm Thermal Paper">80mm Thermal Paper</option>' +
                        '<option value="58mm Thermal Paper">58mm Thermal Paper</option>' +
                        '<option value="None">None</option></select>';
                    break;
                case 120:
                    element.setAttribute("type", "number");
                    element.setAttribute("min", "0");
                    element.setAttribute("id", "vat");
                    element.setAttribute("value", data[2]);
                    element.setAttribute("name", "formdata");
                    element.setAttribute("class", "form-control");
                    element.setAttribute("placeholder", "Enter VAT in %");
                    document.getElementById("formInput").appendChild(element);
                    break;
                case 121:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="support_multi_store"><option value="YES">YES</option><option value="NO">NO</option></select>';
                    break;
                case 122:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="store">   @foreach($store as $store) @if($store->id > 1)<option value="{{$store->name}}">{{$store->name}}</option>@endif @endforeach</select>';
                    break;
                case 123:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="back_date"><option value="YES">YES</option><option value="NO">NO</option></select>';
                    break;
                case 124:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="back_date"><option value="YES">YES</option><option value="NO">NO</option></select>';
                    break;
                case 125:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="saletype">   @foreach($sale_types as $sale_type)<option value="{{$sale_type->name}}">{{$sale_type->name}}</option>@endforeach</select>';
                    break;
                case 126:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="back_date"><option value="YES">YES</option><option value="NO">NO</option></select>';
                    break;
                case 127:
                    document.getElementById("formInput").innerHTML = '<select  class="js-example-basic-single form-control"name="formdata" id="product_details_option"><option value="Normal">Normal</option><option value="Detailed">Detailed</option></select>';
                    break;
                default:
                // code block
            }

            if ($('#receipt_size').length) {
                document.getElementById('receipt_size').value = data[2];
            }

            if ($('#back_date').length) {
                document.getElementById('back_date').value = data[2];
            }
            
            if ($('#reprint_receipt').length) {
                document.getElementById('reprint_receipt').value = data[2];
            }

            if ($('#invoice').length) {
                document.getElementById('invoice').value = data[2];
            }

            if ($('#phone_number').length) {
                document.getElementById('phone_number').value = data[2];
            }

            if ($('#batch_number').length) {
                document.getElementById('batch_number').value = data[2];
            }

            if ($('#slogan').length) {
                document.getElementById('slogan').value = data[2];
            }

            if ($('#vat').length) {
                data[2] = data[2].replace("%", "");
                document.getElementById('vat').value = data[2];
            }

            if ($('#store').length) {
                document.getElementById('store').value = data[2];
            }

            if ($('#paid').length) {
                document.getElementById('paid').value = data[2];
            }

            if ($('#discount').length) {
                document.getElementById('discount').value = data[2];
            }

            if ($('#support_multi_store').length) {
                document.getElementById('support_multi_store').value = data[2];
            }
            if ($('#saletype').length) {
                document.getElementById('saletype').value = data[2];
            }

            if ($('#product_details_option').length) {
                document.getElementById('product_details_option').value = data[2] || 'Detailed';
            }

        });
    </script>


@endpush