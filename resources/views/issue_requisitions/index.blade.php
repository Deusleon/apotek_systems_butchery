@extends("layouts.master")

@section('content-title')
    Stock Issue
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Inventory / Stock Issue / Issue List</a></li>
@endsection

@section('content')
<div class="col-sm-12">
    <!-- TAB LIST -->
    <ul class="nav nav-pills mb-3" id="issueTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active text-uppercase" id="issue-list-tab" data-toggle="pill"
               href="{{ route('issue.index') }}" role="tab" aria-controls="issue-list" aria-selected="true">Issue List</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-uppercase" id="issue-history-tab" data-toggle="pill"
               href="{{ route('requisitions-issue-history') }}" role="tab" aria-controls="issue-history" aria-selected="false">Issue History</a>
        </li>
    </ul>

    <!-- ISSUE LIST CONTENT -->
    <div class="tab-content card-block">
        <div class="table-responsive">
            <table id="table" class="display table nowrap table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Req #</th>
                        <th>Products</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Date</th>
                        <th>Status</th>
                        @if(Auth()->user()->checkPermission('View Requisitions Issue'))
                            <th>Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
@include('partials.notification')

<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // DataTable for Issue List
    var table = $('#table').DataTable({
        iDisplayLength: 10,
        processing: true,
        serverSide: true,
        ajax: { url: "{{ route('requisitions-issue-list') }}" },
        columns: [
            { data: 'req_no', name: 'req_no' },
            { data: 'products', name: 'products', searchable: false },
            { data: 'fromStore', name: 'fromStore', searchable: false },
            { data: 'toStore', name: 'toStore', searchable: false },
            { 
                data: 'reqDate',
                render: function(date) { return moment(date).format('YYYY-MM-DD'); },
                orderable: false,
                searchable: false
            },
            { 
                data: 'status',
                render: function(status) {
                    if(status == 0) return `<span class="badge badge-secondary p-1">Pending</span>`;
                    else if(status == 1) return `<span class="badge badge-success p-1">Approved</span>`;
                    else if(status == 2) return `<span class="badge badge-danger p-1">Denied</span>`;
                },
                orderable: false,
                searchable: false
            },
            @if (Auth()->user()->checkPermission('View Requisitions Issue'))
            { data: 'action', orderable: false, searchable: false }
            @endif
        ]
    });

    // Redirect tabs to separate routes
    $('#issue-history-tab').on('click', function(e){
        e.preventDefault();
        window.location.href = $(this).attr('href');
    });
</script>
@endpush
