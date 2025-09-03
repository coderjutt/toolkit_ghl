<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $announcement->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
        h1 {
            color: #1e3a8a;
        }
        p {
            line-height: 1.6;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
        a {
            color: #1e3a8a;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>{{ $announcement->title }}</h1>
        <p>{!! nl2br(e($announcement->body)) !!}</p>

        <div class="footer">
            This email was sent from {{ config('app.name') }}.<br>
            If you have any questions, contact us at <a href="mailto:{{ $announcement->from_email ?? 'support@example.com' }}">{{ $announcement->from_email ?? 'support@example.com' }}</a>.
        </div>
    </div>
</div>
</body>
</html>
