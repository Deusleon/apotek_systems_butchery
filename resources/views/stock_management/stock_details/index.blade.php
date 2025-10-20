@extends("layouts.master")

@section('page_css')
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .btn-edit {
            padding: 2px 8px;
            font-size: 14px;
        }
    </style>
@endsection

@section('content-title')
    Stock Details
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Inventory / Stock Details </a></li>
@endsection

@section("content")

    @if (auth()->user()->checkPermission('View Current Stock'))
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="stock-details-table" class="table table-striped table-hover mb-3"
                            style="background: white;width: 100%; font-size: 14px;">

                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Batch Number</th>
                                    @if ($expireEnabled)
                                        <th>Expiry Date</th>
                                    @endif
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($detailed as $data)
                                    <tr data-stock-id="{{ $data->id }}">
                                        <td>{{ $data->name }}
                                            {{ $data->brand ? ' ' . $data->brand : '' }}
                                            {{ $data->pack_size ?? '' }}{{ $data->sales_uom ?? '' }}
                                        </td>
                                        <td>{{ $data->cat_name }}</td>
                                        <td class="batch-number">{{ $data->batch_number ?? '' }}</td>
                                        @if ($expireEnabled)
                                            <td class="expiry-date">{{ $data->expiry_date ?? '' }}</td>
                                        @endif
                                        <td>{{ floor($data->quantity) == $data->quantity ? number_format($data->quantity, 0) : number_format($data->quantity, 1) }}</td>
                                        <td>
                                            @if(auth()->user()->checkPermission('Edit Detailed Stock'))
                                                <button type="button" class="btn btn-primary btn-rounded btn-lg btn-edit"
                                                    onclick="editStockDetails({{ $data->id }}, '{{ $data->batch_number ?? '' }}', '{{ $data->expiry_date ?? '' }}')">
                                                    Edit
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Modal -->
    <div class="modal fade" id="editStockModal" tabindex="-1" role="dialog" aria-labelledby="editStockModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStockModalLabel">Edit Stock Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editStockForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_stock_id" name="stock_id">
                        <div class="form-group">
                            <label for="edit_batch_number">Batch Number</label>
                            <input type="text" class="form-control" id="edit_batch_number" name="batch_number">
                        </div>
                        @if ($expireEnabled)
                        <div class="form-group">
                            <label for="edit_expiry_date">Expiry Date</label>
                            <input type="date" class="form-control" id="edit_expiry_date" name="expiry_date">
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@include('partials.notification')

@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>

    <script>
        $(document).ready(function () {
            $('#stock-details-table').DataTable({
                responsive: true,
                order: [[0, 'asc']]
            });
        });

        function editStockDetails(stockId, batchNumber, expiryDate) {
            $('#edit_stock_id').val(stockId);
            $('#edit_batch_number').val(batchNumber || '');
            $('#edit_expiry_date').val(expiryDate || '');
            $('#editStockModal').modal('show');
        }

        $('#editStockForm').on('submit', function(e) {
            e.preventDefault();

            const stockId = $('#edit_stock_id').val();
            const batchNumber = $('#edit_batch_number').val();
            const expiryDate = $('#edit_expiry_date').val();

            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Saving...');

            // Direct update - just update the specific stock record
            $.ajax({
                url: '{{ route("current-stock.update") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    items: [{
                        id: stockId,
                        batch_number: batchNumber,
                        expiry_date: expiryDate
                    }]
                },
                success: function(response) {
                    $('#editStockModal').modal('hide');
                    // Update the table row
                    const row = $('tr[data-stock-id="' + stockId + '"]');
                    row.find('.batch-number').text(batchNumber || '');
                    row.find('.expiry-date').text(expiryDate || '');

                    toastr.success('Stock details updated successfully!');
                    submitBtn.prop('disabled', false).text(originalText);
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while updating stock details.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
    </script>
@endpush
