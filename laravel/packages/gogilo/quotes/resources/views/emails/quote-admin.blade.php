<!DOCTYPE html>
<html>

<head>
    <title>New Quote Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 1024px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 30px;
            background: #f9f9f9;
        }

        .button {
            display: inline-block;
            background: #4f46e5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }

        .info-box {
            background: white;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>New Quote Request</h1>
        </div>

        <div class="content">
            <p>A new quote request has been submitted through the website.</p>

            <div class="info-box">
                <h3>Quote Details:</h3>
                <p><strong>Code:</strong> {{ $quote->code }}</p>
                <p><strong>Customer:</strong> {{ $quote->name }}</p>
                <p><strong>Email:</strong> {{ $quote->email }}</p>
                <p><strong>Phone:</strong> {{ $quote->phone ?? 'N/A' }}</p>
                <p><strong>Company:</strong> {{ $quote->company ?? 'N/A' }}</p>
                <p><strong>Submitted:</strong> {{ $quote->created_at->format('F d, Y H:i') }}</p>
            </div>

            @if ($quote->products->count() > 0)
                <div class="info-box">
                    <h3>Here are Requested Products ({{ $quote->products->count() }})</h3>
                    <ul>
                        @foreach ($quote->products as $product)
                            <li>{{ $product->title }} (Qty: {{ $product->pivot->quantity }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($quote->message)
                <div class="info-box">
                    <h3>Customer Message:</h3>
                    <p>{{ $quote->message }}</p>
                </div>
            @endif

            <p>
                <a href="{{ route('dashboard-quotes', ['search' => $quote->code]) }}" class="button">
                    View Full Quote Details
                </a>
            </p>

            <p>You can also review this quote in the admin panel.</p>
        </div>
    </div>
</body>

</html>
