@extends("layouts.master")

@section('content-title')
    Requisitions
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Requisitions / Requisition Details</a></li>
@endsection

@section('content')

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="requisition-create" 
                        href="{{ url('purchases/requisitions-create') }}" role="tab"
                        aria-controls="current-stock" aria-selected="true">New</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-uppercase" id="edit-requisition" 
                        href="#" role="tab"
                        aria-controls="edit-requisition" aria-selected="false">Edit Requisition</a>
                    </li>
                    @if(Auth::user()->checkPermission('View Requisition List'))
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="requisitions" 
                        href="{{ url('purchases/requisitions') }}" role="tab"
                        aria-controls="stock_list" aria-selected="false">Requisition List</a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <form action="{{ route('requisitions.update') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="user">Requisition No</label>
                                    <h6>{{ $requisition->req_no }}</h6>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="user">Created By</label>
                                    <h6>{{ $requisition->creator->name }}</h6>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="user">Date Created</label>
                                    <h6>{{ date('Y-m-d', strtotime($requisition->created_at)) }}</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="from_store">Requesting From<font color="red">*</font></label>
                                    @if(!auth()->user()->checkPermission('Manage All Branches'))
                                        <select name="from_store" class="js-example-basic-single form-control" id="from_store"
                                                required disabled="true">
                                            <option value="">Select Store...</option>
                                            @foreach ($stores as $item)
                                                <option value='{{ $item->id }}' {{ $item->id == Auth::user()->store_id ? 'selected="selected"' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif

                                    @if(auth()->user()->checkPermission('Manage All Branches'))
                                        <select name="from_store" class="js-example-basic-single form-control" id="from_store"
                                                required>
                                            <option value="">Select Store...</option>
                                            @foreach ($stores as $item)
                                                <option value='{{ $item->id }}' {{ $item->id == Auth::user()->store_id ? 'selected="selected"' : '' }}>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="products">Select Products <font color="red">*</font></label>
                                    <select name="products" class="js-example-basic-single form-control products"
                                        id="products">
                                        <option value="">Select Products...</option>
                                        @foreach ($items as $item)
                                            <option value='@json($item)'>
                                                {{ $item->name }} {{ $item->brand }} {{ $item->pack_size }} {{ $item->sales_uom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <!-- Empty space -->
                                </div>
                            </div>
                            <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">
                            <div class="table-responsive">
                                <input type="hidden" name="orders" id="orders">
                                <table style="width: 100%" class="table nowrap table-striped table-hover" id="order_table">
                                    <thead>
                                        <tr class="bg-navy disabled">
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                            <!-- First Row: Remarks and Upload Document -->
                            <!-- First Row: Remarks and Upload Document -->
                            <hr>
                            <div class="d-flex align-items-start">
                                <!-- Remarks (bigger width) -->
                                <div class="mr-5" style="flex: 5;">
                                    <label for="remark"><b>Remarks</b></label>
                                    <textarea type="text" class="form-control" id="remark" name="remark" placeholder="Enter Remarks Here">{{ $requisition->remarks ?? '' }}</textarea>
                                </div>

                                <!-- New Evidence -->
                                <div class="mr-2" style="flex: 4;">
                                    <label for="evidence"><b>New Evidence <font color="red">*</font></b></label>
                                    <input type="file" id="evidence" name="evidence" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                                </div>

                                <!-- Existing Evidence -->
                                <div style="flex: 1;">
                                    <label class="form-label mb-2"><b>Existing</b></label>
                                    @if($requisition->evidence_document)
                                        <div>
                                            <a href="{{ asset('storage/' . $requisition->evidence_document) }}" 
                                            target="_blank" 
                                            class="btn btn-warning text-body"
                                            title="View Document">
                                                View
                                            </a>
                                        </div>
                                    @else
                                        <div class="alert alert-warning py-2 mb-0">
                                            <i class="feather icon-alert-triangle"></i> None
                                        </div>
                                    @endif
                                </div>
                            </div>


                            <!-- Second Row: Cancel and Update Buttons -->
                            <hr>
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-end">
                                    <a href="{{ route('requisitions.index') }}" class="btn btn-danger me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id='submit_btn'>Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection



@push('page_scripts')
    @include('partials.notification')
    <script>
        // CSRF Token
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        let datas = @json($requisitionDet);
        console.log(datas);
        let cart = {
            data: datas,
            drawTable: function() {
                $('#order_table').DataTable().clear();
                $('#order_table').DataTable().rows.add(this.data);
                $('#order_table').DataTable().draw();

                $('#orders').val(JSON.stringify(this.data));

            },
            addData: function(data) {
                function hasSameID(item) {
                    console.log(item, data);
                    return item.products_.id == data.products_.id;
                }

                if (this.data.some(hasSameID)) {
                    this.data.forEach(function(item) {
                        if (!hasSameID(item)) {
                            return
                        }
                        item.quantity = ++item.quantity;
                        // item.avl_qty = data.avl_qty;
                    });
                } else {
                    this.data.push(data);
                }

                this.drawTable();
            },

            editQuantity: function(item_to_edit, quantity) {
                function hasSameID(item) {
                    return item.products_.id == item_to_edit.products_.id;
                }
                this.data.forEach(function(item) {
                    if (!hasSameID(item)) {
                        return;
                    }
                    quantity = Number(quantity);
                    if (!quantity) {
                        return;
                    }
                    item.quantity = quantity;
                });
                this.drawTable();
            },

            editUnit: function(item_to_edit, unit) {
                function hasSameID(item) {
                    return item.products_.id == item_to_edit.products_.id;
                }
                this.data.forEach(function(item) {
                    if (!hasSameID(item)) {
                        return;
                    }

                    if (!unit) {
                        return;
                    }
                    item.unit = unit;
                });
                this.drawTable();
            },

            deleteItem: function(item_to_delete) {
                console.log(item_to_delete);
                this.data = this.data.filter(function(item) {
                    return item.products_.id != item_to_delete.products_.id;
                });
                this.drawTable();
            },

        }

        var order_table = $('#order_table').DataTable({
            dom: "t",
            ordering: false,
            oLanguage: {
                "sEmptyTable": "No data available in table"
            },
            columns: [{
                    data: 'products_',
                    render: function(data) {
                        if (!data) {
                            return "";
                        }
                        return data.name + ' ' + (data.brand || '') + ' ' + (data.pack_size || '') + ' ' + (data.sales_uom || '');
                    }
                },
                {
                    data: 'quantity',
                    render: function(data) {
                        datas = data.toLocaleString();
                        return `<input type="text" onblur="quantityChange(event)" class="text-left w-100 border-0" step="any" name="qty" value="${datas??""}" readonly style="color: inherit; background-color: transparent;"/>`;
                    }
                },
                {
                    data: 'action',
                    defaultContent: '<button  type="button" onclick="enableEdit(event)" class="btn btn-primary btn-rounded btn-sm" id="edit_btn" title="EDIT">Edit</button>'+
                        '<button onclick="deleteItem(event)" type="button" class="btn btn-danger btn-rounded btn-sm" title="DELETE">Delete</button>'
                }
            ]
        });
        cart.drawTable();

        function enableEdit(event) {
            // Locate the parent row of the button clicked
            const row = event.target.closest('tr');

            // Find the input field within this row
            const inputField = row.querySelector('input[name="qty"]');

            // Enable editing by removing the readonly attribute
            if (inputField) {
                inputField.removeAttribute('readonly');
                inputField.focus(); // Optionally, focus the input for user convenience

                // Apply styles to the input field
                inputField.style.width='20%'
                inputField.style.border = '1px solid #000000'; // Example: blue border
                inputField.style.borderRadius = '4px';        // Rounded corners
                inputField.style.boxShadow = '0 0 5px rgba(0, 0, 0, 0.25)';
                inputField.style.padding = '10px 20px';            // Remove default focus outline
                inputField.style.backgroundColor = '#f4f7fa'; // Example: light blue background
                inputField.style.transition = 'border-color .15s ease-in-out,box-shadow .15s ease-in-out'; // Smooth transition effect

            }
        }

        function deleteItem(event) {
            let item = $('#order_table').DataTable().row($(event.target).parents('tr')).data();
            cart.deleteItem(item);
        }

        function quantityChange(event) {
            let quantity = $(event.target).val();
            let item = $('#order_table').DataTable().row($(event.target).parents('tr')).data();
            cart.editQuantity(item, quantity);
        }

        function unitChange(event) {
            let unit = $(event.target).val();
            let item = $('#order_table').DataTable().row($(event.target).parents('tr')).data();
            cart.editUnit(item, unit);
        }

        $('.products').on('change', function(event) {
            if (!$(this).val()) {
                return
            }
            let itemss = JSON.parse($(this).val());
            $(this).val('').trigger('change');
            $.ajax({
                url: "{{ route('search_items') }}",
                type: 'get',
                dataType: 'json',
                data: {
                    item_id: itemss,
                },
                success: function(data) {
                    cart.addData({
                        products_: itemss,
                        quantity: 1,
                        unit: ''
                    });
                }
            })
        });
    </script>
@endpush
