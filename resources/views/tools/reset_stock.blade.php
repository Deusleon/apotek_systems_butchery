@extends("layouts.master")

@section('page_css')
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection

@section('content-title')
    Reset Stock
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Settings / Tools / Reset Stock</a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('tools.reset-stock') }}" method="POST" id="reset-stock-form">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="adjustment_reason">Reason <span class="text-danger">*</span></label>
                                <select name="adjustment_reason" id="adjustment_reason" class="form-control" required>
                                    <option value="">Select reset Reason</option>
                                    @foreach($adjustmentReasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 justify-content-start d-flex">
                            <a href="{{ route('home') }}" class="btn btn-secondary ml-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-danger" id="reset-btn">
                                Reset Stock
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push("page_scripts")
    <script>
        $(document).ready(function () {
            $('select.form-control').select2({
                placeholder: function () {
                    return $(this).data('placeholder') || 'Select an option';
                }
            });

            // Check if in ALL branch mode and show notification
            @if(is_all_store())
                // Show warning notification
                toastr.warning('Stock reset is not allowed when viewing ALL branches. Please select a specific branch.');

                // Disable form elements
                $('#adjustment_reason').prop('disabled', true);
                $('#reset-btn').prop('disabled', true);
            @endif
        });

        // Form validation before submission
        $('#reset-stock-form').on('submit', function(e) {
            const adjustmentReason = $('#adjustment_reason').val();

            if (!adjustmentReason) {
                e.preventDefault();
                toastr.error('Please select an adjustment reason.');
                return false;
            }

            // Show confirmation modal instead of browser confirm
            e.preventDefault();

            // Create and show confirmation modal
            const modalHtml = `
                <div class="modal fade" id="confirmResetModal" tabindex="-1" role="dialog" aria-labelledby="confirmResetModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmResetModalLabel">
                                    Confirm Reset Stock
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to reset stock? This action cannot be undone</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmResetBtn">
                                   Confirm
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('body').append(modalHtml);
            $('#confirmResetModal').modal('show');

            // Handle confirm button click
            $('#confirmResetBtn').on('click', function() {
                $('#confirmResetModal').modal('hide');

                // Show loading state
                $('#reset-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Resetting...');

                // Submit the form
                $('#reset-stock-form').off('submit').submit();
            });

            // Clean up modal when hidden
            $('#confirmResetModal').on('hidden.bs.modal', function() {
                $(this).remove();
            });
        });
    </script>
@endpush