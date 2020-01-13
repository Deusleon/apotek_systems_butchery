@extends("layouts.master")

@section('content-title')
    Adjustment Reasons
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Masters / Adjustment Reasons</a></li>
@endsection

@section("content")

    <div class="col-sm-12">

        <div class="card-block">
            <div class="col-sm-12">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        @can('Manage Adjustment Reasons')
                            <button style="float: right;margin-bottom: 2%;" type="button"
                                    class="btn btn-secondary btn-sm"
                                    data-toggle="modal"
                                    data-target="#exampleModalCenter">
                                Add Reason
                            </button>
                        @endcan
                        <div class="table-responsive">
                            <table id="fixed-header" class="display table nowrap table-striped table-hover"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th>Reason</th>
                                    @can('Manage Adjustment Reasons')
                                        <th>Actions</th>
                                    @endcan
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($adjustment as $adjustments)
                                    <tr>
                                        <td>{{$adjustments->reason}}</td>
                                        @can('Manage Adjustment Reasons')
                                            <td>
                                                <a href="#">
                                                    <button class="btn btn-sm btn-rounded btn-primary"
                                                            data-id="{{$adjustments->id}}"
                                                            data-name="{{$adjustments->reason}}"
                                                            type="button"
                                                            data-toggle="modal" data-target="#edit">Edit
                                                    </button>
                                                </a>
                                                <a href="#">
                                                    <button class="btn btn-sm btn-rounded btn-danger"
                                                            data-id="{{$adjustments->id}}"
                                                            data-name="{{$adjustments->reason}}" type="button"
                                                            data-toggle="modal"
                                                            data-target="#delete">
                                                        Delete
                                                    </button>
                                                </a>
                                            </td>
                                        @endcan
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

    @include('masters.adjustment_reason.create')
    @include('masters.adjustment_reason.delete')
    @include('masters.adjustment_reason.edit')

@endsection

@push("page_scripts")

    @include('partials.notification')


    <script>

        $('#edit').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body #price_category_id').val(button.data('id'));
            modal.find('.modal-body #name_edit').val(button.data('name'));
            modal.find('.modal-body #code_edit').val(button.data('code'));
            modal.find('.modal-body #status_edit').val(button.data('status'))

        });

        $('#delete').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);

            var message = "Are you sure you want to delete '".concat(button.data('name'), "'?");
            var modal = $(this);

            modal.find('.modal-body #message').text(message);
            modal.find('.modal-body #price_category_id').val(button.data('id'))
        });


    </script>
@endpush
