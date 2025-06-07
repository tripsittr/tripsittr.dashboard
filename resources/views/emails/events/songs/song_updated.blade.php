<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Song Has Been Updated</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
            border-top: 1px solid #ddd;
            font-size: 0.9em;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #C75D5D;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #a44949;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
    </div>

    <div class="content">
        <h2>Song Has Been Updated</h2>
        <p>Hello,</p>
        <p>A model has been updated in the system:</p>
        <ul>
            <li><strong>Song Name:</strong> {{ $model->title }}</li>
            <li><strong>Updated By:</strong> {{ $user->name }}</li>
            <li><strong>Updated At:</strong> {{ now()->toDateTimeString() }}</li>
        </ul>
        <p>You can view more details by logging into the system.</p>
        <a href="{{ config('app.url') }}" class="button">Go to Dashboard</a>
        <p>Thank you,</p>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="footer">
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</body>
</html>