<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 30px -30px;
            text-align: center;
        }
        .field {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .field-value {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
        }
        .message-content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            white-space: pre-wrap;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Form Message</h1>
        </div>
        
        <div class="field">
            <div class="field-label">From:</div>
            <div class="field-value">{{ $name }} ({{ $email }})</div>
        </div>
        
        <div class="field">
            <div class="field-label">Subject:</div>
            <div class="field-value">{{ $subject }}</div>
        </div>
        
        <div class="field">
            <div class="field-label">Message:</div>
            <div class="message-content">{{ $messageContent }}</div>
        </div>
        
        <div class="footer">
            <p>This message was sent from your portfolio contact form.</p>
            <p>Reply directly to this email to respond to {{ $name }}.</p>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">
            <div style="font-size: 12px; color: #999;">
                <p><strong>Technical Details:</strong></p>
                <p>Sent: {{ $timestamp }}</p>
                <p>IP: {{ $ipAddress }}</p>
                <p>User Agent: {{ $userAgent }}</p>
            </div>
        </div>
    </div>
</body>
</html>