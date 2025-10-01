@extends("layouts.master")

@section('page_css')
    <style>
        .preview-table {
            /* max-height: 500px; */
            overflow-y: auto;
        }

        table td,
        table th {
            white-space: normal !important;
            word-wrap: break-word;
        }

        .error-row {
            background-color: #fff3f3;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
        }

        .valid-row {
            background-color: #f3fff3;
        }

        .spinning {
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
    </style>
@endsection

@section('content-title')
    Preview Import Data
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('import-data') }}">Import</a></li>
    <li class="breadcrumb-item"><a href="#">Preview</a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Preview Import Data</h5>
            </div>
            <div class="card-body">
                @if(!empty($preview_data))
                    @php
                        $total_records = count($preview_data);
                        $error_count = collect($preview_data)->filter(function ($item) {
                            return !empty($item['errors']);
                        })->count();
                        $valid_count = $total_records - $error_count;
                    @endphp

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-body">
                                <div class="card-body">
                                    <h6 class="card-title">Total Records</h6>
                                    <h3 class="mb-0">{{ $total_records }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white" style="background-color:#BBF7D0; color:#48bb78;">
                                <div class="card-body">
                                    <h6 class="card-title" style="color: #48bb78">Valid Records</h6>
                                    <h3 class="mb-0" style="color: #48bb78">{{ $valid_count }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white" style="background-color:#FECACA; text-color:#f56565;">
                                <div class="card-body">
                                    <h6 class="card-title" style="color: #f56565">Records with Errors</h6>
                                    <h3 class="mb-0" style="color: #f56565">{{ $error_count }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="preview-table">
                        <table class="table" id="preview_products_import">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Barcode</th>
                                    <th>Brand</th>
                                    <th>Pack Size</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Min Stock</th>
                                    <th>Max Stock</th>
                                    <th>Validation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($preview_data as $row)
                                    <tr class="{{ !empty($row['errors']) ? 'error-row' : 'valid-row' }}">
                                        <td>{{ $row['data'][1] ?? '' }}</td>
                                        <td>{{ $row['data'][2] ?? '' }}</td>
                                        <td>{{ $row['data'][3] ?? '' }}</td>
                                        <td>{{ $row['data'][4] ?? '' }}</td>
                                        <td>{{ $row['data'][5] ?? '' }}</td>
                                        <td>{{ $row['data'][6] ?? '' }}</td>
                                        <td>{{ number_format((float)$row['data'][7], 0) ?? '' }}</td>
                                        <td>{{ number_format((float)$row['data'][8], 0) ?? '' }}</td>
                                        <td>
                                            @if(!empty($row['errors']))
                                                <span class="text-danger">
                                                    <i class="feather icon-alert-circle"></i>
                                                    {{ implode(', ', $row['errors']) }}
                                                </span>
                                            @else
                                                <span class="text-success">
                                                    <i class="feather icon-check-circle"></i> Valid
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('import-products') }}" class="btn btn-secondary">
                                <i class="feather icon-x-circle"></i> Cancel
                            </a>
                            @if($valid_count > 0)
                                <button type="button" class="btn btn-primary" id="importButton">
                                    <i class="feather icon-upload"></i> Import {{ number_format($valid_count, 0) }} Records
                                </button>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning" role="alert">
                        <i class="feather icon-alert-triangle"></i> No valid data found in the uploaded file.
                        <a href="{{ route('import-products') }}" class="btn btn-sm btn-warning ml-3">
                            Try Again
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function () {
            // Create a hidden form for submission
            var $hiddenForm = $('<form>', {
                'method': 'POST',
                'action': '{{ route('record-products-import') }}',
                'style': 'display: none'
            }).append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': '{{ csrf_token() }}'
            }));

            // Add form fields
            var formData = {
                'temp_file': '{{ $temp_file ?? "" }}',
                'store_id': '{{ $store_id ?? "" }}',
                'price_category_id': '{{ $price_category_id ?? "" }}',
                'supplier_id': '{{ $supplier_id ?? "" }}'
            };

            // Add each field to the form
            Object.keys(formData).forEach(function (key) {
                $hiddenForm.append($('<input>', {
                    'type': 'hidden',
                    'name': key,
                    'value': formData[key]
                }));
            });

            // Append form to body
            $hiddenForm.appendTo('body');

            // Handle the import button click
            $('#importButton').on('click', function (e) {
                e.preventDefault();

                var $btn = $(this);
                var originalText = $btn.html();

                // Disable the button and show loading state
                $btn.prop('disabled', true)
                    .html('<i class="feather icon-loader spinning"></i> Importing...');

                // Submit the form
                $hiddenForm.submit();
            });
        });

        $('#preview_products_import').DataTable({
            searching: true,
            bPaginate: true,
            bInfo: true,
            bSort: true,
            order: [[0, "asc"]],
        });
    </script>
@endpush