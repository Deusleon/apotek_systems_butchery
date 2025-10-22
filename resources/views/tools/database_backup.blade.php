@extends("layouts.master")

@section('content-title')
    Database Backup
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Settings / Tools / Database Backup</a></li>
@endsection

@section("content")

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-end align-items-end mb-3">
                    <div class="col-md-6 text-right pr-1">
                        <button type="button" class="btn btn-secondary btn-sm mr-2" data-toggle="modal" data-target="#createBackupModal">
                            Create New Backup
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="backup-table" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Backup Name</th>
                                <th>Size</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $backup)
                                <tr>
                                    <td>{{ $backup['name'] }}</td>
                                    <td>{{ $backup['size_human'] }}</td>
                                    <td>{{ $backup['created_at']->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <a href="{{ route('database-backup.download', $backup['name']) }}" class="btn btn-success btn-sm btn-rounded">
                                            Download
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm btn-rounded" onclick="confirmDelete('{{ $backup['name'] }}')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No backups found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Backup Modal -->
    <div class="modal fade" id="createBackupModal" tabindex="-1" role="dialog" aria-labelledby="createBackupModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBackupModalLabel">
                       Create Database Backup
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('database-backup.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to create a new database backup? This process may take a few moments depending on the database size.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> The backup will be saved in the storage/backups directory and can be downloaded later.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        Delete Backup
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to delete this backup? This action cannot be undone.</p>
                        <p class="text-danger"><strong>Backup file: <span id="deleteFilename"></span></strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                           Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push("page_scripts")

    <script>

        $(document).ready(function () {
            // Initialize DataTable
            $('#backup-table').DataTable({
                "pageLength": 10,
                "order": [[2, 'desc']], // Sort by created date descending
                "drawCallback": function (settings) {
                    // Fix colspan for empty table message
                    var api = this.api();
                    var columnsCount = api.columns().header().length;

                    // Update empty table message colspan
                    $('#backup-table tbody tr td.dataTables_empty').attr('colspan', columnsCount);
                },
            });
        });

        function confirmDelete(filename) {
            document.getElementById('deleteForm').action = '{{ url("settings/tools/database-backup") }}/' + filename;
            document.getElementById('deleteFilename').textContent = filename;
            $('#deleteModal').modal('show');
        }

    </script>

@endpush