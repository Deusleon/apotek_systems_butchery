@extends("layouts.master")

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Masters / Expense Subcategories</a></li>

@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card-block">
            <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                                data-toggle="modal"
                                data-target="#create">
                            Add Expense Subcategory
                        </button>

                        <div class="table-responsive">
                            <table id="fixed-header" class="display table nowrap table-striped table-hover"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th>Expense Category</th>
                                    <th>Expense Subcategory</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    @foreach($expense_subcategories as $subcategory)
                                        <td>{{$subcategory->expenseCategory['name']}}</td>
                                        <td>{{$subcategory->name}}</td>

                                        <td>
                                            <a href="#">
                                                <button class="btn btn-sm btn-rounded btn-info"
                                                        data-id="{{$subcategory->id}}"
                                                        data-category_name="{{$subcategory->expense_category_id}}"
                                                        data-subcategory_name="{{$subcategory->name}}"
                                                        type="button"
                                                        data-toggle="modal" data-target="#edit">Edit
                                                </button>
                                            </a>
                                            <a href="#">
                                                <button class="btn btn-sm btn-rounded btn-danger"
                                                        data-id="{{$subcategory->id}}"
                                                        data-subcategory_name="{{$subcategory->name}}" type="button"
                                                        data-toggle="modal"
                                                        data-target="#delete"> Delete
                                                </button>
                                            </a>
                                        </td>
                                </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('masters.expense_subcategory.create')
    @include('masters.expense_subcategory.delete')
    @include('masters.expense_subcategory.edit')

@endsection

@push("page_scripts")
    @include('partials.notification')

    <script>

        $('#edit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body #id_edit').val(button.data('id'));
            modal.find('.modal-body #category_id').val(button.data('category_name'));
            modal.find('.modal-body #subcategory_name').val(button.data('subcategory_name'))
        });

        $('#delete').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);

            var message = "Are you sure you want to delete '".concat(button.data('subcategory_name'), "'?");
            var modal = $(this);

            modal.find('.modal-body #message').text(message);
            modal.find('.modal-body #id_edit').val(button.data('id'))
        });


    </script>
@endpush
