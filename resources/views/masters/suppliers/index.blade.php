@extends("layouts.master")

@section('page_css')
@endsection

@section('content-title')
    Suppliers
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Suppliers</a></li>
@endsection

@section("content")

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        @if(auth()->user()->checkPermission('Add Suppliers'))
                            <button style="float: right;margin-bottom: 2%;" type="button"
                                    class="btn btn-secondary btn-sm mr-0"
                                    data-toggle="modal"
                                    data-target="#create">
                                Add Supplier
                            </button>
                        @endif
                        <div class="table-responsive">
                            <table id="fixed-header" class="display table nowrap table-striped table-hover"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact Person</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>     
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($suppliers as $supplier)
                                    <tr>
                                        <td>{{$supplier->name}}</td>
                                        <td>{{$supplier->contact_person}}</td>
                                        <td>{{$supplier->mobile}}</td>
                                        <td>{{$supplier->email}}</td>
                                        <td>{{$supplier->address}}</td>
                                        <td>
                                            {{-- Edit button --}}
                                            @if(auth()->user()->checkPermission('Edit Suppliers'))
                                                <button class="btn btn-sm btn-rounded btn-primary"
                                                        data-id="{{$supplier->id}}"
                                                        data-name="{{$supplier->name}}"
                                                        data-contact_person="{{$supplier->contact_person}}"
                                                        data-address="{{$supplier->address}}"
                                                        data-phone="{{$supplier->mobile}}"
                                                        data-email="{{$supplier->email}}"
                                                        type="button"
                                                        data-toggle="modal" data-target="#edit">
                                                    Edit
                                                </button>
                                            @endif

                                            {{-- Delete button (requires permission + no transactions) --}}
                                            @if(auth()->user()->checkPermission('Delete Suppliers') && $supplier->active_user != "has transactions")
                                                <button class="btn btn-sm btn-rounded btn-danger"
                                                        data-id="{{$supplier->id}}"
                                                        data-name="{{$supplier->name}}"
                                                        type="button"
                                                        data-toggle="modal"
                                                        data-target="#delete">
                                                    Delete
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('masters.suppliers.create')
    @include('masters.suppliers.delete')
    @include('masters.suppliers.edit')

@endsection


