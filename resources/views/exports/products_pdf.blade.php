<!DOCTYPE html>
<html>
<head>
    <title>Products List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 8px;
        }
        .page-container {
            position: relative;
            min-height: 100%;
            padding-bottom: 30px; /* Space for footer */
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        tr {
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: left;
            font-size: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 150px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            padding: 5px;
            border-top: 1px solid #ddd;
            background-color: white;
        }
        @page {
            margin: 0.3cm;
            size: landscape;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="header">
            <h2>Products List</h2>
            <p>Generated on: {{ $date }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Pack</th>
                    <th>Category</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Min</th>
                    <th>Max</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $index => $product)
                <tr>
                    <td>{{ ($page - 1) * 100 + $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->brand }}</td>
                    <td>{{ $product->pack_size }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td>{{ $product->type }}</td>
                    <td>{{ $product->status ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $product->min_quantinty }}</td>
                    <td>{{ $product->max_quantinty }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Page {{ $page }} of {{ $total_pages }} | Total Products: {{ $products->count() }}</p>
        </div>
    </div>
</body>
</html> 