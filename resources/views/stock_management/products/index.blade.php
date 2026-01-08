@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Product List
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Inventory / Product List </a></li>
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
                    <div class="row mb-3">
                        <!-- <div class="col-md-6">
                                                                                                                    <div class="btn-group">
                                                                                                                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                                                            <i class="fas fa-download mr-1"></i> Export
                                                                                                                        </button>
                                                                                                                        <div class="dropdown-menu">
                                                                                                                            <a class="dropdown-item" href="{{ route('products.export', ['format' => 'pdf']) }}" target="_blank">
                                                                                                                                <i class="far fa-file-pdf text-danger mr-2"></i>PDF
                                                                                                                            </a>
                                                                                                                            <a class="dropdown-item" href="{{ route('products.export', ['format' => 'excel']) }}">
                                                                                                                                <i class="far fa-file-excel text-success mr-2"></i>Excel
                                                                                                                            </a>
                                                                                                                            <a class="dropdown-item" href="{{ route('products.export', ['format' => 'csv']) }}">
                                                                                                                                <i class="fas fa-file-csv text-info mr-2"></i>CSV
                                                                                                                            </a>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div> -->
                    </div>
                    <div class="row justify-content-end align-items-end mb-3">
                        <!-- Status Filter -->
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="status-filter">Status:</label>
                                <select name="status-filter" class="js-example-basic-single form-control"
                                    id="status-filter">
                                    <option value="">All</option>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                    <option value="unused">Unused</option>
                                </select>
                            </div>
                        </div>
                        <!-- Category Filter -->
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="category-filter">Category:</label>
                                <select name="category-filter" class="js-example-basic-single form-control"
                                    id="category-filter">
                                    <option value="">All</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="" id="is_detailed" value="{{ $is_detailed }}">
                        <div class="col-md-3 text-right">
                            @if(auth()->user()->checkPermission('Add Products'))
                                @if($is_detailed === 'Normal')
                                    <button type="button" class="btn btn-secondary" data-toggle="modal"
                                        data-target="#create_normal">
                                        <i class="fas fa-plus mr-1"></i>Add Product
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#create">
                                        <i class="fas fa-plus mr-1"></i>Add Product
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div id="product-table" class="table-responsive">
                        <table id="fixed-header1" class="display table nowrap table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th hidden>Brand</th>
                                    <th hidden>Pack Size</th>
                                    <th>Category</th>
                                    @if(auth()->user()->checkPermission('View Product List'))
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



                </div>
            </div>
        </div>

        @include('masters.products.create_normal')
        @include('masters.products.create')
        @include('masters.products.edit')
        @include('masters.products.edit_normal')
        @include('masters.products.delete')
        @if ($is_detailed === 'Detailed')
            @include('masters.products.show')
        @else
            @include('masters.products.show_normal')
        @endif

