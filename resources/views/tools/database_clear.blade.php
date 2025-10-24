@extends("layouts.master")

@section('content-title')
    Database Clear
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Settings / Tools / Database Clear</a></li>
@endsection

@section("content")

    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Database Clearing Tool</h5>
                <span class="d-block m-t-5">Clear all data in the database</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="alert-warning p-3">
                            <h6><i class="fas fa-exclamation-triangle"></i> Warning</h6>
                            <p>This action will permanently delete all data from the database:</p>
                            <ul class="mb-0">
                                <li><strong>All system settings</strong> - Will be reset to default</li>
                                <li><strong>All System</strong> - Users will be removed</li>
                            </ul>
                            <p class="mt-3 mb-0"><strong>This action cannot be undone!</strong> Make sure you have a recent backup before proceeding.</p>
                        </div>

                        <div class="alert-info mt-3 p-3">
                            <h6><i class="fas fa-info-circle"></i> What gets cleared</h6>
                            <p>All transaction data including:</p>
                            <ul class="mb-0">
                                <li>Sales and sales details</li>
                                <li>Purchase orders and invoices</li>
                                <li>Stock and Inventory data</li>
                                <li>Customer and supplier data</li>
                                <li>Products and reports</li>
                                <li>All other operational data</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <h6 class="text-danger mb-3">Clear Database</h6>
                                <p class="text-muted small mb-3">Enter the confirmation password to proceed</p>

                                <form action="{{ route('database-clear.clear') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="password" name="password"
                                               placeholder="Enter password" required>
                                        <small class="form-text text-muted">Contact system administrator for password</small>
                                    </div>

                                    <button type="submit" class="btn btn-danger btn-block">
                                         Clear Database
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-2">Recent Backups</h6>
                                    @if(count($backups) > 0)
                                        <small class="text-muted">Latest: {{ $backups[0]['created_at']->format('M d, Y H:i') }}</small>
                                        <br>
                                        <a href="{{ route('database-backup.download', $backups[0]['name']) }}"
                                           class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    @else
                                        <small class="text-muted">No recent backups found</small>
                                        <br>
                                        <a href="{{ route('database-backup.index') }}"
                                           class="btn btn-sm btn-outline-secondary mt-2">
                                            <i class="fas fa-database"></i> Create Backup
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push("page_scripts")

    <script>
        $(document).ready(function () {
            // Add confirmation dialog
            $('form[action*="database-clear"]').on('submit', function(e) {
                var password = $('#password').val();
                if (!password) {
                    e.preventDefault();
                    alert('Please enter the confirmation password.');
                    return false;
                }

                e.preventDefault();

                // Create and show confirmation modal
                const modalHtml = `
                    <div class="modal fade" id="confirmClearModal" tabindex="-1" role="dialog" aria-labelledby="confirmClearModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmClearModalLabel">
                                        Confirm Database Clear
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger">
                                        <h6><i class="fas fa-exclamation-triangle"></i> Warning!</h6>
                                        <p>Are you absolutely sure you want to clear the database? This action will permanently delete all data and cannot be undone!</p>
                                        <p class="mb-0"><strong>All transaction data, stock, customers, suppliers, and operational records will be removed.</strong></p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger" id="confirmClearBtn">
                                        <i class="fas fa-trash"></i> Clear Database
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('body').append(modalHtml);
                $('#confirmClearModal').modal('show');

                // Handle confirm button click
                $('#confirmClearBtn').on('click', function () {
                    $('#confirmClearModal').modal('hide');

                    // Show loading state
                    var submitBtn = $('form[action*="database-clear"]').find('button[type="submit"]');
                    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Clearing...');

                    // Submit the form
                    $('form[action*="database-clear"]').off('submit').submit();
                });

                // Clean up modal when hidden
                $('#confirmClearModal').on('hidden.bs.modal', function () {
                    $(this).remove();
                });
            });
        });
    </script>

@endpush