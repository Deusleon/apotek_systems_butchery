@extends("layouts.master")

@section('content-title')
    Requisitions
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Inventory /Stock Requisition /Requisition list</a></li>
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
                    @if(Auth::user()->checkPermission('View Requisition List'))
                    <li class="nav-item">
                        <a class="nav-link active text-uppercase" id="requisitions"
                           href="{{ url('purchases/requisitions') }}" role="tab"
                           aria-controls="stock_list" aria-selected="false">Requisition List
                        </a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="table-responsive">
                            <table id="table" class="display table nowrap table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Req #</th>
                                        <th>Products</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Date</th>
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
    </div>



    @include('requisitions.details')

@endsection



@push('page_scripts')
    @include('partials.notification')

    <script>

    $(document).ready(function () {
        // ----- keep your config token and routes definition (if you already have it) -----
        var config = {
            token: '{{ csrf_token() }}',
            routes: {
                retrieveRequisitions: '{{route('requisitions.data')}}'
            }
        };

        // Initialize the modal table once and keep a reference
        var orderTable;
        if ($.fn.dataTable.isDataTable('#order_table')) {
            orderTable = $('#order_table').DataTable();
        } else {
            orderTable = $('#order_table').DataTable({
                responsive: true,
                searching: false,
                paging: false,
                info: false,
                ordering: false, // modal list - no ordering necessary
                columns: [
                    { title: "Product Name" },
                    { title: "Quantity" }
                ]
            });
        }

        // Robust populateTable using DataTables API
        function populateTable(rows) {
            orderTable.clear();

            if (!rows || rows.length === 0) {
                orderTable.row.add(['No products found', '']).draw();
                return;
            }

            rows.forEach(function(item) {
                // item may already have full_product_name
                var name = item.full_product_name || [
                    item.name, item.brand, item.pack_size, item.sales_uom
                ].filter(Boolean).join(' ');
                var qty = (item.quantity !== undefined ? item.quantity : '') + (item.unit ? ' ' + item.unit : '');
                orderTable.row.add([name, qty]);
            });

            orderTable.draw();
        }

        // When modal opens — fetch server data and fill table
        $('#requisition-details').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            // Show quick loading row
            orderTable.clear().row.add(['Loading...', '']).draw();

            $.ajax({
                url: config.routes.retrieveRequisitions,
                type: 'POST',
                data: {
                    _token: config.token,
                    req_id: id
                },
                dataType: 'json',
                success: function(response) {
                    var requisition = response.requisition || {};
                    var rows = response.products || [];

                    // Populate requisition info
                    $('#req_no').text(requisition.req_no || 'N/A');
                    $('#created_by').text(requisition.created_by || 'N/A');
                    $('#date_created').text(
                        requisition.created_at ? moment(requisition.created_at).format('YYYY-MM-DD') : 'N/A'
                    );

                    // Populate products table
                    populateTable(rows);
                },

                error: function(xhr, status, error) {
                    console.error('Error fetching requisition data:', error, xhr.responseText);
                    orderTable.clear().row.add(['Error loading data', '']).draw();
                }
            });
        });

    });


    </script>

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // DataTable initialization
        var table = $('#table').DataTable({
            iDisplayLength: 10,
            processing: true,
            serverSide: true,
            columnDefs: [{
                "targets": "_all"
            }],
            ajax: {
                url: "{{ route('requisitions-list') }}"
            },
            columns: [{
                    data: 'req_no',
                    name: 'req_no'
                },
                {
                    data: 'products',
                    name: 'products',
                    render: function(data, type, row) {
                        return data || '';
                    }
                },
                {
                    data: 'fromStore',
                    name: 'fromStore'
                },
                {
                    data: 'toStore',
                    name: 'toStore'
                },
                {
                    data: 'reqDate', 
                    render: function (date) {
                        return moment(date).format('YYYY-MM-DD');
                    },
                    orderable: false,
                },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
            ]
        });

        function showRequisitionDetails(details){
            var details_table = $('#requisitions-table').DataTable({
                searching: true,
                bPaginate:false,
                bInfo: true,
                data: details,
                columns: [
                    { 
                        title: "Product Name",
                        render: function(data, type, row) {
                            return row.full_product_name || 
                                (row.name + ' ' + (row.brand || '') + ' ' + 
                                (row.pack_size || '') + ' ' + (row.sales_uom || ''));
                        }
                    },
                    { 
                        title: "Quantity",
                        render: function(data, type, row) {
                            return row.quantity + (row.unit ? ' ' + row.unit : '');
                        }
                    }
                ]
            });
            details_table.destroy();
            details_table.clear();
            details_table.rows.add(details);
            details_table.draw();
        }


    </script>
@endpush
