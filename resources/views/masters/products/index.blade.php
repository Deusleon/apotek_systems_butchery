@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Products
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Masters / Products </a></li>
@endsection

@section("content")

    <style>
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

        .select2-container {
            width: 100% !important;
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
                                @if(auth()->user()->checkPermission('Manage Products'))
                                    <button style="float: right;margin-bottom: 7%;" type="button"
                                            class="btn btn-secondary btn-sm"
                                            data-toggle="modal"
                                            data-target="#create">
                                        Add Product
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6" style="margin-left: 2%">

                        </div>
                        <label for="status" style="margin-right: -2.5%" class="col-md-3 col-form-label text-md-right">Status:</label>
                        <div class="col-md-3">
                            <select name="status-filter" class="js-example-basic-single form-control"
                                    id="status-filter" onchange="statusCheck()">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div id="product-table" class="table-responsive">
                        <table id="fixed-header1" class="display table nowrap table-striped table-hover"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Created at</th>
                                @if(auth()->user()->checkPermission('Manage Products'))
                                    <th>Actions</th>
                                @endif
                            </tr>
                            </thead>
                        </table>
                    </div>

                    {{--inactive table--}}
                    <div id="product-table-status-filter" style=" display: none" class="table-responsive">
                        <table id="fixed-header2" class="display table nowrap table-striped table-hover"
                               style="width:100%;">

                            <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Created at</th>
                                @if(auth()->user()->checkPermission('Manage Products'))
                                    <th>Actions</th>
                                @endif
                            </tr>
                            </thead>

                        </table>
                    </div>

                    <!-- ajax loading image -->
                    <div id="loading">
                        <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
                    </div>

                    {{--active table--}}
                    <div id="product-table-status-filter-active" style=" display: none" class="table-responsive">
                        <table id="fixed-header3" class="display table nowrap table-striped table-hover"
                               style="width:100%;">

                            <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Created at</th>
                                @if(auth()->user()->checkPermission('Manage Products'))
                                    <th>Actions</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>


                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>

        @include('masters.products.create')
        @include('masters.products.edit')
        @include('masters.products.delete')
        @include('masters.products.show')
        @endsection

        @push("page_scripts")

            @include('partials.notification')
            <script src="{{asset("assets/apotek/js/notification.js")}}"></script>
            <script src="{{asset("assets/apotek/js/scannerDetection.js")}}"></script>

            <script>

                $(document).ready(function () {
                    $("#fixed-header1").dataTable().fnDestroy();
                    var table_main = $('#fixed-header1').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": "{{ route('all-products') }}",
                            "dataType": "json",
                            "type": "post",
                            "cache": false,
                            "data": {_token: "{{csrf_token()}}"}
                        },
                        "columns": [
                            {"data": "name"},
                            {"data": "category"},
                            {"data": "type", render: function(type){
                                if(type){
                                   return type.charAt(0).toUpperCase() + type.slice(1);
                                }
                            }, defaultContent:'Stockable'},
                            {"data": "date"},
                                @if(auth()->user()->checkPermission('Manage Products'))
                            {
                                "data": "action",
                                defaultContent: "<button type='button' id='shows' class='btn btn-sm btn-rounded btn-success'>Show</button><button type='button' id='edits' class='btn btn-sm btn-rounded btn-primary'>Edit</button><button type='button' id='deletes' class='btn btn-sm btn-rounded btn-danger'>Delete</button>"
                            }
                            @endif
                        ],

                    });

                });

                function loadActive() {
                    $("#fixed-header3").dataTable().fnDestroy();
                    var table_main = $('#fixed-header3').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": "{{ route('all-products') }}",
                            "dataType": "json",
                            "type": "post",
                            "cache": false,
                            "data": {_token: "{{csrf_token()}}"},
                            complete: function () {
                                document.getElementById('product-table-status-filter').style.display = 'none';
                                document.getElementById('product-table-status-filter-active').style.display = 'block';
                                document.getElementById('product-table').style.display = 'none';
                            }
                        },
                        "columns": [
                            {"data": "name"},
                            {"data": "category"},
                            {"data": "type", render: function(type){
                                if(type){
                                   return type.charAt(0).toUpperCase() + type.slice(1);
                                }
                            }, defaultContent:'Stockable'},
                            {"data": "date"},
                                @if(auth()->user()->checkPermission('Manage Products'))
                            {
                                "data": "action",
                                defaultContent: "<button type='button' id='show-active' class='btn btn-sm btn-rounded btn-success'>Show</button><button type='button' id='edit-active' class='btn btn-sm btn-rounded btn-primary'>Edit</button><button type='button' id='delete-active' class='btn btn-sm btn-rounded btn-danger'>Delete</button>"
                            }
                            @endif
                        ],

                    });

                }

                $('#product-table').on('click', '#shows', function () {
                    var row_data = $('#fixed-header1').DataTable().row($(this).parents('tr')).data();
                    $('#show').find('.modal-body #name_edit').val(row_data.name);
                    $('#show').find('.modal-body #barcode_edit').val(row_data.barcode);
                    $('#show').find('.modal-body #generic_edit').val(row_data.generic);
                    $('#show').find('.modal-body #category_edit').val(row_data.category);
                    $('#show').find('.modal-body #sub_category_edit').val(row_data.sub_category);
                    $('#show').find('.modal-body #standard_edit').val(row_data.standard);
                    $('#show').find('.modal-body #sale_edit').val(row_data.sale);
                    $('#show').find('.modal-body #purchase_edit').val(row_data.purchase);
                    if (row_data.min !== null) {
                        $('#show').find('.modal-body #min_stock_edit').val(numberWithCommas(row_data.min));
                    } else {
                        $('#show').find('.modal-body #min_stock_edit').val(row_data.min);
                    }
                    if (row_data.max !== null) {
                        $('#show').find('.modal-body #max_stock_edit').val(numberWithCommas(row_data.max));
                    } else {
                        $('#show').find('.modal-body #max_stock_edit').val(row_data.max);
                    }
                    $('#show').find('.modal-body #dosage_edit').val(row_data.dosage);
                    $('#show').find('.modal-body #indication_edit').val(row_data.indication);
                    $('#show').modal('show');

                });

                $('#product-table').on('click', '#edits', function () {
                    var row_data = $('#fixed-header1').DataTable().row($(this).parents('tr')).data();
                    $('#edit').find('.modal-body #id').val(row_data.id);
                    $('#edit').find('.modal-body #name_edit').val(row_data.name);
                    $('#edit').find('.modal-body #barcode_edits').val(row_data.barcode);
                    $('#edit').find('.modal-body #generic_edit').val(row_data.generic);
                    $('#edit').find('.modal-body #category_options').val(row_data.category_id);
                    $('#edit').find('.modal-body #sub_categories').val(row_data.sub_category_id);
                    $('#edit').find('.modal-body #standard_edit').val(row_data.standard);
                    $('#edit').find('.modal-body #sale_edit').val(row_data.sale);
                    $('#edit').find('.modal-body #product_type').val(row_data.type??'stockable');
                    $('#edit').find('.modal-body #purchase_edit').val(row_data.purchase);
                    if (row_data.min !== null) {
                        $('#edit').find('.modal-body #min_stock_edit').val(numberWithCommas(row_data.min));
                    } else {
                        $('#edit').find('.modal-body #min_stock_edit').val('');
                    }
                    if (row_data.max !== null) {
                        $('#edit').find('.modal-body #max_stock_edit').val(numberWithCommas(row_data.max));
                    } else {
                        $('#edit').find('.modal-body #max_stock_edit').val('');
                    }
                    $('#edit').find('.modal-body #dosage_edit').val(row_data.dosage);
                    $('#edit').find('.modal-body #indication_edit').val(row_data.indication);
                    $('#edit').modal('show');

                });

                $('#product-table').on('click', '#deletes', function () {
                    var row_data = $('#fixed-header1').DataTable().row($(this).parents('tr')).data();
                    var message = "Are you sure you want to delete '".concat(row_data.name, "'?");
                    $('#delete').find('.modal-body #message').text(message);
                    $('#delete').find('.modal-body #product_id').val(row_data.id);
                    $('#delete').modal('show');

                });

                $('#product-table-status-filter-active').on('click', '#show-active', function () {
                    var row_data = $('#fixed-header3').DataTable().row($(this).parents('tr')).data();
                    // console.log(row_data);
                    $('#show').find('.modal-body #name_edit').val(row_data.name);
                    $('#show').find('.modal-body #barcode_edit').val(row_data.barcode);
                    $('#show').find('.modal-body #generic_edit').val(row_data.generic);
                    $('#show').find('.modal-body #category_edit').val(row_data.category);
                    $('#show').find('.modal-body #sub_category_edit').val(row_data.sub_category);
                    $('#show').find('.modal-body #standard_edit').val(row_data.standard);
                    $('#show').find('.modal-body #sale_edit').val(row_data.sale);
                    $('#show').find('.modal-body #purchase_edit').val(row_data.purchase);
                    $('#show').find('.modal-body #min_stock_edit').val(row_data.min);
                    $('#show').find('.modal-body #max_stock_edit').val(row_data.max);
                    $('#show').find('.modal-body #dosage_edit').val(row_data.dosage);
                    $('#show').find('.modal-body #indication_edit').val(row_data.indication);
                    $('#show').modal('show');
                });

                $('#product-table-status-filter-active').on('click', '#edit-active', function () {
                    var row_data = $('#fixed-header3').DataTable().row($(this).parents('tr')).data();

                    $('#edit').find('.modal-body #id').val(row_data.id);
                    $('#edit').find('.modal-body #name_edit').val(row_data.name);
                    $('#edit').find('.modal-body #barcode_edits').val(row_data.barcode);
                    $('#edit').find('.modal-body #generic_edit').val(row_data.generic);
                    $('#edit').find('.modal-body #category_options').val(row_data.category_id);
                    $('#edit').find('.modal-body #sub_categories').val(row_data.sub_category_id);
                    $('#edit').find('.modal-body #standard_edit').val(row_data.standard);
                    $('#edit').find('.modal-body #sale_edit').val(row_data.sale);
                    $('#edit').find('.modal-body #purchase_edit').val(row_data.purchase);
                    $('#edit').find('.modal-body #min_stock_edit').val(row_data.min);
                    $('#edit').find('.modal-body #max_stock_edit').val(row_data.max);
                    $('#edit').find('.modal-body #dosage_edit').val(row_data.dosage);
                    $('#edit').find('.modal-body #product_type').val(row_data.type?? 'stockable');
                    $('#edit').find('.modal-body #indication_edit').val(row_data.indication);
                    $('#edit').modal('show');
                });

                $('#product-table-status-filter-active').on('click', '#delete-active', function () {
                    var row_data = $('#fixed-header3').DataTable().row($(this).parents('tr')).data();
                    var message = "Are you sure you want to delete '".concat(row_data.name, "'?");
                    $('#delete').find('.modal-body #message').text(message);
                    $('#delete').find('.modal-body #product_id').val(row_data.id);
                    $('#delete').modal('show');
                });

                /*barcode*/
                $(window).ready(function () {
                    // console.log('all is well');
                    $(window).scannerDetection();
                    $(window).bind('scannerDetectionComplete', function (e, data) {
                        // console.log('complete ' + data.string);

                        var hasFocus = $('#barcode_edit').is(':focus');
                        if (hasFocus) {
                            $("#barcode_edit").val(data.string);
                        }

                        if ($('#barcode_edits').is(':focus')) {
                            $("#barcode_edits").val(data.string);
                        }

                    })
                        .bind('scannerDetectionError', function (e, data) {
                            // console.log('detection error ' + data.string);
                        })
                        .bind('scannerDetectionReceive', function (e, data) {
                            // console.log('Recieve');
                            // console.log(data.evt.which);
                        });
                });
                /*end barcode*/


                $('#product-table-status-filter').on('click', '#activate', function () {
                    var row_data = $('#fixed-header2').DataTable().row($(this).parents('tr')).data();
                    var index = $('#fixed-header2').DataTable().row($(this).parents('tr')).index();

                    $('#loading').show();
                    $.ajax({
                        url: '{{ route('status-activate') }}',
                        type: "get",
                        dataType: "json",
                        data: {
                            product_id: row_data.id
                        },
                        success: function (data) {
                            $('#fixed-header2').DataTable().row(index).remove().draw();
                        },
                        complete: function () {
                            $('#loading').hide();
                        }
                    });


                });

                function createOption() {
                    var category = document.getElementById('category_option');
                    var category_id = category.options[category.selectedIndex].value;
                    filterCategory(category_id);
                }

                function editOption() {
                    var category = document.getElementById('category_options');
                    var category_id = category.options[category.selectedIndex].value;
                    filterCategoryEdit(category_id);
                }

                function filterCategory(data) {

                    /*make ajax call*/
                    if (Number(data) !== 0) {
                        $("#sub_category option").remove();
                        $.ajax({
                            url: '{{ route('product-category-filter') }}',
                            type: "get",
                            dataType: "json",
                            data: {
                                category_id: data
                            },
                            success: function (data) {
                                $('#sub_category').append($('<option>', {
                                    value: '',
                                    text: 'Select category'
                                }));
                                $.each(data, function (id, detail) {
                                    $('#sub_category').append($('<option>', {value: detail.id, text: detail.name}));
                                });
                            }
                        });
                    }

                }

                function filterCategoryEdit(data) {

                    /*make ajax call*/
                    if (Number(data) !== 0) {
                        $("#sub_categories option").remove();
                        $.ajax({
                            url: '{{ route('product-category-filter') }}',
                            type: "get",
                            dataType: "json",
                            data: {
                                category_id: data
                            },
                            success: function (data) {
                                $('#sub_categories').append($('<option>', {
                                    value: '',
                                    text: 'Select category'
                                }));
                                $.each(data, function (id, detail) {
                                    $('#sub_categories').append($('<option>', {value: detail.id, text: detail.name}));
                                });
                            }
                        });
                    }

                }

                function saveFormData() {

                    var form = $('#form_product').serialize();

                    $.ajax({
                        url: '{{ route('store-products') }}',
                        type: "post",
                        dataType: "json",
                        data: form,
                        success: function (data) {
                            if (data[0].message === 'success') {
                                notify('Product added successfully', 'top', 'right', 'success');
                                $('#category_option').val('0').change();
                                document.getElementById('form_product').reset();
                            } else {
                                notify('Product name exists', 'top', 'right', 'danger');
                                $('#category_option').val('0').change();
                                document.getElementById('form_product').reset();
                            }
                        }
                    });
                }

                function statusCheck() {
                    var status = document.getElementById('status-filter');
                    var status_value = status.options[status.selectedIndex].value;

                    if (Number(status_value) === 0) {
                        statusFilter(status_value);
                    } else {
                        loadActive();
                        // statusFilterActive(status_value);
                    }

                }

                function statusFilter(data) {
                    /*make ajax call*/

                    $.ajax({
                        url: '{{ route('status-filter') }}',
                        type: "get",
                        cache: true,
                        dataType: "json",
                        data: {
                            status: data
                        },
                        success: function (data) {
                            document.getElementById('product-table-status-filter').style.display = 'block';
                            document.getElementById('product-table-status-filter-active').style.display = 'none';
                            document.getElementById('product-table').style.display = 'none';
                            bindStatusFilterData(data);
                        }
                    });

                }

                function statusFilterActive(data) {
                    /*make ajax call*/
                    $('#loading').show();
                    $.ajax({
                        url: '{{ route('status-filter') }}',
                        type: "get",
                        dataType: "json",
                        cache: true,
                        headers: {
                            'Cache-Control': 'max-age=604800, public'
                        },
                        data: {
                            status: data
                        },
                        success: function (data) {
                            // console.log(data);
                            document.getElementById('product-table-status-filter').style.display = 'none';
                            document.getElementById('product-table-status-filter-active').style.display = 'block';
                            document.getElementById('product-table').style.display = 'none';
                            bindStatusFilterActiveData(data);
                        },
                        complete: function () {
                            $('#loading').hide();
                        }
                    });

                }


                function bindStatusFilterData(data) {

                    $("#fixed-header2").dataTable().fnDestroy();
                    var table = $('#fixed-header2').DataTable({
                        'columns': [
                            {'data': 'name'},
                            {'data': 'category'},
                            {"data": "type", render: function(type){
                                if(type){
                                   return type.charAt(0).toUpperCase() + type.slice(1);
                                }
                            }, defaultContent:'Stockable'},
                            {'data': 'date'},
                                @if(auth()->user()->checkPermission('Manage Products'))
                            {
                                'data': "action",
                                defaultContent: "<button type='button' id='activate' class='btn btn-sm btn-rounded btn-success'>Activate</button>"
                            }
                            @endif

                        ]
                    });

                    table.clear();
                    table.rows.add(data);
                    table.draw();
                }

                function bindStatusFilterActiveData(data) {
                    table3.clear();
                    table3.rows.add(data);
                    table3.draw();
                }

                $('#form_product').on('submit', function (e) {
                    e.preventDefault();
                    var category = document.getElementById('category_option');
                    var category_id = category.options[category.selectedIndex].value;

                    if (Number(category_id) === 0) {
                        document.getElementById('category_border').style.display = 'block';
                        return false;
                    }

                    document.getElementById('category_border').style.display = 'none';

                    saveFormData();

                });

                $('#form_product_edit').on('submit', function (e) {
                    var category = document.getElementById('category_options');
                    var category_id = category.options[category.selectedIndex].value;

                    if (Number(category_id) === 0) {
                        document.getElementById('category_borders').style.display = 'block';
                        return false;
                    }

                    document.getElementById('category_borders').style.display = 'none';

                });

                $('#min_stock_edit').on('change', function () {
                    var min = document.getElementById('min_stock_edit').value;

                    if (min !== '') {
                        document.getElementById('min_stock_edit').value =
                            numberWithCommas(parseFloat(min.replace(/\,/g, ''), 10));
                    } else {
                        document.getElementById('min_stock_edit').value = '';
                    }

                });

                $('#max_stock_edit').on('change', function () {
                    var max = document.getElementById('max_stock_edit').value;
                    if (max !== '') {
                        document.getElementById('max_stock_edit').value =
                            numberWithCommas(parseFloat(max.replace(/\,/g, ''), 10));
                    } else {
                        document.getElementById('max_stock_edit').value = '';
                    }

                });

                $('#min_stock_edits').on('change', function () {
                    var min = document.getElementById('min_stock_edits').value;

                    if (min !== '') {
                        document.getElementById('min_stock_edits').value =
                            numberWithCommas(parseFloat(min.replace(/\,/g, ''), 10));
                    } else {
                        document.getElementById('min_stock_edits').value = '';
                    }

                });

                $('#max_stock_edits').on('change', function () {
                    var max = document.getElementById('max_stock_edits').value;
                    if (max !== '') {
                        document.getElementById('max_stock_edits').value =
                            numberWithCommas(parseFloat(max.replace(/\,/g, ''), 10));
                    } else {
                        document.getElementById('max_stock_edits').value = '';
                    }

                });

                $('#category_option').select2({
                    dropdownParent: $('#create')
                });

                $('#sub_category').select2({
                    dropdownParent: $('#create')
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

                function numberWithCommas(digit) {
                    return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }

            </script>

    @endpush
