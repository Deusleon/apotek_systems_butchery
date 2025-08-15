@extends("layouts.master")

@section('content-title')
    Requisitions Issue
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Requisitions Issue</a></li>
@endsection

@section('content')

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="table-responsive">
                            <table id="table" class="display table nowrap table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Req. No</th>
                                        <th>Products</th>
                                        <th>Status</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Date</th>
                                        @can('View Requisitions Details')
                                            <th>Action</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody id="table-body">

                                </tbody>
                            </table>
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
                url: "{{ route('requisitions-issue-list') }}"
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
                    data: 'status',
                    render: function(status, type, row) {
                        if (status == 0) {
                            return `<span class="badge badge-secondary p-1">Pending</span>`
                        } else if (status == 1) {
                            return `<span class="badge badge-success p-1">Approved</span>`
                        } else if (status == 2) {
                            return `<span class="badge badge-danger p-1">Denied</span>`
                        }
                    },
                    orderable: false,
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



    </script>
@endpush
