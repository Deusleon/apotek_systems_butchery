@extends("layouts.master")

@section('page_css')
    <style>
        .preview-table {
            max-height: 400px;
            overflow-y: auto;
        }

        .error-list {
            max-height: 200px;
            overflow-y: auto;
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .error-list ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .required-field::after {
            content: " *";
            color: red;
        }

        .loading {
            display: inline-block;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.65;
        }

        .import-history {
            max-height: 400px;
            overflow-y: auto;
        }

        .status-badge {
            width: 90px;
            padding: 0.25rem 0.5rem;
            border-radius: 100px;
            font-size: 0.875rem;
        }

        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }

        .status-processing {
            background-color: #b8daff;
            color: #004085;
        }

        .status-completed {
            background-color: #BBF7D0;
            color: #48bb78;
        }

        .status-failed {
            background-color: #FECACA;
            color: #f56565;
        }
    </style>
@endsection

@section('content-title')
    Products Import
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Inventory / Products Import</a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="invoice-received" href="{{ route('import-products') }}"
                    role="tab" aria-controls="quotes_list" aria-selected="false">Products Import</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="invoice-received" href="{{ route('import-data') }}" role="tab"
                    aria-controls="quotes_list" aria-selected="false">Stock Import</a>
            </li>
            {{-- @if (auth()->user()->checkPermission('Download Import Templates')) --}}
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="order-received" href="{{ route('download-products-template') }}"
                        role="tab" aria-controls="new_quotes" aria-selected="true"> Products Template
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-uppercase" id="order-received" href="{{ route('download-template') }}" role="tab"
                        aria-controls="new_quotes" aria-selected="true"> Stock Template
                    </a>
                </li>
            {{-- @endif --}}
        </ul>
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="feather icon-alert-circle"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('preview-products-import') }}" method="post" enctype="multipart/form-data"
                    id="importForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="file">Import File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" style="padding: 7px;" id="file" name="file"
                                    accept=".xlsx, .xls, .csv" required>
                                <small class="form-text text-muted">
                                    Accepted formats: Excel (.xlsx, .xls) or CSV (.csv)
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6 mt-4">
                            <div class="form-group mt-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-upload"></i> Preview Import
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Recent Import History</h5>
            </div>
            <div class="card-body">
                <div class="import-history table-responsive">
                    <table class="table table-sm table-striped" id="import_product_history">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>File Name</th>
                                {{-- <th>Branch</th> --}}
                                <th>Total Records</th>
                                <th>Succeded</th>
                                <th>Failed</th>
                                <th>Status</th>
                                <th>Imported By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($import_history as $history)
                                <tr>
                                    <td>{{ $history->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $history->file_name }}</td>
                                    {{-- <td>{{ $history->store->name }}</td> --}}
                                    <td>{{ number_format($history->total_records, 0) }}</td>
                                    <td class="text-success">{{ number_format($history->successful_records, 0) }}</td>
                                    <td class="text-danger">{{ number_format($history->failed_records, 0) }}</td>
                                    <td>
                                        <span class="status-badge btn btn-rounded status-{{ $history->status }}">
                                            {{ ucfirst($history->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $history->creator->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No import history found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
    <script>
        $(document).ready(function () {
            const form = $('#importForm');
            const fileInput = $('#file');
            const submitBtn = form.find('button[type="submit"]');
            const maxFileSize = 20 * 1024 * 1024; // 20MB in bytes

            // Update file input label with selected filename
            fileInput.on('change', function (e) {
                const file = this.files[0];
                const label = $(this).next('.custom-file-label');

                // Reset form state
                submitBtn.prop('disabled', false);
                $('.alert').remove();

                if (!file) {
                    label.html('Choose file');
                    return;
                }

                // Validate file
                if (file.size > maxFileSize) {
                    e.preventDefault();
                    fileInput.val('');
                    label.html('Choose file');
                    form.prepend(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="feather icon-alert-circle"></i> File size exceeds 20MB limit
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `);
                    return;
                }

                const extension = file.name.split('.').pop().toLowerCase();
                if (!['xlsx', 'xls', 'csv'].includes(extension)) {
                    e.preventDefault();
                    fileInput.val('');
                    label.html('Choose file');
                    form.prepend(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="feather icon-alert-circle"></i> Invalid file type. Please upload an Excel file (xlsx, xls) or CSV file
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `);
                    return;
                }

                label.html(file.name);
            });

            // Form submission
            form.on('submit', function (e) {
                e.preventDefault();

                // Validate required fields
                const requiredFields = form.find('[required]');
                let isValid = true;

                requiredFields.each(function () {
                    if (!$(this).val()) {
                        isValid = false;
                        const fieldName = $(this).prev('label').text().replace(' *', '');
                        form.prepend(`
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="feather icon-alert-circle"></i> Please select ${fieldName}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                `);
                    }
                });

                if (!isValid) {
                    return;
                }

                // Show loading state
                submitBtn.prop('disabled', true)
                    .html('<i class="feather icon-loader loading"></i> Uploading...');

                // Submit form
                this.submit();
            });
        });

        $('#import_product_history').DataTable({
            searching: true,
            bPaginate: true,
            bInfo: true,
            bSort: true,
            order: [[0, "asc"]],
        })
    </script>
@endsection