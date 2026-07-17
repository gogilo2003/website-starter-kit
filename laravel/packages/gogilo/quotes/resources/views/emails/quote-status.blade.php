<!DOCTYPE html>
<html>

<head>
    <title>Quote Status Updated</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1024px;
            margin: 0 auto;
            background: #ffffff;
        }

        .header {
            background: #A3D52F;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .content {
            padding: 40px;
            background: #f9f9f9;
        }

        .code {
            display: inline-block;
            background: #EC8F3C;
            color: white;
            padding: 12px 24px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 8px;
            margin: 15px 0;
            letter-spacing: 1px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            margin: 5px 0;
        }

        .status-viewed {
            background: #3498db;
            color: white;
        }

        .status-pending {
            background: #f39c12;
            color: white;
        }

        .status-accepted {
            background: #2ecc71;
            color: white;
        }

        .status-rejected {
            background: #e74c3c;
            color: white;
        }

        .status-draft {
            background: #95a5a6;
            color: white;
        }

        .status-completed {
            background: #9b59b6;
            color: white;
        }

        .button {
            display: inline-block;
            background: #A3D52F;
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
            font-size: 16px;
        }

        .button:hover {
            background: #81AA22;
        }

        .attachment-notice {
            background: #e8f4fc;
            border: 2px dashed #3498db;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }

        .attachment-icon {
            font-size: 48px;
            color: #3498db;
            margin-bottom: 15px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            background: #f5f5f5;
        }

        .info-box {
            background: white;
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #A3D52F;
        }

        .quote-items {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .quote-items th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #e0e0e0;
            font-weight: bold;
        }

        .quote-items td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        .quote-items tr:last-child td {
            border-bottom: none;
        }

        ul {
            padding-left: 20px;
        }

        li {
            margin-bottom: 8px;
        }

        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Quote Status Updated</h1>
        </div>

        <div class="content">
            <p>Dear {{ $quote->name }},</p>

            <p>The status of your quote has been updated. Here are the latest details:</p>

            <div class="info-box">
                <p><strong>Your Quote Tracking Code:</strong></p>
                <div class="code">{{ $quote->code }}</div>

                <p><strong>New Status:</strong></p>
                @php
                    $statusClass = 'status-' . strtolower($quote->status);
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ ucfirst($quote->status) }}</span>

                @if ($quote->last_viewed_at)
                    <p><strong>Last Viewed:</strong>
                        {{ \Carbon\Carbon::parse($quote->last_viewed_at)->format('F d, Y \a\t H:i') }}</p>
                @endif
            </div>

            @if ($hasPdfAttachment)
                <div class="attachment-notice">
                    <div class="attachment-icon">📎</div>
                    <h3>Quote PDF Attached</h3>
                    <p>A detailed PDF version of your quote is attached to this email.</p>
                    <p>You can download the PDF file: <a
                            href="{{ route('quote-download', $quote->code) }}"><strong>Quote-{{ $quote->code }}.pdf</strong></a>
                    </p>
                </div>
            @endif

            <p><strong>Quote Details:</strong></p>
            <div class="info-box">
                <ul>
                    <li><strong>Name:</strong> {{ $quote->name }}</li>
                    <li><strong>Email:</strong> {{ $quote->email }}</li>
                    @if ($quote->phone)
                        <li><strong>Phone:</strong> {{ $quote->phone }}</li>
                    @endif
                    @if ($quote->company)
                        <li><strong>Company:</strong> {{ $quote->company }}</li>
                    @endif
                    <li><strong>Date Submitted:</strong>
                        {{ \Carbon\Carbon::parse($quote->created_at)->format('F d, Y \a\t H:i') }}</li>
                    <li><strong>Total Amount:</strong>
                        <span class="total-amount">KES {{ number_format($quote->total_amount, 2) }}</span>
                    </li>
                </ul>
            </div>

            @if ($quote->items && count($quote->items) > 0)
                <p><strong>Requested Items:</strong></p>
                <div class="info-box">
                    <table class="quote-items">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quote->items as $item)
                                <tr>
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
                            <tr>
                                <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                                <td><strong>KES {{ number_format($quote->total_amount, 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($quote->message)
                <p><strong>Your Message:</strong></p>
                <div class="info-box">
                    <p>{{ $quote->message }}</p>
                </div>
            @endif

            <p><strong>What does this status mean?</strong></p>
            <div class="info-box">
                @switch(strtolower($quote->status))
                    @case('viewed')
                        <p>Our team has reviewed your quote request and is currently preparing your quotation. You should
                            receive the detailed quote shortly.</p>
                    @break

                    @case('pending')
                        <p>Your quote is currently being processed. Our team is working on preparing the best offer for your
                            requirements.</p>
                    @break

                    @case('accepted')
                        <p>Your quote has been accepted! Please proceed with the next steps as outlined in the quotation
                            document.</p>
                    @break

                    @case('rejected')
                        <p>Your quote has been reviewed but wasn't accepted at this time. Please contact us for more information
                            or to discuss alternatives.</p>
                    @break

                    @case('draft')
                        <p>Your quote is currently in draft status. We are still preparing the final details.</p>
                    @break

                    @case('completed')
                        <p>Your quote has been completed to an order! A detailed PDF quote is attached to this email. Please
                            review the attached document for complete details.</p>
                    @break

                    @default
                        <p>Your quote status has been updated. Please check the quote details above for more information.</p>
                @endswitch
            </div>

            @if ($hasPdfAttachment)
                <p><strong>Important:</strong> Please review the attached PDF quote document for complete details
                    including terms and conditions.</p>
            @endif

            <p>You can track the status of your quote anytime using your tracking code:
                <strong>{{ $quote->code }}</strong>
            </p>

            <p>If you have any questions about this status update or need further assistance, please don't hesitate to
                reply to this email.</p>

            <p>Best regards,<br>
                <strong>{{ config('app.name') }} Team</strong>
            </p>
        </div>

        <div class="footer">
            <p>This email was sent automatically. Please do not reply to this email.</p>
            <p>If you need assistance, contact us at {{ config('mail.from.address') }}</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
