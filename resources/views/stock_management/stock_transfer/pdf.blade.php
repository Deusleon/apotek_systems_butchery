<!DOCTYPE html>
<html>
<head>
    <title>Stock Transfer #{{ $transfer->transfer_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }
        .info-box h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .status {
            font-weight: bold;
            color: #2196F3;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .signature-section {
            margin-top: 50px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('fileStore/logo/logo.png') }}" alt="Company Logo" class="logo">
        <div class="title">Stock Transfer Receipt</div>
        <div class="subtitle">Transfer No: {{ $transfer->transfer_no }}</div>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-box">
                <h3>From Store</h3>
                <p>{{ $transfer->from_store_name }}</p>
            </div>
            <div class="info-box">
                <h3>To Store</h3>
                <p>{{ $transfer->to_store_name }}</p>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>Transfer Details</h3>
            <table>
                <tr>
                    <th>Status</th>
                    <td class="status">{{ $transfer->status_text }}</td>
                </tr>
                <tr>
                    <th>Created By</th>
                    <td>{{ $transfer->created_by_name }}</td>
                </tr>
                <tr>
                    <th>Created Date</th>
                    <td>{{ date('d/m/Y H:i', strtotime($transfer->created_at)) }}</td>
                </tr>
                <tr>
                    <th>Last Updated By</th>
                    <td>{{ $transfer->updated_by_name }}</td>
                </tr>
                <tr>
                    <th>Last Updated Date</th>
                    <td>{{ date('d/m/Y H:i', strtotime($transfer->updated_at)) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="info-section">
        <h3>Product Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Brand</th>
                    <th>Pack Size</th>
                    <th>Transfer Quantity</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $transfer->product_name }}</td>
                    <td>{{ $transfer->brand }}</td>
                    <td>{{ $transfer->pack_size }}</td>
                    <td>{{ $transfer->transfer_qty }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($transfer->remarks)
    <div class="info-section">
        <div class="info-box">
            <h3>Remarks</h3>
            <p>{{ $transfer->remarks }}</p>
        </div>
    </div>
    @endif

    @if(count($audit_trail) > 0 && isset($audit_trail[0]->action))
    <div class="info-section">
        <h3>Audit Trail</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                @foreach($audit_trail as $log)
                <tr>
                    <td>{{ date('d/m/Y H:i', strtotime($log->created_at)) }}</td>
                    <td>{{ $log->action ?? 'N/A' }}</td>
                    <td>{{ $log->user_name ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @elseif(count($audit_trail) > 0)
    <div class="info-section">
        <h3>Stock Adjustment History</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reason</th>
                    <th>Adjustment</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($audit_trail as $log)
                <tr>
                    <td>{{ date('d/m/Y H:i', strtotime($log->created_at)) }}</td>
                    <td>{{ $log->reason ?? 'N/A' }}</td>
                    <td>{{ $log->adjustment_quantity ?? 'N/A' }} ({{ $log->adjustment_type ?? 'N/A' }})</td>
                    <td>{{ $log->notes ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Created By</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Approved By</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Received By</div>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer generated document. No signature is required.</p>
        <p>Generated on {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html> 