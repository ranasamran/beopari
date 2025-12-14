<!DOCTYPE html>
<html>
<head>
    <title>Order #{{ $order->id }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; }
    </style>
</head>
<body>
    <h2>Order #{{ $order->id }}</h2>
    <p><strong>Customer:</strong> {{ $order->customer }}</p>
    <p><strong>Date:</strong> {{ $order->created_at->format('Y-m-d') }}</p>
    <p><strong>Total Paid:</strong> {{ $order->total_paid }}</p>
    <h3>Order Details</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
        @foreach($order->orderDetails as $detail)
            <tr>
                <td>{{ $detail->name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>{{ $detail->price }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html> 