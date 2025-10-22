@extends("layouts.master")

@section('page_css')
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endsection

@section('content-title')
    Upload Stock
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Settings / Tools / Upload Stock</a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('tools.upload-stock') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="adjustment_reason">Adjustment Reason <span class="text-danger">*</span></label>
                                <select name="adjustment_reason" id="adjustment_reason" class="form-control" required>
                                    <option value="">Select Adjustment Reason</option>
                                    @foreach($adjustmentReasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <input type="hidden" name="adjustment_reason" id="adjustment_reason" value="Stock Upload">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="file">Upload File <span class="text-danger">*</span></label>
                                <input type="file" name="file" id="file" class="form-control" accept=".csv,.xlsx,.xls" required onchange="validateFile(this)">
                                <small class="form-text text-muted">Supported formats: CSV, Excel (.xlsx, .xls)</small>
                                <div id="file-error" class="text-danger mt-1" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>File Format Requirements:</strong>
                                <ul class="mb-0">
                                    <li>The file must contain exactly 3 columns: <strong>code</strong>, <strong>product name</strong>, and <strong>quantity</strong></li>
                                    <li>Code should match the product code/ID or barcode</li>
                                    <li>Product name should match the product name</li>
                                    <li>Quantity should be a numeric value (will override existing stock)</li>
                                    <li>First row should be headers</li>
                                </ul>
                            </div>
                        </div>
                    </div> --}}

                    <div class="row">
                        <div class="col-md-6 justify-content-start pl-2 d-flex">
                            <a href="{{ route('home') }}" class="btn btn-danger ml-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Upload Stock
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
                toastr.warning('Stock upload is not allowed when viewing ALL branches. Please select a specific branch.');

                // Disable form elements
                $('#adjustment_reason').prop('disabled', true);
                $('#file').prop('disabled', true);
                $('button[type="submit"]').prop('disabled', true);
            @endif
        });

        // File validation function
        function validateFile(input) {
            const file = input.files[0];
            const errorDiv = document.getElementById('file-error');
            const submitBtn = document.querySelector('button[type="submit"]');

            if (!file) {
                errorDiv.style.display = 'none';
                submitBtn.disabled = false;
                return;
            }

            // Check file extension
            const allowedExtensions = ['csv', 'xlsx', 'xls'];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedExtensions.includes(fileExtension)) {
                errorDiv.textContent = 'Invalid file type. Only CSV and Excel files (.csv, .xlsx, .xls) are allowed.';
                errorDiv.style.display = 'block';
                submitBtn.disabled = true;
                input.value = ''; // Clear the file input
                return;
            }

            // Check file size (10MB max)
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            if (file.size > maxSize) {
                errorDiv.textContent = 'File size too large. Maximum allowed size is 10MB.';
                errorDiv.style.display = 'block';
                submitBtn.disabled = true;
                input.value = ''; // Clear the file input
                return;
            }

            // If validation passes, hide error and enable submit
            errorDiv.style.display = 'none';
            submitBtn.disabled = false;
        }

        // Form validation before submission
        $('form').on('submit', function(e) {
            const fileInput = document.getElementById('file');
            const adjustmentReason = document.getElementById('adjustment_reason');

            if (!fileInput.files[0]) {
                e.preventDefault();
                toastr.error('Please select a file to upload.');
                return false;
            }

            if (!adjustmentReason.value) {
                e.preventDefault();
                toastr.error('Please select an adjustment reason.');
                return false;
            }

            // Show loading state
            $('button[type="submit"]').prop('disabled', true).text('Uploading...');
        });
    </script>
@endpush