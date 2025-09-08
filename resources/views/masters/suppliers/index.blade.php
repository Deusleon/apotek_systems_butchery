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
                        @if(auth()->user()->checkPermission('Manage Suppliers'))
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
                                    @if(auth()->user()->checkPermission('Manage Suppliers'))
                                        <th>Action</th>
                                    @endif
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
                                        @if(auth()->user()->checkPermission('Manage Suppliers'))
                                            <td>
                                                <a href="#">
                                                    <button class="btn btn-sm btn-rounded btn-primary"
                                                            data-id="{{$supplier->id}}"
                                                            data-name="{{$supplier->name}}"
                                                            data-contact_person="{{$supplier->contact_person}}"
                                                            data-address="{{$supplier->address}}"
                                                            data-phone="{{$supplier->mobile}}"
                                                            data-email="{{$supplier->email}}"
                                                            type="button"
                                                            data-toggle="modal" data-target="#edit">Edit
                                                    </button>
                                                </a>
                                                <a href="#">
                                                    <button class="btn btn-sm btn-rounded btn-danger"
                                                            data-id="{{$supplier->id}}"
                                                            data-name="{{$supplier->name}}"
                                                            type="button"
                                                            data-toggle="modal"
                                                            data-target="#delete">
                                                        Delete
                                                    </button>
                                                </a>
                                            </td>
                                        @endif
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
        });


        $('#edit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body #id').val(button.data('id'));
            modal.find('.modal-body #name_edit').val(button.data('name'));
            modal.find('.modal-body #address_edit').val(button.data('address'));
            modal.find('.modal-body #contact_edit').val(button.data('contact_person'));
            modal.find('.modal-body #phone_edits').val(button.data('phone'));
            modal.find('.modal-body #email_edit').val(button.data('email'))

        });

        $('#delete').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var message = "Are you sure you want to delete '".concat(button.data('name'), "'?");
            var modal = $(this);
            modal.find('.modal-body #message').text(message);
            modal.find('.modal-body #id').val(button.data('id'));

        });

        //Create Modal
        $('#edit').on('show.bs.modal', function () {
            initPhoneValidation("#phone_edits", "#save_btns");
        });

        //Create Modal
        $('#create').on('show.bs.modal', function () {
            initPhoneValidation("#phone_edit", "#save_btn");
        });


        function initPhoneValidation(inputSelector, saveBtnSelector) {
            const input = document.querySelector(inputSelector);
            const validMsg = input.parentElement.querySelector('.hide'); // assuming first span is valid-msg
            const errorMsg = validMsg.nextElementSibling; // next span is error-msg
            const errorMap = ["Invalid Phone Number", "Invalid Country Code", "Too Short", "Too Long", "Invalid Phone Number"];

            const iti = window.intlTelInput(input, {
                initialCountry: "tz",
                onlyCountries: ["tz", "ug", "ke", "rw", "bi", "sd"],
                nationalMode: false,
                utilsScript: "{{asset('assets/plugins/intl-tel-input/js/utils.js?1562189064761')}}"
            });

            function reset() {
                input.classList.remove("error");
                validMsg.classList.add("hide");
                errorMsg.classList.add("hide");
                errorMsg.innerHTML = "";
                $(saveBtnSelector).prop('disabled', true);
            }

            function validateNumber() {
                reset();
                if (input.value.trim()) {
                    if (iti.isValidNumber()) {
                        validMsg.classList.remove("hide");
                        $(saveBtnSelector).prop('disabled', false);
                        input.value = iti.getNumber();
                    } else {
                        input.classList.add("error");
                        $(saveBtnSelector).prop('disabled', true);
                        const errorCode = iti.getValidationError();
                        errorMsg.innerHTML = errorMap[errorCode] || "Invalid Number";
                        errorMsg.classList.remove("hide");
                    }
                }
            }

            input.addEventListener('blur', validateNumber);
            input.addEventListener('change', reset);
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
