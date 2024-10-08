<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            background-color: #B30600;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #B30600;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #900400;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #aaaaaa;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>{{ $messageDetails['title'] }}</h1>
        </div>
        <div class="content">
            <p>Hi {{ $messageDetails['recipient_name'] }},</p>
            <p>{{ $messageDetails['sender_name'] }} has {{ $messageDetails['action'] }}.</p>
            <p><strong>Subject:</strong> {{ $messageDetails['subject'] }}</p>
            <p><strong>Message Snippet:</strong> {{ $messageDetails['body_snippet'] }}</p>
            <p><strong>Sent On:</strong> {{ $messageDetails['created_at'] }}</p>
            
            <a href="{{ url('/messages') }}" class="button">View Your Inbox</a>
        </div>
        <div class="footer">
            <p>Thank you for using {{ config('app.name') }}!</p>
        </div>
    </div>

</body>
</html>
