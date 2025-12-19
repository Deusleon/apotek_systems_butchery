@extends("layouts.master")

@section('content-title')
    Stock Issue
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Inventory / Stock Issue / Issue History</a></li>
@endsection

@section('content')
    <div class="col-sm-12">
        <!-- TAB LIST -->
        <ul class="nav nav-pills mb-3" id="issueTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="issue-list-tab" href="{{ route('issue.index') }}" role="tab">Issue
                    List</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="issue-history-tab"
                    href="{{ route('requisitions-issue-history') }}" role="tab">Issue History</a>
            </li>
        </ul>

        <!-- ISSUE HISTORY CONTENT -->
        <div class="tab-content card-block">
            <div class="table-responsive">
                <table id="historyTable" class="display table nowrap table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Req #</th>
                            <th>Date Issued</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Products</th>
                            <th>Issued By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 📌 Bootstrap Modal -->
    <div class="modal fade" id="requisitionModal" tabindex="-1" role="dialog" aria-labelledby="requisitionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="requisitionModalLabel">Issue Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Dynamic content will be loaded here -->
                    <div id="requisitionDetails">
                        <p class="text-center text-muted">Loading details...</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    @include('partials.notification')

    <script>
        // Initialize DataTable for Issue History
        var table = $('#historyTable').DataTable({
            iDisplayLength: 10,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('requisitions-issue-history-list') }}",
                data: function (d) {
                    @php $currentStoreId = current_store_id(); @endphp
                    @if($currentStoreId != 1)
                        d.from_store_filter = "{{ $currentStoreId }}";
                    @endif
                }
            },
            columns: [
                { data: 'req_no', name: 'req_no' },
                {
                    data: 'reqDate',
                    render: function (date) { return moment(date).format('YYYY-MM-DD'); },
                    orderable: false,
                    searchable: false
                },
                { data: 'fromStore', name: 'fromStore' },
                { data: 'toStore', name: 'toStore' },
                {
                    data: 'products',
                    name: 'products',
                    render: function (data, type, row) {
                        return data || '';
                    },
                    searchable: false
                },
                { data: 'issued_by', name: 'issued_by' },
                { data: 'action', orderable: false, searchable: false }
            ]
        });

        // Intercept View button and show modal
        $(document).on('click', '.btn-view', function (e) {
            e.preventDefault();
            var reqId = $(this).data('id');
            $('#requisitionDetails').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2 text-muted">Loading details...</p></div>');
            $('#requisitionModal').modal('show');

            $.ajax({
                url: "{{ route('requisitions.data') }}",
                type: "POST",
                data: { req_id: reqId, _token: "{{ csrf_token() }}" },
                success: function (data) {
                    let requisition = data.requisition;
                    let products = data.products;

                    let html = `
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="detail-item">
                                <label class="font-weight-bold">Req #:</label>
                                <p style="font-size: 15px;">${requisition.req_no}</p>
                            </div>
                            <div class="detail-item">
                                <label class="font-weight-bold">From Branch:</label>
                                <p style="font-size: 15px;">${requisition.from_store}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-item">
                                <label class="font-weight-bold">To Branch:</label>
                                <p style="font-size: 15px;">${requisition.to_store}</p>
                            </div>
                            <div class="detail-item">
                                <label class="font-weight-bold">Issued By:</label>
                                <p style="font-size: 15px;">${requisition.issued_by}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-item">
                                <label class="font-weight-bold">Evidence:</label>
                                <div class="mt-1">
                                    ${requisition.evidence_document ?
                            `<a href="{{ asset('./fileStore/${requisition.evidence_document}') }}" target="_blank" class="btn btn-warning btn-sm color-body">View</a>` :
                            '<span class="text-muted">No document attached</span>'
                        }
                                </div>
                            </div>
                        </div>
                    </div>
                   <div class="d-flex justify-content-between align-items-center mb-2">
                        <div id="modalProductsSearch"></div> <!-- Search input will be moved here -->
                    </div>
                    <div class="table-responsive">
                        <table id="modalProductsTable" class="table table-striped table-hover table-sm">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold">Product Name</th>
                                    <th class="text-center font-weight-bold">Requested</th>
                                    <th class="text-center font-weight-bold">Issued</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    $.each(products, function (i, item) {
                        html += `<tr>
                                    <td class="font-weight-medium">${item.full_product_name || item.name}</td>
                                    <td class="text-center">${formatNumber(Number(item.quantity))}</td>
                                    <td class="text-center">${formatNumber(Number(item.issued))}</td>
                                </tr>`;
                    });

                    html += `</tbody></table></div>
                    <div class="detail-item mb-3">
                        <label class="font-weight-bold">Remarks:</label>
                        <p style="font-size: 15px;">${requisition.remarks || 'No remarks provided'}</p>
                    </div>`;

                    $('#requisitionDetails').html(html);

                    // Initialize DataTable for modal products with search
                    if ($.fn.DataTable.isDataTable('#modalProductsTable')) {
                        $('#modalProductsTable').DataTable().destroy();
                    }
                    $('#modalProductsTable').DataTable({
                        paging: true,
                        pageLength: 5,
                        lengthChange: false,
                        searching: true,       // Enable search input
                        ordering: true,
                        info: false,
                        dom: '<"top"f>rt<"bottom"p><"clear">' // Places search box on top nicely
                    });
                },
                error: function () {
                    $('#requisitionDetails').html('<div class="alert alert-danger text-center">Failed to load requisition details. Please try again.</div>');
                }
            });
        });


        // Redirect tabs to separate routes
        $('#issue-list-tab').on('click', function (e) {
            e.preventDefault();
            window.location.href = $(this).attr('href');
        });

        function formatNumber(digit) {
            return String(parseFloat(digit))
                .toString()
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    </script>

    <style>
        .detail-item {
            margin-bottom: 12px;
        }

        .detail-item label {
            margin-bottom: 4px;
            display: block;
            font-size: 0.9rem;
        }

        .detail-item p {
            margin: 0;
            font-size: 1rem;
        }

        .table th {
            font-size: 0.85rem;
        }

        .table td {
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .font-weight-medium {
            font-weight: 500;
        }

        /* Remove extra space between details and remarks */
        #requisitionDetails .row.mb-4 {
            margin-bottom: 0 !important;
        }

        #requisitionDetails .detail-item.mb-3 {
            margin-top: 0 !important;
        }
    </style>
@endpush