@endsection

    @push("page_scripts")

        @include('partials.notification')
        <script src="{{asset('assets/apotek/js/notification.js')}}"></script>
        <script src="{{asset('assets/apotek/js/scannerDetection.js')}}"></script>

        <script>

            $(document).ready(function () {
                // Initialize Select2
                $('.js-example-basic-single').select2();

                // Initialize DataTable
                var table = $('#fixed-header1').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('all-products') }}",
                        "type": "POST",
                        "data": function (d) {
                            d._token = "{{ csrf_token() }}";
                            d.category = $('#category-filter').val();
                            d.status = $('#status-filter').val();
                        },
                        "error": function (xhr, error, thrown) {
                            console.error('DataTables error:', error);
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                console.error('Server error:', xhr.responseJSON.message);
                            }
                        }
                    },
                    "columns": [
                        // {"data": "name"},
                        {
                            "data": null,
                            "render": function (data, type, row) {
                                let displayName = row.name;
                                let packSizeLabel = "";

                                if (row.brand) {
                                    displayName += " " + row.brand;
                                }
                                if (row.pack_size) {
                                    packSizeLabel += " " + row.pack_size;
                                }
                                if (row.sales_uom) {
                                    packSizeLabel += row.sales_uom;
                                }

                                return displayName + packSizeLabel;
                            }
                        },

                        // {"data": "brand"},
                        // {"data": "pack_size"},
                        { "data": "category.name", "defaultContent": "" },
                        {
                            "data": "id",
                            "orderable": false,
                            "searchable": false,
                            "render": function (data, type, row) {
                                let buttons = `
                                                                                                        <button type="button" class="btn btn-success btn-sm btn-rounded show-modal">
                                                                                                            Show
                                                                                                        </button>
                                                                                                    `;

                                @if(auth()->user()->checkPermission('Edit Products'))
                                    buttons += `
                                                                                                                                                                                <button type="button" class="btn btn-primary btn-sm btn-rounded" id="edits">
                                                                                                                                                                                    Edit
                                                                                                                                                                                </button>
                                                                                                                                                                            `;
                                @endif

                                @if(auth()->user()->checkPermission('Delete Products'))
                                    buttons += `
                                                                                                                                                                                <button type="button" class="btn btn-danger btn-sm btn-rounded" id="deletes">
                                                                                                                                                                                    Delete
                                                                                                                                                                                </button>
                                                                                                                                                                            `;
                                @endif

                                                                                                    return buttons;
                            }

                        }
                    ],
                    "order": [[0, 'asc']],
                    "pageLength": 10,
                    "drawCallback": function (settings) {
                        // Re-initialize tooltips after table redraw
                        $('[data-toggle="tooltip"]').tooltip();
                        // Fix colspan for empty table message
                        var api = this.api();
                        var columnsCount = api.columns().header().length;

                        // Update empty table message colspan
                        $('#fixed-header1 tbody tr td.dataTables_empty').attr('colspan', columnsCount);
                    },
                });

                // Handle filter changes.
                $('#category-filter, #status-filter').change(function () {
                    table.draw();
                });

                $('#create').on('hide.bs.modal', function () {
                    table.draw();
                });
                $('#create_normal').on('hide.bs.modal', function () {
                    table.draw();
                });

            });

            $(document).on('click', '.show-modal', function (e) {
                var product_option = document.getElementById('is_detailed').value;
                var is_detailed = product_option === 'Detailed' ? true : false;

                e.preventDefault();
                var table = $('#fixed-header1').DataTable();
                var row = $(this).closest('tr');
                var row_data = table.row(row).data();
                $('#show').modal('show');

                // Populate modal with data
                $('#show #name_edit').html(row_data.name || 'N/A');
                $('#show #brand_show').html(row_data.brand || 'N/A');
                $('#show #barcode_edit').html(row_data.barcode || 'N/A');
                $('#show #category_edit').html(row_data.category ? row_data.category.name : 'N/A');
                $('#show #sale_edit').html(row_data.sales_uom || 'N/A');
                $('#show #pack_size_show').html(row_data.pack_size || 'N/A');
                $('#show #min_quantinty_show').html(row_data.min_quantinty > 0 ? numberWithCommas(row_data.min_quantinty) : 'N/A');
                $('#show #max_quantinty_show').html(row_data.max_quantinty > 0 ? numberWithCommas(row_data.max_quantinty) : 'N/A');
                $('#show #status').html(row_data.status == 1 ? 'Active' : 'Inactive');
            });

            $('#product-table').on('click', '#edits', function () {
                var product_option = document.getElementById('is_detailed').value;
                var is_detailed = product_option === 'Detailed' ? true : false;
                var table = $('#fixed-header1').DataTable();
                var row_data = table.row($(this).parents('tr')).data();
                if (is_detailed) {
                    $('#edit').modal('show');
                    var form = $('#form_product_edit');
                } else {
                    $('#edit_normal').modal('show');
                    var form = $('#form_product_edit_normal');
                }

                var action = form.attr('action');
                form.attr('action', action.replace(':id', row_data.id));
                console.log('Edit-DATA:', row_data);
                $('#edit').find('.modal-body #name_edit').val(row_data.name);
                $('#edit_normal').find('.modal-body #name_edit_normal').val(row_data.name);
                $('#edit').find('.modal-body #barcode_edits').val(row_data.barcode);
                $('#edit_normal').find('.modal-body #barcode_edits_normal').val(row_data.barcode);
                $('#edit').find('.modal-body #brand_edits').val(row_data.brand);
                $('#edit').find('.modal-body #pack_size_edit').val(row_data.pack_size);
                $('#edit').find('.modal-body #category_options').val(row_data.category_id);
                $('#edit_normal').find('.modal-body #category_options_normal').val(row_data.category_id);
                $('#edit').find('.modal-body #sale_edit').val(row_data.sales_uom);
                $('#edit_normal').find('.modal-body #sale_edit_normal').val(row_data.sales_uom);
                $('#edit').find('.modal-body #min_stock_edit').val(row_data.min_quantinty > 0 ? numberWithCommas(row_data.min_quantinty) : '');
                $('#edit_normal').find('.modal-body #min_stock_edit_normal').val(row_data.min_quantinty > 0 ? numberWithCommas(row_data.min_quantinty) : '');
                $('#edit').find('.modal-body #max_stock_edit').val(row_data.max_quantinty > 0 ? numberWithCommas(row_data.max_quantinty) : '');
                $('#edit').find('.modal-body #product_type').val(row_data.type);
                $('#edit').find('.modal-body #status_edit').val(row_data.status);
                $('#edit_normal').find('.modal-body #status_edit_normal').val(row_data.status);
                $('#edit').find('.modal-body #id').val(row_data.id);
                $('#edit_normal').find('.modal-body #id_normal').val(row_data.id);
            });

            $('#product-table').on('click', '#deletes', function () {
                var row_data = $('#fixed-header1').DataTable().row($(this).parents('tr')).data();
                let displayName = row_data.name;
                let packSizeLabel = "";

                if (row_data.brand) {
                    displayName += ' ' + row_data.brand;
                }
                if (row_data.pack_size) {
                    packSizeLabel += ' ' + row_data.pack_size;
                }
                if (row_data.sales_uom) {
                    packSizeLabel += row_data.sales_uom;
                }

                var message = "Are you sure you want to delete '" + displayName + packSizeLabel + "'?";
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

            function createOption() {
                var category = document.getElementById('category_option');
                var category_id = category.options[category.selectedIndex].value;
                filterCategory(category_id);
            }

            function createOptionNormal() {
                var category = document.getElementById('category_option_normal');
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
                                $('#sub_category').append($('<option>', { value: detail.id, text: detail.name }));
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
                                $('#sub_categories').append($('<option>', { value: detail.id, text: detail.name }));
                            });
                        }
                    });
                }

            }

            function saveFormData() {
                let $visibleForm = $('.modal.show form');
                if (!$visibleForm.length) {
                    notify('No active form found', 'top', 'right', 'danger');
                    return;
                }

                let formData = $visibleForm.serialize();

                $.ajax({
                    url: '{{ route('store-products') }}',
                    type: "post",
                    dataType: "json",
                    data: formData,
                    success: function (data) {
                        console.log('Response Data', data);
                        if (data[0].message === 'success') {
                            notify('Product added successfully', 'top', 'right', 'success');
                            let categoryVal = $('#category_option').val();
                            let categoryValNormal = $('#category_option_normal').val();
                            $('#form_product')[0].reset();
                            $('#form_product_normal')[0].reset();
                            $('#category_option').val(categoryVal).change();
                            $('#category_option_normal').val(categoryValNormal).change();
                        } else {
                            notify('Product name exists', 'top', 'right', 'danger');
                            // $('#category_option').val('0').change();
                            document.getElementById('form_product').reset();
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            console.log('Validation Errors:', errors);

                            let firstError = Object.values(errors)[0][0];
                            if (firstError === 'The name has already been taken.') {
                                notify('Product name exists', 'top', 'right', 'danger');
                            } else if (firstError === 'The barcode has already been taken.') {
                                notify('Product barcode exists', 'top', 'right', 'danger');
                            } else {
                                notify(firstError, 'top', 'right', 'danger');
                            }
                        } else {
                            notify('Something went wrong', 'top', 'right', 'danger');
                        }
                    }
                });
            }

            $('#form_product_normal').on('submit', function (e) {
                e.preventDefault();
                console.log('Submit clicked');
                var category = document.getElementById('category_option_normal');
                var category_id = category.options[category.selectedIndex].value;

                if (Number(category_id) === 0) {
                    document.getElementById('category_border').style.display = 'block';
                    return false;
                }

                document.getElementById('category_border').style.display = 'none';

                saveFormData();

            });

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

            $('#form_product_edit_normal').on('submit', function (e) {
                var category = document.getElementById('category_options_normal');
                var category_id = category.options[category.selectedIndex].value;

                if (Number(category_id) === 0) {
                    document.getElementById('category_borders_normal').style.display = 'block';
                    return false;
                }

                document.getElementById('category_borders_normal').style.display = 'none';
            });

            $('#pack_size_edit').on('change', function () {
                var val = document.getElementById('pack_size_edit').value;
                if (val !== '') {
                    document.getElementById('pack_size_edit').value =
                        numberWithCommas(parseFloat(val.replace(/\,/g, ''), 10));
                } else {
                    document.getElementById('pack_size_edit').value = '';
                }

            });

            $('#pack_size_edits').on('change', function () {
                var val = document.getElementById('pack_size_edits').value;
                if (val !== '') {
                    document.getElementById('pack_size_edits').value =
                        numberWithCommas(parseFloat(val.replace(/\,/g, ''), 10));
                } else {
                    document.getElementById('pack_size_edits').value = '';
                }

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

            $('#min_stock_edit_normal').on('change', function () {
                var min = document.getElementById('min_stock_edit_normal').value;

                if (min !== '') {
                    document.getElementById('min_stock_edit_normal').value =
                        numberWithCommas(parseFloat(min.replace(/\,/g, ''), 10));
                } else {
                    document.getElementById('min_stock_edit_normal').value = '';
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

            $('#min_stock_edits_normal').on('change', function () {
                var min = document.getElementById('min_stock_edits_normal').value;

                if (min !== '') {
                    document.getElementById('min_stock_edits_normal').value =
                        numberWithCommas(parseFloat(min.replace(/\,/g, ''), 10));
                } else {
                    document.getElementById('min_stock_edits_normal').value = '';
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

            $('#category_option_normal').select2({
                dropdownParent: $('#create_normal')
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