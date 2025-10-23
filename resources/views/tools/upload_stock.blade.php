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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="store_id">Select Branch <span class="text-danger">*</span></label>
                                <select name="store_id" id="store_id" class="form-control" required>
                                    <option value="">Select Branch</option>
                                    @if(is_all_store())
                                        @foreach($stores as $store)
                                            @if($store->id > 1)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="{{ current_store()->id }}" selected>{{ current_store()->name }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
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
                toastr.warning('Please select a branch to upload stock for.');

                // Disable form elements
                $('#store_id').prop('disabled', false);
                $('#file').prop('disabled', true);
                $('button[type="submit"]').prop('disabled', true);

                // Enable file input when store is selected
                $('#store_id').on('change', function() {
                    if ($(this).val()) {
                        $('#file').prop('disabled', false);
                        $('button[type="submit"]').prop('disabled', false);
                    } else {
                        $('#file').prop('disabled', true);
                        $('button[type="submit"]').prop('disabled', true);
                    }
                });
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
            @if(is_all_store())
            const storeId = $('#store_id').val();

            if (!storeId) {
                e.preventDefault();
                toastr.error('Please select a branch.');
                return false;
            }
            @endif

            if (!fileInput.files[0]) {
                e.preventDefault();
                toastr.error('Please select a file to upload.');
                return false;
            }

            // Show loading state
            $('button[type="submit"]').prop('disabled', true).text('Uploading...');
        });
    </script>
@endpush