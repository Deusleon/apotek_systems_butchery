@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1>Edit Stock Count Schedule</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    {!! Form::model($stockCountSchedule, ['route' => ['stock-count-schedules.update', $stockCountSchedule->id], 'method' => 'patch'])
                     !!}

                    <div class="row">
                        <!-- Schedule Date Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('schedule_date', 'Schedule Date:') !!}
                            {!! Form::date('schedule_date', null, ['class' => 'form-control','id'=>'schedule_date'])
                             !!}
                        </div>

                        <!-- Store Id Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('store_id', 'Store:') !!}
                            {!! Form::select('store_id', $stores, null, ['class' => 'form-control custom-select'])
                             !!}
                        </div>

                        <!-- Status Field -->
                        <div class="form-group col-sm-6">
                            {!! Form::label('status', 'Status:') !!}
                            {!! Form::select('status', ['pending' => 'Pending', 'completed' => 'Completed', 'cancelled' => 'Cancelled'], null, ['class' => 'form-control custom-select'])
                             !!}
                        </div>

                        <!-- Notes Field -->
                        <div class="form-group col-sm-12">
                            {!! Form::label('notes', 'Notes:') !!}
                            {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>

                    <!-- Submit Field -->
                    <div class="card-footer">
                        {!! Form::submit('Save', ['class' => 'btn btn-primary'])
                         !!}
                        <a href="{{ route('stock-count-schedules.index') }}" class="btn btn-default">Cancel</a>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection 