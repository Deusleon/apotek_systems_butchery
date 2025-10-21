@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Expenses
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Accounting / Expenses </a></li>
@endsection

@section("content")

    <style>
        .datepicker>.datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        .ms-container {
            background: transparent url('../assets/plugins/multi-select/img/switch.png') no-repeat 50% 50%;
            width: 100%;
        }

        .ms-selectable,
        .ms-selection {
            background: #fff;
            color: #555555;
            float: left;
            width: 45%;
        }

        #loading {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            display: none;
            opacity: 0.7;
            background-color: #fff;
            z-index: 99;
            text-align: center;
        }

        #loading-image {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 100;
        }

        input[type=button]:focus {
            background-color: #748892;
            border-color: #748892;
            color: white;
        }
    </style>

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">


                    <div class="row">
                        <div class="col-md-9">
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                @if(auth()->user()->checkPermission('Add Expenses'))
                                    <button style="float: right;margin-bottom: 7%;" type="button"
                                        class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#create">
                                        Add Expense
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-3 align-items-center">
                        <label class="mr-2" for="">Date:</label>
                        <input type="text" name="date_of_expense" id="expense_date" class="form-control w-auto"
                            onchange="getExpenseDate()">
                    </div>

                    {{--ajax loading gif--}}
                    <div id="loading">
                        <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                    </div>

                    <div id="tbody-header-expense" class="table-responsive" style="display: none;">
                        <table id="fixed-header-expense" class="display table nowrap table-striped table-hover"
                            style="width:100%;">

                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Pay Method</th>
                                    <th>Amount</th>
                                    <th>Category</th>
                                    <th>Updated by</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @include("expense.create")
    @include("expense.edit")
    @include("expense.delete")
    @include("expense.show")

@endsection

