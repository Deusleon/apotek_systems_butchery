@extends("layouts.master")

@section('content-title')
    Requisitions
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / New Requisition</a></li>
@endsection

@section('content')

    <div class="col-sm-12">

        <div class="card-block">
            <div class="col-sm-12">
                <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active text-uppercase" id="requisition-create" data-toggle="pill"
                           href="{{ url('purchases/requisitions-create') }}" role="tab"
                           aria-controls="current-stock" aria-selected="true">New</a>
                    </li>
                    @if(Auth::user()->checkPermission('View Requisition List'))
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="requisitions" data-toggle="pill"
                           href="{{ url('purchases/requisitions') }}" role="tab"
                           aria-controls="stock_list" aria-selected="false">Requisition List
                        </a>
                    </li>
                    @endif
                </ul>
               <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <form action="{{ route('requisitions.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <!-- Store and Products Selection -->
                            <div class="row mb-3">
                                <div class="form-group col-md-3">
                                    <label for="from_store">Requesting From <font color="red">*</font></label>
                                    
                                    @if(!auth()->user()->checkPermission('Manage All Branches'))
                                    <select name="from_store" class="js-example-basic-single form-control" id="from_store" required>
                                        <option value="">Select Branch...</option>
                                        @foreach ($stores as $item)
                                            @if($item->id != Auth::user()->store_id)
                                                <option value="{{ $item->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $item->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @endif
                                    
                                    @if(auth()->user()->checkPermission('Manage All Branches'))
                                    <select name="from_store" class="js-example-basic-single form-control" id="from_store" required>
                                        <option value="">Select Branch...</option>
                                        @foreach ($stores as $item)
                                            @if($item->id != Auth::user()->store_id)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @endif
                                </div>

                                <!--Concurtination of product name, brand, pack_size and sales_uom-->
                                <div class="form-group col-md-6">
                                    <label for="products">Select Products <font color="red">*</font></label>
                                    <select name="products" class="js-example-basic-single form-control products" id="products">
                                        <option value="">Select Products...</option>
                                        @foreach ($items as $item)
                                            <option value='@json($item)'>
                                                {{ $item->name }} {{ $item->brand }} {{ $item->pack_size }} {{ $item->sales_uom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    {{-- reserved space --}}
                                </div>
                            </div>

                            <!-- Hidden Orders Field -->
                            <input type="hidden" name="orders" id="orders">

                            <!-- Order Table -->
                            <div class="table-responsive mb-3">
                                <table style="width: 100%" class="table nowrap table-striped table-hover" id="order_table">
                                    <thead>
                                        <tr class="bg-navy disabled">
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <!-- Remarks (Left side) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><b>Remarks</b></label>
                                        <textarea id="remark" name="remark" class="form-control" rows="2" placeholder="Enter Remarks Here"></textarea>
                                    </div>
                                </div>

                                <!-- File Upload (Right side) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><b>Evidence <font color="red">*</font></b></label>
                                        <input type="file" id="evidence" name="evidence" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png" required>
                                    </div>
                                </div>
                            </div>

                            <hr> <!-- visible separator between remarks and buttons -->

                            <!-- Buttons Row -->
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-end">
                                    <a href="{{ route('requisitions.create') }}" class="btn btn-danger me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="submit_btn">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection



@push('page_scripts')
    @include('partials.notification')
    <script>
    $(document).ready(function () {
        $('#requisitions').on('click', function(e) {
            e.preventDefault();
            var redirectUrl = $(this).attr('href');
            window.location.href = redirectUrl;
        });

        $('#requisition-create').on('click', function(e) {
            e.preventDefault();
            var redirectUrl = $(this).attr('href');
            window.location.href = redirectUrl;
        });
    });

    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    let cart = {
        data: [],
        drawTable: function() {
            $('#order_table').DataTable().clear();
            $('#order_table').DataTable().rows.add(this.data);
            $('#order_table').DataTable().draw();
            $('#orders').val(JSON.stringify(this.data));
        },
        addData: function(data) {
            function hasSameID(item) {
                return item.itemss.id == data.itemss.id;
            }

            if (this.data.some(hasSameID)) {
                this.data.forEach(function(item) {
                    if (!hasSameID(item)) return;
                    item.quantity = ++item.quantity;
                });
            } else {
                this.data.unshift(data);
            }
            this.drawTable();
        },
        editQuantity: function(item_to_edit, quantity) {
            function hasSameID(item) {
                return item.itemss.id == item_to_edit.itemss.id;
            }
            this.data.forEach(function(item) {
                if (!hasSameID(item)) return;
                quantity = Number(quantity);
                if (!quantity || quantity <= 0) return;
                item.quantity = quantity;
            });
            this.drawTable();
        },
        editUnit: function(item_to_edit, unit) {
            function hasSameID(item) {
                return item.itemss.id == item_to_edit.itemss.id;
            }
            this.data.forEach(function(item) {
                if (!hasSameID(item)) return;
                if (!unit) return;
                item.unit = unit;
            });
            this.drawTable();
        },
        deleteItem: function(item_to_delete) {
            this.data = this.data.filter(function(item) {
                return item.itemss.id != item_to_delete.itemss.id;
            });
            this.drawTable();
        }
    }

    var order_table = $('#order_table').DataTable({
        dom: "t",
        ordering: false,
        oLanguage: {
            "sEmptyTable": "No data available in table"
        },
        columns: [{
                data: 'itemss',
                render: function(data) {
                    if (!data) return "";
                    return data.name + ' ' + data.brand + ' ' + data.pack_size + ' ' + data.sales_uom;
                }
            },
            {
                data: 'quantity',
                render: function(data, type, row) {
                    if (type === 'display') {
                        // Display as plain text (no input field)
                        return data.toLocaleString();
                    }
                    return data;
                }
            },
            {
                data: 'action',
                defaultContent: '<button type="button" onclick="enableEdit(event)" class="btn btn-primary btn-rounded btn-sm edit-btn" title="EDIT">Edit</button>' +
                    '<button onclick="deleteItem(event)" type="button" class="btn btn-danger btn-rounded btn-sm" title="DELETE">Delete</button>'
            }
        ]
    });

    function enableEdit(event) {
        const row = event.target.closest('tr');
        const rowIndex = order_table.row(row).index();
        const rowData = order_table.row(rowIndex).data();
        
        // Replace quantity text with input field
        const quantityCell = row.cells[1];
        quantityCell.innerHTML = `
            <input type="text" 
                   class="form-control" 
                   value="${rowData.quantity}" 
                   step="any"
                   min="1"
                   onblur="saveQuantityChange(event, ${rowIndex})"
                   onkeypress="handleQuantityKeyPress(event, ${rowIndex})">
        `;
        
        // Focus the input field
        const inputField = quantityCell.querySelector('input');
        inputField.focus();
        inputField.select();
        
    }

    function saveQuantityChange(event, rowIndex) {
        const row = order_table.row(rowIndex).node();
        const inputField = row.querySelector('input');
        const newQuantity = parseFloat(inputField.value);
        
        if (!isNaN(newQuantity) && newQuantity > 0) {
            const rowData = order_table.row(rowIndex).data();
            cart.editQuantity(rowData, newQuantity);
            
            // Change back to Edit button
            const saveButton = row.querySelector('.btn-success');
            saveButton.textContent = 'Edit';
            saveButton.setAttribute('onclick', 'enableEdit(event)');
            saveButton.classList.remove('btn-success');
            saveButton.classList.add('btn-primary');
        } else {
            // If invalid quantity, revert back to original value
            const rowData = order_table.row(rowIndex).data();
            const quantityCell = row.cells[1];
            quantityCell.innerHTML = rowData.quantity.toLocaleString();
            
            // Revert button back to Edit
            const saveButton = row.querySelector('.btn-success');
            saveButton.textContent = 'Edit';
            saveButton.setAttribute('onclick', 'enableEdit(event)');
            saveButton.classList.remove('btn-success');
            saveButton.classList.add('btn-primary');
        }
    }

    function handleQuantityKeyPress(event, rowIndex) {
        if (event.key === 'Enter') {
            saveQuantityChange(event, rowIndex);
        }
    }

    function deleteItem(event) {
        let item = order_table.row($(event.target).closest('tr')).data();
        cart.deleteItem(item);
    }

    function unitChange(event) {
        let unit = $(event.target).val();
        let item = order_table.row($(event.target).closest('tr')).data();
        cart.editUnit(item, unit);
    }

    $('.products').on('change', function(event) {
        if (!$(this).val()) return;
        
        let itemss = JSON.parse($(this).val());
        $(this).val('').trigger('change');
        
        $.ajax({
            url: "{{ route('search_items') }}",
            type: 'get',
            dataType: 'json',
            data: { item_id: itemss.id },
            success: function(data) {
                cart.addData({
                    itemss: itemss,
                    quantity: 1,
                    unit: ''
                });
            }
        });
    });
</script>
@endpush
