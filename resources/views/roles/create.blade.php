@extends("layouts.master")


@section('page_css')
@endsection

@section('content-title')
   New Role

@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> User Management / Roles / Add Role </a> </li>
@endsection



@section("content")

<div class="col-sm-12">

<div class="card">
    <div class="card-body">
        <form action="{{route('roles.store')}}" method="post">
            @csrf()
        <div class="form-group row">
                <label style="text-align: right;" class="col-md-2 col-form-label text-md-right">Role:<font color="red">*</font> </label>
                <div class="col-md-8">
                        <input id="role" type="text" class="form-control" name="name" value="{{old('name')}}" required autofocus>

                </div>
        </div>
        <div class="form-group row">
            <label style="text-align: right;" class="col-md-2 col-form-label text-md-right">Description:<font color="red">*</font> </label>
            <div class="col-md-8">
                    <input id="description" type="text" class="form-control" name="description" value="{{old('description')}}" required>
            </div>
        </div>
        <div class="form-group row">
            <label style="text-align: right;" class="col-md-2 col-form-label text-md-right">Permissions:<font color="red">*</font> </label>
            <div class="col-md-10">
                    <div class="form-group row">
                            <div class="col-sm-2">
                                    <div class="checkbox checkbox-fill d-inline">
                                        <input type="checkbox" id="check_all">
                                        <label for="check_all" class="cr">Check All</label>
                                    </div>
                            </div>
                    </div>

                <div class="form-group row">
                    @foreach ($permissions as $permission)
                        <div class="col-sm-4">
                                <div class="checkbox checkbox-fill d-inline">
                                    <input type="checkbox" name="permissions[]" id="{{$permission->id}}" value="{{$permission->id}}">
                                    <label for="{{$permission->id}}" class="cr"> {{$permission->name}}</label>
                                </div>
                        </div>
                    @endforeach

                </div>
                <hr>
                <div class="form-group row">
                        <div class="col-sm-8"></div>
                        <a href="{{route('roles.index')}}">
                                <button type="button" class="btn btn-danger">Back</button>
                        </a>
                        <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </div>
        </div>

        </form>

    </div> {{-- /Card-Body --}}

</div>
</div>

@endsection

@push("page_scripts")
@include('partials.notification')

<script>

$(document).ready(function() {

    $('#check_all').click(function() {
            var c = this.checked;
            $(':checkbox').prop('checked',c);
    });



});

</script>

@endpush