@push("page_scripts")
    @include('partials.notification')

    <script>
        $(document).ready(function(){
            $('#save_btn, #save_btns').prop('disabled', true);

            // EDIT modal
            $('#edit').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var modal = $(this);

                modal.find('.modal-body #id').val(button.data('id'));
                modal.find('.modal-body #name_edit').val(button.data('name'));
                modal.find('.modal-body #address_edit').val(button.data('address'));
                modal.find('.modal-body #contact_edit').val(button.data('contact_person'));
                modal.find('.modal-body #phone_edits').val(button.data('phone'));
                modal.find('.modal-body #email_edit').val(button.data('email'));

                initPhoneValidation("#phone_edits", "#save_btns", modal);

                // For edit mode, validate the pre-filled phone number
                setTimeout(function() {
                    var phoneInput = document.querySelector("#phone_edits");
                    if (phoneInput && phoneInput._itiInstance && phoneInput.value.trim()) {
                        if (phoneInput._itiInstance.isValidNumber()) {
                            $('#save_btns').prop('disabled', false);
                        }
                    } else {
                        // If no phone validation or empty, enable button
                        $('#save_btns').prop('disabled', false);
                    }
                }, 200);
            });

            // DELETE modal
            $('#delete').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var message = "Are you sure you want to delete '".concat(button.data('name'), "'?");
                var modal = $(this);
                modal.find('.modal-body #message').text(message);
                modal.find('.modal-body #id').val(button.data('id'));
            });

            // CREATE modal
            $('#create').on('show.bs.modal', function () {
                var modal = $(this);
                initPhoneValidation("#phone_edit", "#save_btn", modal);
            });
        });

        // Safe Phone Validation Init
        function initPhoneValidation(inputSelector, saveBtnSelector, modal) {
            const input = document.querySelector(inputSelector);
            if (!input) return;

            // Avoid multiple inits
            if (input._itiInstance) {
                return;
            }

            const validMsg = input.parentElement.querySelector('.valid-msg') || input.parentElement.querySelector('.hide');
            const errorMsg = input.parentElement.querySelector('.error-msg') || (validMsg ? validMsg.nextElementSibling : null);
            const errorMap = ["Invalid Phone Number", "Invalid Country Code", "Too Short", "Too Long", "Invalid Phone Number"];

            try {
                const iti = window.intlTelInput(input, {
                    initialCountry: "tz",
                    onlyCountries: ["tz", "ug", "ke", "rw", "bi", "sd", "zm", "zw", "mw", "mz", "bw", "za", "na", "ao", "cd", "cg", "ga", "gq", "cm", "td", "cf", "ss", "et", "dj", "er", "so", "mg", "sc", "mu", "km", "yt", "re", "bf", "bj", "ci", "cv", "gh", "gm", "gn", "gw", "lr", "ml", "mr", "ne", "ng", "sh", "sl", "sn", "st", "tg", "eh", "eg", "ly", "ma", "tn", "dz", "jo", "lb", "sy", "iq", "kw", "sa", "ye", "om", "ae", "qa", "bh", "cy", "il", "ps", "tr", "az", "am", "ge", "ru", "by", "ua", "md", "ro", "bg", "mk", "al", "me", "rs", "ba", "hr", "si", "sk", "cz", "pl", "hu", "at", "ch", "li", "de", "be", "nl", "lu", "fr", "mc", "ad", "es", "pt", "gi", "it", "sm", "va", "mt", "gr", "cy", "gb", "ie", "dk", "no", "se", "fi", "is", "sj", "fo", "ax", "ee", "lv", "lt"],
                    nationalMode: false,
                    utilsScript: "{{asset('assets/plugins/intl-tel-input/js/utils.js?1562189064761')}}"
                });
                input._itiInstance = iti;

                // Auto-fill initial country code if empty
                if (!input.value.trim()) {
                    input.value = iti.getNumber().replace(/\D/g,'').length === 0 ? iti.getSelectedCountryData().dialCode : input.value;
                    input.value = "+" + input.value;
                }

                // Ensure when user types starting with 0, it converts to full country code
                input.addEventListener('input', function(e) {
                // Remove any non-digit characters except the leading '+'
                let val = input.value;
                if (val.startsWith("+")) {
                    val = "+" + val.slice(1).replace(/\D/g, '');
                } else {
                    val = val.replace(/\D/g, '');
                }

                // Handle numbers starting with 0 -> replace with country code
                if (val.startsWith("0")) {
                    val = "+" + iti.getSelectedCountryData().dialCode + val.substring(1);
                }

                // Limit max length based on intlTelInput's example length
                const maxLength = iti.getNumber().replace(/\D/g,'').length + 12; 
                if (val.length > maxLength) {
                    val = val.slice(0, maxLength);
                }

                input.value = val;
            });


            } catch (e) {
                console.error("intlTelInput init failed", e);
                return;
            }

            function reset() {
                input.classList.remove("error");
                if (validMsg) validMsg.classList.add("hide");
                if (errorMsg) { errorMsg.classList.add("hide"); errorMsg.innerHTML = ""; }
                $(saveBtnSelector).prop('disabled', true);
            }

            function validateNumber() {
                reset();
                if (input.value.trim()) {
                    if (input._itiInstance.isValidNumber()) {
                        if (validMsg) validMsg.classList.remove("hide");
                        $(saveBtnSelector).prop('disabled', false);
                        input.value = input._itiInstance.getNumber();
                    } else {
                        input.classList.add("error");
                        $(saveBtnSelector).prop('disabled', true);
                        const errorCode = input._itiInstance.getValidationError();
                        if (errorMsg) errorMsg.innerHTML = errorMap[errorCode] || "Invalid Number";
                        if (errorMsg) errorMsg.classList.remove("hide");
                    }
                }
            }

            input.addEventListener('blur', validateNumber);
            input.addEventListener('change', reset);

            // Cleanup when modal closes
            modal.one('hidden.bs.modal', function () {
                input.removeEventListener('blur', validateNumber);
                input.removeEventListener('change', reset);
                if (input._itiInstance && typeof input._itiInstance.destroy === 'function') {
                    input._itiInstance.destroy();
                }
                delete input._itiInstance;
                $(saveBtnSelector).prop('disabled', true);
            });
        }
    </script>

    <!-- Input mask Js -->
    <script src="{{asset("assets/plugins/inputmask/js/inputmask.min.js")}}"></script>
    <script src="{{asset("assets/plugins/inputmask/js/jquery.inputmask.min.js")}}"></script>
    <script src="{{asset("assets/plugins/inputmask/js/autoNumeric.js")}}"></script>

    <!-- select2 Js -->
    <script src="{{asset("assets/plugins/select2/js/select2.full.min.js")}}"></script>
    <!-- form-select-custom Js -->
    <script src="{{asset("assets/js/pages/form-select-custom.js")}}"></script>

    <!-- form-picker-custom Js -->
    <script src="{{asset("assets/js/pages/form-masking-custom.js")}}"></script>

@endpush
