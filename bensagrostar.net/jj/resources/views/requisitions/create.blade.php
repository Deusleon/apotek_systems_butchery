@extends("layouts.master")

@section('content-title')
    Requisitions
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Stores / Requisitions / New Requisition</a></li>
@endsection

@section('content')

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <form action="{{ route('requisitions.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="from_store">From <font color="red">*</font></label>
                                    <select name="from_store" class="js-example-basic-single form-control" id="from_store"
                                        required>
                                        <option value="">Select Store...</option>
                                        @foreach ($stores as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="to_store">To <font color="red">*</font></label>
                                    <select name="to_store" class="js-example-basic-single form-control" id="to_store"
                                        required>
                                        <option value="">Select Store...</option>
                                        @foreach ($stores as $item)
                                            <option value='{{ $item->id }}'>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="products">Select Products <font color="red">*</font></label>
                                    <select name="products" class="js-example-basic-single form-control products"
                                        id="products">
                                        <option value="">Select Products...</option>
                                        @foreach ($items as $item)
                                            <option value='@json($item)'>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="orders" id="orders">
                            <div class="table-responsive">
                                <table style="width: 100%" class="table nowrap table-striped table-hover" id="order_table">
                                    <thead>
                                        <tr class="bg-navy disabled">
                                            <th class="text-center">Item Name</th>
                                            <th class="text-center">Unit</th>
                                            <th class="text-center">Quantity</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            {{-- <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="products">Notes</label>
                                    <textarea class="form-control" name="notes" id="" rows="2"></textarea>
                                </div>
                            </div> --}}
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" id='submit_btn'>Submit</button>
                                <a href="{{ route('requisitions.index') }}" class="btn btn-danger">Close</a>
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
                    return item.itemss.id == item_to_edit.itemss.id;
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
                    return item.itemss.id == item_to_edit.itemss.id;
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
                    return item.itemss.id != item_to_delete.itemss.id;
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
                    data: 'itemss',
                    render: function(data) {
                        if (!data) {
                            return
                        }
                        return data.name
                    }
                },
                {
                    data: 'unit',
                    render: function(data) {
                        datas = data.toLocaleString();
                        return `<input type="text" onblur="unitChange(event)" class="text-center w-100 border-0" name="unit" value="${datas??""}" />`;
                    }
                },
                {
                    data: 'quantity',
                    render: function(data) {
                        datas = data.toLocaleString();
                        return `<input type="text" onblur="quantityChange(event)" class="text-center w-100 border-0" step="any" name="qty" value="${datas??""}" />`;
                    }
                },
                {
                    data: 'action',
                    defaultContent: '<button onclick="deleteItem(event)" type="button" class="btn btn-danger btn-rounded btn-sm" title="DELETE">Delete</button>'
                }
            ]
        });

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
                        itemss: itemss,
                        quantity: 1,
                        unit: ''
                    });
                }
            })
        });
    </script>
@endpush
