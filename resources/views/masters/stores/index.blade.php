@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Branches
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Settings / General / Branches </a></li>
@endsection

@section("content")

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    @if(auth()->user()->checkPermission('Add Branches'))
                        <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                            data-toggle="modal" data-target="#create">
                            Add Branch
                        </button>
                    @endif

                    <div class="table-responsive">
                        <table id="fixed-header" class="display table nowrap table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    @if(auth()->user()->checkPermission('Edit Branches') || auth()->user()->checkPermission('Delete Branches'))
                                        <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if($count > 0)
                                    <tr>

                                        @foreach($stores as $store)
                                                <td>{{$store->name}}</td>
                                                @if(auth()->user()->checkPermission('Edit Branches') || auth()->user()->checkPermission('Delete Branches'))
                                                    <td>
                                                        @if(auth()->user()->checkPermission('Edit Branches'))
                                                            <a href="#">
                                                                <button class="btn btn-sm btn-rounded btn-primary" data-id="{{$store->id}}"
                                                                    data-name="{{$store->name}}" type="button" data-toggle="modal"
                                                                    data-target="#edit">Edit
                                                                </button>
                                                            </a>
                                                        @endif
                                                        @if($store->id != $defaultStoreId)
                                                            @if(auth()->user()->checkPermission('Delete Branches'))
                                                                @if($store->is_used === 'no')
                                                                    <a href="#">
                                                                        <button class="btn btn-sm btn-rounded btn-danger" data-id="{{$store->id}}"
                                                                            data-name="{{$store->name}}" type="button" data-toggle="modal"
                                                                            data-target="#delete">
                                                                            Delete
                                                                        </button>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- [ configuration table ] end -->
                </div>
            </div>
        </div>
    </div>
    @include('masters.stores.create')
    @include('masters.stores.delete')
    @include('masters.stores.edit')

@endsection

@push("page_scripts")
    @include('partials.notification')

    <script>

        $('#edit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body #id_edit').val(button.data('id'));
            modal.find('.modal-body #name_edit').val(button.data('name'))
        });

        $('#delete').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);

            var message = "Are you sure you want to delete '".concat(button.data('name'), "'?");
            var modal = $(this);

            modal.find('.modal-body #message').text(message);
            modal.find('.modal-body #id').val(button.data('id'))
        });


    </script>
@endpush