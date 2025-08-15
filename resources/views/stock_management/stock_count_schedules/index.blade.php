@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Stock Count Schedules</h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary float-right"
                       href="{{ route('stock-count-schedules.create') }}">
                        Add New Schedule
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped" id="stockCountSchedulesTable">
                        <thead>
                            <tr>
                                <th>Schedule Date</th>
                                <th>Store</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</td>
                                <td>{{ $schedule->store->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($schedule->status) }}</td>
                                <td>{{ $schedule->notes ?? 'N/A' }}</td>
                                <td>{{ $schedule->creator->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <div class='btn-group'>
                                        <a href="{{ route('stock-count-schedules.edit', [$schedule->id]) }}"
                                           class='btn btn-default btn-xs'>
                                            <i class="far fa-edit"></i>
                                        </a>
                                        @if(auth()->user()->can('approve stock count schedules') && $schedule->status == 'pending')
                                            {!! Form::open(['route' => ['stock-count-schedules.approve', $schedule->id], 'method' => 'post'])
                                            !!}
                                            {!! Form::button('<i class="far fa-check-circle"></i>', ['type' => 'submit', 'class' => 'btn btn-success btn-xs', 'onclick' => "return confirm('Are you sure you want to approve this schedule?')"])
                                            !!}
                                            {!! Form::close() !!}
                                        @endif
                                        {!! Form::open(['route' => ['stock-count-schedules.destroy', $schedule->id], 'method' => 'delete'])
                                        !!}
                                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"])
                                        !!}
                                        {!! Form::close() !!}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection 