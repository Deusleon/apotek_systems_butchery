@extends("layouts.master")

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Masters / Expense Categories</a></li>
@endsection

@section("content")

    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        @if(auth()->user()->checkPermission('Add Expense Categories'))
                            <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                                data-toggle="modal" data-target="#create">
                                Add Category
                            </button>
                        @endif
                        <div class="table-responsive">
                            <table id="fixed-header" class="display table nowrap table-striped table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        @if(auth()->user()->checkPermission('Edit Expense Categories') || auth()->user()->checkPermission('Delete Expense Categories'))
                                            <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($count > 0)
                                        <tr>
                                            @foreach($expense_categories as $category)
                                                    <td>{{$category->name}}</td>
                                                    @if(auth()->user()->checkPermission('Edit Expense Categories') || auth()->user()->checkPermission('Delete Expense Categories'))
                                                        <td>
                                                            @if(auth()->user()->checkPermission('Edit Expense Categories'))
                                                                <a href="#">
                                                                    <button class="btn btn-sm btn-rounded btn-primary"
                                                                        data-id="{{$category->id}}" data-name="{{$category->name}}"
                                                                        type="button" data-toggle="modal" data-target="#edit">Edit
                                                                    </button>
                                                                </a>
                                                            @endif
                                                            @if(auth()->user()->checkPermission('Delete Expense Categories'))
                                                                @if($category->is_used === "no")
                                                                    <a href="#">
                                                                        <button class="btn btn-sm btn-rounded btn-danger"
                                                                            data-id="{{$category->id}}" data-name="{{$category->name}}"
                                                                            type="button" data-toggle="modal" data-target="#delete">
                                                                            Delete
                                                                        </button>
                                                                    </a>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('masters.expense_category.create')
    @include('masters.expense_category.delete')
    @include('masters.expense_category.edit')
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