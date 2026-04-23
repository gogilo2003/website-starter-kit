<!DOCTYPE html>
<html>

<head>
    <title>Quote Request Confirmation</title>
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
            /* Your primary color */
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
            /* Your secondary color */
            color: white;
            padding: 12px 24px;
            font-size: 20px;
            font-weight: bold;
            border-radius: 8px;
            margin: 15px 0;
            letter-spacing: 1px;
        }

        .button {
            display: inline-block;
            background: #A3D52F;
            /* Your primary color */
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
            /* Your primary-600 */
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

        ul {
            padding-left: 20px;
        }

        li {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Quote Request Received</h1>
        </div>

        <div class="content">
            <p>Dear {{ $quote->name }},</p>

            <p>Thank you for your quote request. We have received your request and will process it shortly.</p>

            <div class="info-box">
                <p><strong>Your Quote Tracking Code:</strong></p>
                <div class="code">{{ $quote->code }}</div>

                <p><strong>Quote Status:</strong> <span
                        style="color: #EC8F3C; font-weight: bold;">{{ ucfirst($quote->status) }}</span></p>

                <p>You can track the status of your quote using the following link:</p>
                <p>
                    <a href="{{ $trackingUrl }}" class="button">Track Your Quote Status</a>
                </p>
            </div>

            <p><strong>Request Summary:</strong></p>
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
                    <li><strong>Date Submitted:</strong> {{ $quote->created_at->format('F d, Y \a\t H:i') }}</li>
                    @if ($hasProducts)
                        <li><strong>Products Requested:</strong> {{ $quote->products->count() }}</li>
                    @endif
                </ul>
            </div>

            <p><strong>What happens next?</strong></p>
            <ol>
                <li>Our team will review your requirements</li>
                <li>We'll prepare a detailed quotation</li>
                <li>You'll receive the quote via email within 24-48 hours</li>
                <li>You can track progress anytime using your quote code</li>
            </ol>

            @if ($quote->quote_path)
                <p>A PDF summary of your quote request is attached to this email for your records.</p>
            @endif

            <p>If you have any questions or need to make changes to your request, please reply to this email.</p>

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
