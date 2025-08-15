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
                                        <th>Req. No</th>
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
                    var row = '<tr>' +
                        '<td>' + item.name + '</td>' +
                        '<td>' + item.unit + '</td>' +
                        '<td>' + item.quantity + '</td>' +
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
                tableBody.html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');

                $.ajax({
                    url: config.routes.retrieveRequisitions,
                    type: 'POST', // Make sure to use POST
                    data: {
                        _token: config.token, // CSRF token
                        req_id: id
                    },
                    dataType: 'json', // Specify JSON format
                    success: function(response) {
                        // Populate the table with the fetched data
                        console.log(response);
                        populateTable(response);
                        // console.log(response);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error fetching data: ', error);
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
                    name: 'products'
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
                    data: 'reqDate', render: function (date) {
                    return moment(date).format('MMM DD, YYYY');
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
                    { title: "Product Name" },
                    { title: "Unit" },
                    { title: "Quantity" }
                ]
            });
            details_table.destroy();
            details_table.clear();
            details_table.rows.add(details);
            details_table.draw();

        }


    </script>
@endpush