@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    @include('partials.notification')

    <script>
        /*expense filter table results*/
        var table_expense_filter = $('#fixed-header-expense').DataTable({
            searching: true,
            bPaginate: true,
            bInfo: true,
            'columns': [
                {
                    'data': 'created_at',
                    render: function (data, type, row) {
                        if (type === 'sort') {
                            return data; // Use full datetime for sorting
                        }
                        return data.split(' ')[0]; // Display only date part
                    }
                },
                { 'data': 'payment_method' },
                {
                    'data': 'amount', render: function (amount) {
                        return formatMoney(amount);
                    }
                },
                { 'data': 'expense_Category' },
                { 'data': 'user' },
                {
                    data: 'action',
                    defaultContent: `
                                    <button class="btn btn-info btn-rounded btn-sm" type="button" id="show_btn">Show</button>
                                @if(auth()->user()->checkPermission('Edit Expenses'))
                                    <button class="btn btn-primary btn-rounded btn-sm" type="button" id="edit_btn">Edit</button>
                                @endif
                                @if(auth()->user()->checkPermission('Delete Expenses'))
                                    <button class="btn btn-danger btn-rounded btn-sm" type="button" id="delete_btn">Delete</button>
                                @endif
                            `
                }


            ], aaSorting: [[0, "desc"]]

        });

        $('#fixed-header-expense tbody').on('click', '#edit_btn', function () {
            var row_data = table_expense_filter.row($(this).parents('tr')).data();
            var index = table_expense_filter.row($(this).parents('tr')).index();

            $('#edit').find('.modal-body #d_auto_91_edit').val(row_data.created_at.split(' ')[0]); // Display date only
            $('#payment_method_edit').val(row_data.payment_method_id).change();
            $('#edit').find('.modal-body #expense_amount_edit').val(formatMoney(row_data.amount));
            $('#expense_category_edit').val(row_data.expense_category_id).change();
            $('#edit').find('.modal-body #expense_description_edit').val(row_data.description);
            $('#edit').find('.modal-body #expense_id').val(row_data.id);

            $('#edit').modal('show');

        });

        $('#fixed-header-expense tbody').on('click', '#delete_btn', function () {
            var row_data = table_expense_filter.row($(this).parents('tr')).data();
            var index = table_expense_filter.row($(this).parents('tr')).index();

            var message = "Are you sure you want to delete this expense?";
            $('#delete').find('.modal-body #message').text(message);

            $('#delete').find('.modal-body #expense_id').val(row_data.id);

            $('#delete').modal('show');

        });

        $('#fixed-header-expense tbody').on('click', '#show_btn', function () {
            var row_data = table_expense_filter.row($(this).parents('tr')).data();

            $('#show').find('#show_date').val(row_data.created_at.split(' ')[0]); // Display date only
            $('#show').find('#show_payment_method').val(row_data.payment_method);
            $('#show').find('#show_amount').val(formatMoney(row_data.amount));
            $('#show').find('#show_category').val(row_data.expense_Category);
            $('#show').find('#show_updated_by').val(row_data.user);
            $('#show').find('#show_description').val(row_data.description);

            $('#show').modal('show');

        });

        $(function () {

            var start = moment().startOf('month');
            var end = moment().endOf('month');

            function cb(start, end) {
                $('#expense_date').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
                getExpenseDate();
            }

            $('#expense_date').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                maxDate: end,
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY/MM/DD'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment()]
                }
            }, cb);

            cb(start, end);

            $('#expense_date').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
                getExpenseDate(); // update table on selection
            });

        });

        /*category select2 dropdown*/
        $('#expense_category').select2({
            dropdownParent: $('#create')
        });

        /*category select2 dropdown*/
        $('#expense_category_edit').select2({
            dropdownParent: $('#edit')
        });

        /*to date*/
        $('#d_auto_7').datepicker({
            todayHighlight: true,
            format: 'YYYY-MM-DD',
            changeYear: true
        }).on('change', function () {
            //filterExpenseDate();
            $('.datepicker').hide();
        }).attr('readonly', 'readonly');

        /*from date*/
        $('#d_auto_8').datepicker({
            todayHighlight: true,
            format: 'YYYY-MM-DD',
            changeYear: true
        }).on('change', function () {
            //filterExpenseDate();
            $('.datepicker').hide();
        }).attr('readonly', 'readonly');

        $('#d_auto_9').datepicker({
            todayHighlight: true,
            format: 'YYYY-MM-DD',
            changeYear: true,
            maxDate: '+0m +0w'
        }).on('change', function () {
            $('.datepicker').hide();
        }).attr('readonly', 'readonly');

        $(function () {
            var start = moment();
            var end = moment();

            $('#d_auto_91').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                maxDate: end,
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });

        $(function () {
            var start = moment();
            var end = moment();

            $('#d_auto_91_edit').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                maxDate: end,
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });

        /* Get expense date range and call AJAX */
        function getExpenseDate() {
            var value = $('#expense_date').val();
            if (!value) return;

            var dates = value.split('-').map(d => d.trim()); // trim spaces
            filterExpenseDate(dates);
        }

        /* AJAX request to backend */
        function filterExpenseDate(dates) {
            var from_date = moment(dates[0], 'YYYY/MM/DD').format('YYYY-MM-DD');
            var to_date = moment(dates[1], 'YYYY/MM/DD').format('YYYY-MM-DD');

            $('#loading').show();

            $.ajax({
                url: '{{ route("expense-date-filter") }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    from_date: from_date,
                    to_date: to_date
                },
                success: function (data) {
                    $('#tbody-header-expense').show();
                    bindDataFilter(data[0][0]);
                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        }

        /* Bind filtered data to DataTable */
        function bindDataFilter(data) {
            table_expense_filter.clear();
            table_expense_filter.rows.add(data);
            table_expense_filter.draw();
        }

        /*validate form*/
        $('#expense_form').on('submit', function () {

            var date = document.getElementById('d_auto_91').value;
            var payment_method = document.getElementById('payment_method');
            var method_id = payment_method.options[payment_method.selectedIndex].value;
            var expense_category = document.getElementById('expense_category');
            var category_id = expense_category.options[expense_category.selectedIndex].value;

            if (date === '') {
                document.getElementById('date').style.borderColor = 'red';
                return false;
            } else if (Number(method_id) === Number(0)) {
                document.getElementById('method').style.borderColor = 'red';
                return false;
            } else if (Number(category_id) === Number(0)) {
                document.getElementById('category').style.borderColor = 'red';
                return false;
            }

            document.getElementById('method').style.borderColor = 'white';
            document.getElementById('category').style.borderColor = 'white';
            document.getElementById('date').style.borderColor = 'white';
        });

        $('#expense').on('change', function () {
            var amount = document.getElementById('expense').value.replace(/,/g, '');
            document.getElementById('expense').value = formatMoney(amount);
        });

        $('#expense_amount_edit').on('change', function () {
            var amount = document.getElementById('expense_amount_edit').value.replace(/,/g, '');
            document.getElementById('expense_amount_edit').value = formatMoney(amount);
        });

        /* AJAX submit for edit form */
        $('#expense_form_edit').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            formData.append('_method', 'PUT');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#edit').modal('hide');
                    getExpenseDate(); // Refresh table
                    // Show success message - assuming toastr or similar is available
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Expense updated successfully!');
                    }
                },
                error: function (xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Error updating expense');
                    }
                }
            });
        });

        function isNumberKey(evt, obj) {

            var charCode = (evt.which) ? evt.which : event.keyCode;
            var value = obj.value;
            var dotcontains = value.indexOf(".") !== -1;
            if (dotcontains)
                if (charCode === 46) return false;
            if (charCode === 46) return true;
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

        /*format money*/
        function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
                const negativeSign = amount < 0 ? "-" : "";
                let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
                let j = (i.length > 3) ? i.length % 3 : 0;
                return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
            } catch (e) {
            }
        }

    </script>


@endpush