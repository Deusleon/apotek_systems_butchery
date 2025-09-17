<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Requisition</title>
    <style>
        * {
            font-size: 12px;
        }


        /* table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        } */

    </style>
</head>

<body>
    @php
        ini_set('memory_limit', '-1');
    @endphp
    <h1 align="center">{{ $pharmacy['name'] }}</h1>
    <h3 align="center" style="margin-top: -1%">{{ $pharmacy['address'] }}</h3>
    <h3 align="center" style="margin-top: -1%">{{ $pharmacy['phone'] }}</h3>
    <h3 align="center" style="margin-top: -1%">{{ $pharmacy['email'] . ' | ' . $pharmacy['website'] }}</h3>
    <h3 align="center" style="margin-top: -1%">REQUISITION</h3>

    <table style="width: 100%">
        <tr>
            <td><b>Request No</b></td>
            <td><b>Created By</b></td>
            <td><b>Date</b></td>
        </tr>
        <tr>
            <td>{{ $requisition->req_no }}</td>
            <td>{{ $requisition->creator->name }}</td>
            <td>{{ date('j M, Y', strtotime($requisition->created_at)) }}</td>
        </tr>
    </table>
    <br>
    <table class="table table-striped table-bordered" border="1" style="width: 100%">
        <colgroup>
            <col width="5%">
            <col width="20%">
            <col width="5%">
            <col width="5%">
            <col width="10%">
        </colgroup>
        <thead>
            <tr class="bg-navy disabled">
                <th class="px-1 py-1 text-center">#</th>
                <th align="left">Product</th>
                <th>Unit</th>
                <th class="px-1 py-1 text-center">Qty Req</th>
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
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->products_->name }}</td>
                    <td>{{ $item->unit ?? '--' }}</td>
                    <td align="center">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    {{-- <table style="width: 100%">
        <tr>
            <td><b>Notes:</b></td>
        </tr>
        <tr>
            <td>{{ $requisition->notes }}</td>
        </tr>
    </table>
    <br>
    <table style="width: 100%">
        <tr>
            <td><b>Remarks:</b></td>
        </tr>
        <tr>
            <td>{{ $requisition->remarks ?? '--' }}</td>
        </tr>
    </table>
    <table style="width: 100%">
        <tr>
            <td><b>Status:</b></td>
        </tr>
        <tr>
            @if ($requisition->status == 0)
                <td><span style="background-color:yellow">Pending</span></td>
            @elseif($requisition->status == 1)
                <td><span style="background-color:rgb(78, 145, 78)">Approved</span></td>
            @elseif($requisition->status == 2)
                <td><span style="background-color:rgb(199, 65, 65)">Denied</span></td>
            @endif
        </tr>
    </table> --}}

</body>

</html>
