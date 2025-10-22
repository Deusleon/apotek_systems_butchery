@extends("layouts.master")

@section('content-title')
    Stock Issue
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Inventory / Stock Issue / Issue Details</a></li>
@endsection

@section('content')

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                 <!-- TAB LIST - FIXED -->
                <ul class="nav nav-pills mb-3" id="issueTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active text-uppercase" id="issue-details-tab" data-toggle="pill"
                        href="#issue-details" role="tab" aria-controls="issue-details" aria-selected="true">Issue Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" id="issue-list-tab" href="{{ route('issue.index') }}" 
                        role="tab" aria-controls="issue-list" aria-selected="false">Issue List</a>
                    </li>
                </ul>
                <div class="tab-content" id="issueTabContent">
                    <div class="tab-pane fade show active" id="issue-details" role="tabpanel" aria-labelledby="issue-details-tab">
                        <form action="{{ route('requisitions.issuing') }}" method="post" id="issueForm">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="user">Requisition #</label>
                                    <h6>{{ $requisition->req_no }}</h6>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="user">Created By</label>
                                    <h6>{{ $requisition->creator->name }}</h6>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="user">Date Created</label>
                                    <h6>{{ date('Y-m-d', strtotime($requisition->created_at)) }}</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="user">From Branch</label>
                                    <h6>{{ $fromStore->name }}</h6>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="user">To Branch</label>
                                    <h6>{{ $toStore->name }}</h6>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="evidence_document">View Evidence:</label>
                                        @if($requisition->evidence_document)
                                            <div class="d-flex align-items-center gap-3">
                                                <a href="{{ Storage::url($requisition->evidence_document) }}" target="_blank" 
                                                class="btn btn-warning text-body">
                                                 View
                                                </a>
                                            </div>
                                        @else
                                            <h6 class="text-muted">No document attached.</h6>
                                        @endif
                                </div>
                            </div>
                            <input type="hidden" name="store_id" value="{{ $toStore->id }}">
                            <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">
                            
                            <div class="table-responsive">
                                <table class="table nowrap table-striped table-hover" id="order_table">
                                    <thead>
                                        <tr class="bg-navy disabled">
                                            <th>Product Name</th>
                                            <th class="text-center">QOH</th>
                                            <th class="text-center">Qty Req</th>
                                            <th class="text-center">Qty Issued</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            if ($requisition->status != 0) {
                                                $disable = 'readonly';
                                                $isEditable = false;
                                            } else {
                                                $disable = '';
                                                $isEditable = true;
                                            }
                                        @endphp
                                        @foreach ($requisitionDet as $index => $item)
                                            <tr>
                                                <td style="display: none"><input type="text" name="product_id[]"
                                                        value="{{ $item->products_->id }}"></td>
                                                <td class="border-0">{{ $item->products_->full_product_name ?? $item->products_->name }}</td>
                                                <td class="text-center border-0 qoh-cell" data-qoh="{{ $item->qty_oh }}">{{ number_format($item->qty_oh) }}</td>
                                                <td class="text-center border-0">
                                                    <input class="form-control text-center" style="display: none" type="text" name="qty_req[]"
                                                        value="{{ $item->quantity }}">{{ number_format($item->quantity) }}
                                                </td>
                                                <td class="text-center border-0 qty-issued-cell" data-index="{{ $index }}">
                                                    <!-- Display as text initially -->
                                                    <span class="qty-text">{{ number_format($item->quantity_given ?? $item->quantity) }}</span>
                                                    <!-- Input field (hidden initially) -->
                                                    <input class="form-control text-center qty-input" type="text" name="qty[]"
                                                        value="{{ $item->quantity_given ?? $item->quantity }}"
                                                        style="display: none;" {{ $disable }} required />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-3">
                                <div class="form-group col-md-12">
                                    <label for="products">Remarks:</label>
                                    <textarea class="form-control" name="remarks" id="remarksTextarea" rows="2">{{ $requisition->remarks }}</textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                @can('Approve Requisitions', Model::class)
                                    @if (!$disable)
                                        <!-- Edit and Issue buttons side by side -->
                                        <div>
                                            <!-- Edit button to toggle editing mode -->
                                            <button type="button" class="btn btn-warning" id="editBtn">Edit</button>
                                            <!-- Submit button -->
                                            <button type="submit" class="btn btn-primary" id="submitBtn">Issue</button>
                                        </div>
                                    @endif
                                @endcan
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection

@push('page_scripts')
    @include('partials.notification')

    <script>
        var rowCount = $('#order_table tr').length;
        var isEditing = false;

        if (rowCount == 1) {
            $("#submitBtn").attr("disabled", true);
        } else {
            $("#submitBtn").removeAttr("disabled");
        }
        
        // Add click handler for the Issue List tab
        document.getElementById('issue-list-tab').addEventListener('click', function(e) {
            window.location.href = this.getAttribute('href');
        });

        // Function to check QOH vs Qty Issued and show alert
        function checkQOHValidation() {
            let hasAlert = false;
            document.querySelectorAll('.qty-issued-cell').forEach(function(cell, index) {
                const qohCell = document.querySelectorAll('.qoh-cell')[index];
                const qoh = parseFloat(qohCell ? qohCell.getAttribute('data-qoh') : 0);
                const qtyIssued = parseFloat(cell.querySelector('.qty-input').value || cell.querySelector('.qty-text').textContent.replace(/,/g, ''));
                if (qtyIssued > qoh) {
                    const productName = cell.closest('tr').querySelector('td.border-0').textContent.trim();
                    alert('Qty Issued cannot exceed QOH for product: ' + productName);
                    hasAlert = true;
                }
            });
            return hasAlert;
        }

        // Edit button functionality
        document.getElementById('editBtn').addEventListener('click', function() {
            if (!isEditing) {
                // Switch to edit mode
                this.textContent = 'Cancel';
                this.classList.remove('btn-warning');
                this.classList.add('btn-secondary');

                // Switch quantity display to input fields
                document.querySelectorAll('.qty-text').forEach(function(el) {
                    el.style.display = 'none';
                });

                document.querySelectorAll('.qty-input').forEach(function(el) {
                    el.style.display = 'block';
                });

                isEditing = true;
            } else {
                // Cancel edit mode
                this.textContent = 'Edit';
                this.classList.remove('btn-secondary');
                this.classList.add('btn-warning');

                // Switch input fields back to text display
                document.querySelectorAll('.qty-text').forEach(function(el) {
                    el.style.display = 'inline';
                });

                document.querySelectorAll('.qty-input').forEach(function(el) {
                    el.style.display = 'none';
                });

                isEditing = false;
            }
        });

        // Add validation on form submit
        document.getElementById('issueForm').addEventListener('submit', function(e) {
            if (checkQOHValidation()) {
                e.preventDefault();
            }
        });
    </script>
@endpush