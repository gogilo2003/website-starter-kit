<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Quote {{ $quote->code }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .company-info {
            margin-bottom: 30px;
        }

        .customer-info {
            margin-bottom: 30px;
            background: #f9f9f9;
            padding: 15px;
        }

        .customer-info p {
            padding: 0;
            margin: 0 0 1rem 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .table th {
            background: #f5f5f5;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-sent {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .logo {
            display: block;
            /* max-width: 128px; */
            max-height: 128px;
            object-fit: contain;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="header">
        <img class="logo" src="{{ \App\Support\Util::fileBase64('images/letterhead.png') }}" />
        <h1>QUOTE #{{ $quote->code }}</h1>
        <p>Date: {{ $date }}</p>
        <span class="status-badge status-{{ $quote->status }}">
            {{ strtoupper($quote->status) }}
        </span>
    </div>

    <div class="customer-info">
        <h3>Customer Information</h3>
        <p><strong>Name:</strong> {{ $quote->name }}</p>
        <p><strong>Email:</strong> {{ $quote->email }}</p>
        @if ($quote->phone)
            <p><strong>Phone:</strong> {{ $quote->phone }}</p>
        @endif
        @if ($quote->company)
            <p><strong>Company:</strong> {{ $quote->company }}</p>
        @endif
        @if ($quote->message)
            <p><strong>Message:</strong> {{ $quote->message }}</p>
        @endif
    </div>

    <h3>Quote Items</h3>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quote->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->title ?? 'Product' }}</strong>
                        @if ($item->product->summary ?? false)
                            <br><small>{{ $item->product->summary }}</small>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>KES {{ number_format($item->price, 2) }}</td>
                    <td>KES {{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <h3>Total Amount: KES{{ number_format($quote->total_amount, 2) }}</h3>
    </div>

    @if ($quote->message)
        <div class="notes">
            <h3>Additional Notes</h3>
            <p>{{ $quote->message }}</p>
        </div>
    @endif

    <div class="footer">
        <p>This quote is valid for 30 days from the date of issue.</p>
        <p>For any inquiries, please contact us at {{ config('email.contact', 'quote@youngolive.co.ke') }}</p>
        <p>Quote Tracking Code: {{ $quote->code }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>

</html>
