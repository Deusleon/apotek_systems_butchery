@extends("layouts.master")

@section('content-title')
    Requisitions
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Requisitions</a></li>
@endsection

@section('content')

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="requisition-create" data-toggle="pill"
                           href="{{ url('purchases/requisitions-create') }}" role="tab"
                           aria-controls="current-stock" aria-selected="true">New</a>
                    </li>
                    @if(Auth::user()->checkPermission('View Requisition List'))
                    <li class="nav-item">
                        <a class="nav-link active text-uppercase" id="requisitions" data-toggle="pill"
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
                                        @can('View Requisitions Details')
                                            <th>Action</th>
                                        @endcan
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

            $('#order_table').DataTable({
                responsive: true,
                order: [[0, 'asc']]
            });

            $('#requisitions').on('click', function(e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#requisition-create').on('click', function(e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            // Populate Data to table
           function populateTable(data) {
                var tableBody = $('#order_table tbody');
                tableBody.empty(); // Clear any existing rows

                // Loop through the data and append rows to the table
                data.forEach(function(item) {
                    // Use the concatenated product name from the server
                    var productName = item.full_product_name || 
                                    (item.name + ' ' + (item.brand || '') + ' ' + 
                                    (item.pack_size || '') + ' ' + (item.sales_uom || ''));
                    
                    var row = '<tr>' +
                        '<td>' + productName + '</td>' +
                        '<td>' + item.quantity + (item.unit ? ' ' + item.unit : '') + '</td>' +
                        '</tr>';
                    tableBody.append(row);
                });
            }

            //Endpoints
            var config = {
                token: '{{ csrf_token() }}',
                routes: {
                    retrieveRequisitions: '{{route('requisitions.data')}}'
                }
            };

            //Display
            $('#requisition-details').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                var tableBody = $('#order_table tbody');
                tableBody.html('<tr><td colspan="2" class="text-center">Loading...</td></tr>');

                $.ajax({
                    url: config.routes.retrieveRequisitions,
                    type: 'POST',
                    data: {
                        _token: config.token,
                        req_id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        populateTable(response);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error fetching data: ', error);
                        tableBody.html('<tr><td colspan="2" class="text-center text-danger">Error loading data</td></tr>');
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
                @can('View Requisitions Details')
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                @endcan
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
