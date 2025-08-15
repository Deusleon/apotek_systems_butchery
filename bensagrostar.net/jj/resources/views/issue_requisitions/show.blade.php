@extends("layouts.master")

@section('content-title')
    Issue Requisition
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Stores / Requisitions / Requisition Details</a></li>
@endsection

@section('content')

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <form action="{{ route('requisitions.issuing') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="user">Requisition No</label>
                                    <h6>{{ $requisition->req_no }}</h6>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="user">Created By</label>
                                    <h6>{{ $requisition->creator->name }}</h6>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="user">Date Created</label>
                                    <h6>{{ date('j M, Y', strtotime($requisition->created_at)) }}</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="user">From Store</label>
                                    <h6>{{ $fromStore->name }}</h6>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="user">To Store</label>
                                    <h6>{{ $toStore->name }}</h6>
                                </div>
                            </div>
                            <input type="hidden" name="store_id" value="{{ $toStore->id }}">
                            <input type="hidden" name="requisition_id" value="{{ $requisition->id }}">
                            <div class="table-responsive">
                                <table class="table nowrap table-striped table-hover" id="order_table">
                                    <thead>
                                        <tr class="bg-navy disabled">
                                            <th>Product</th>
                                            <th class="text-center">Unit</th>
                                            <th class="text-center">Qty OH</th>
                                            <th class="text-center">Qty Req</th>
                                            <th class="text-center">Qty Given</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            if ($requisition->status != 0) {
                                                $disable = 'readonly';
                                            } else {
                                                $disable = '';
                                            }
                                        @endphp
                                        @foreach ($requisitionDet as $item)
                                            {{-- @if ($item->quantity_given < $item->quantity) --}}
                                                @php
                                                    $available = '1';
                                                @endphp
                                                <tr>
                                                    <td style="display: none"><input type="text" name="product_id[]"
                                                            value="{{ $item->products_->id }}"></td>
                                                    <td class=" border-0">{{ $item->products_->name }}</td>
                                                    <td class="text-center border-0">{{ $item->unit }}</td>
                                                    <td class="text-center border-0">{{ number_format($item->qty_oh) }}
                                                    </td>
                                                    <td class="text-center border-0">
                                                        <input style="display: none" type="text" name="qty_req[]"
                                                            value="{{ $item->quantity }}">{{ $item->quantity }}
                                                    </td>
                                                    <td class="text-center border-0"><input class="text-center "
                                                            type="number" name="qty[]"
                                                            value="{{ $item->quantity_given ?? $item->quantity }}"
                                                            {{ $disable }} required /></td>
                                                </tr>
                                            {{-- @endif --}}
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                {{-- <div class="form-group col-md-12">
                                    <label for="products">Notes:</label>
                                    <h6>{{ $requisition->notes ?? '--' }}</h6>
                                </div> --}}
                                {{-- <div class="form-group col-md-12">
                                    <label for="products">Status:</label> <br>
                                    @if ($requisition->status == 0)
                                        <span class="badge badge-secondary p-1">Pending</span>
                                    @elseif($requisition->status == 1)
                                        <span class="badge badge-success p-1">Approved</span>
                                    @elseif($requisition->status == 2)
                                        <span class="badge badge-danger p-1">Denied</span>
                                    @endif
                                </div> --}}


                                <div class="form-group col-md-12">
                                    <label for="products">Remarks:</label>
                                    @if (!$disable)
                                        <textarea class="form-control" name="remarks" id="" rows="2"></textarea>
                                    @else
                                        <h6>{{ $requisition->remarks ?? '--' }}</h6>
                                    @endif

                                </div>
                            </div>
                            <div class="modal-footer">
                                @can('Approve Requisitions', Model::class)
                                    @if (!$disable)
                                        <button type="submit" class="btn btn-primary" id='submit_btn'>Issue</button>
                                    @endif
                                @endcan
                        </form>
                        <a href="{{ route('issue.index') }}" class="btn btn-danger">Close</a>
                        @can('Delete Requisitionss', Model::class)
                            @if (!$disable)
                                <form action="{{ route('requisitions.delete') }}" method="post">
                                    @csrf
                                    @method("DELETE")
                                    <input type="hidden" name="req_id" value="{{ $requisition->id }}">
                                    <button type="submit" name="save" class="btn btn-warning">Delete</button>
                                </form>
                            @endif
                        @endcan

                        {{-- @can('Print Requisitions', Model::class)
                            <form action="{{ route('print-requisitions') }}" method="GET" target="_blank">
                                <input type="hidden" name="req_id" value="{{ $requisition->id }}">
                                <button type="submit" name="save" class="btn btn-secondary">Print</button>
                            </form>
                        @endcan --}}
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

        if (rowCount == 1) {
            $("#submit_btn").attr("disabled", true);
        } else {
            $("#submit_btn").removeAttr("disabled");
        }
    </script>
@endpush
