@php
    $statuses = [
        1 => ['name' => 'Created', 'class' => 'badge-secondary'],
        2 => ['name' => 'Assigned', 'class' => 'badge-info'],
        3 => ['name' => 'Approved', 'class' => 'badge-warning'],
        4 => ['name' => 'In Transit', 'class' => 'badge-primary'],
        5 => ['name' => 'Acknowledged', 'class' => 'badge-success'],
        6 => ['name' => 'Completed', 'class' => 'badge-dark']
    ];
    
    $currentStatus = $transfer->status ?? 1;
    $statusInfo = $statuses[$currentStatus] ?? ['name' => 'Unknown', 'class' => 'badge-secondary'];
@endphp

<div class="card">
    <div class="card-header">
        <h5 class="card-title">Transfer Status Workflow</h5>
    </div>
    <div class="card-body">
        <!-- Current Status -->
        <div class="row mb-3">
            <div class="col-md-12">
                <strong>Current Status:</strong>
                <span class="badge {{ $statusInfo['class'] }}">{{ $statusInfo['name'] }}</span>
            </div>
        </div>

        <!-- Status Workflow Buttons -->
        <div class="row">
            <div class="col-md-12">
                @if($currentStatus == 1)
                    <!-- Created -> Assigned -->
                    <button type="button" class="btn btn-info btn-sm" onclick="updateStatus({{ $transfer->id }}, 2, 'assign')">
                        <i class="fas fa-user-check"></i> Assign Transfer
                    </button>
                    
                    <!-- Created -> Approved (if user has permission) -->
                    @can('approve_transfers')
                    <button type="button" class="btn btn-warning btn-sm" onclick="updateStatus({{ $transfer->id }}, 3, 'approve')">
                        <i class="fas fa-check-circle"></i> Approve Transfer
                    </button>
                    @endcan
                @endif

                @if($currentStatus == 2)
                    <!-- Assigned -> Approved -->
                    @can('approve_transfers')
                    <button type="button" class="btn btn-warning btn-sm" onclick="updateStatus({{ $transfer->id }}, 3, 'approve')">
                        <i class="fas fa-check-circle"></i> Approve Transfer
                    </button>
                    @endcan
                    
                    <!-- Assigned -> In Transit -->
                    @can('manage_transfers')
                    <button type="button" class="btn btn-primary btn-sm" onclick="updateStatus({{ $transfer->id }}, 4, 'in-transit')">
                        <i class="fas fa-truck"></i> Mark In Transit
                    </button>
                    @endcan
                @endif

                @if($currentStatus == 3)
                    <!-- Approved -> In Transit -->
                    @can('manage_transfers')
                    <button type="button" class="btn btn-primary btn-sm" onclick="updateStatus({{ $transfer->id }}, 4, 'in-transit')">
                        <i class="fas fa-truck"></i> Mark In Transit
                    </button>
                    @endcan
                @endif

                @if($currentStatus == 4)
                    <!-- In Transit -> Acknowledged -->
                    @can('acknowledge_transfers')
                    <button type="button" class="btn btn-success btn-sm" onclick="updateStatus({{ $transfer->id }}, 5, 'acknowledge')">
                        <i class="fas fa-handshake"></i> Acknowledge Receipt
                    </button>
                    @endcan
                @endif

                @if($currentStatus == 5)
                    <!-- Acknowledged -> Completed -->
                    @can('complete_transfers')
                    <button type="button" class="btn btn-dark btn-sm" onclick="updateStatus({{ $transfer->id }}, 6, 'complete')">
                        <i class="fas fa-flag-checkered"></i> Complete Transfer
                    </button>
                    @endcan
                @endif

                @if($currentStatus == 6)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Transfer completed successfully!
                    </div>
                @endif
            </div>
        </div>

        <!-- Status History -->
        <div class="row mt-3">
            <div class="col-md-12">
                <h6>Status History</h6>
                <div class="timeline">
                    @foreach($statuses as $statusId => $status)
                        <div class="timeline-item {{ $statusId <= $currentStatus ? 'active' : 'inactive' }}">
                            <div class="timeline-marker {{ $statusId <= $currentStatus ? $status['class'] : 'badge-light' }}"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ $status['name'] }}</h6>
                                @if($statusId == $currentStatus)
                                    <small class="text-muted">Current Status</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-item.active .timeline-marker {
    background-color: #007bff;
}

.timeline-item.inactive .timeline-marker {
    background-color: #e9ecef;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
}
</style>

<script>
function updateStatus(transferId, newStatus, action) {
    if (!confirm('Are you sure you want to ' + action + ' this transfer?')) {
        return;
    }

    $.ajax({
        url: '/stock-transfer/' + transferId + '/' + action,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: newStatus
        },
        success: function(response) {
            toastr.success(response.message);
            setTimeout(function() {
                location.reload();
            }, 1000);
        },
        error: function(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.error) {
                toastr.error(xhr.responseJSON.error);
            } else {
                toastr.error('An error occurred while updating the status');
            }
        }
    });
}
</script> 