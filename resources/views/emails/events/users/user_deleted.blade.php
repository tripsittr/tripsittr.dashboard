{{-- filepath: /Users/tripsittr/Documents/GitHub/tripsittr.dashboard/resources/views/emails/model-created.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A New User Has Been Created</title>
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
        <h2>A User Has Been Deleted</h2>
        <p>Hello,</p>
        <p>A model has been deleted in the system:</p>
        <ul>
            <li><strong>User Name:</strong> {{ $model->name }}</li>
            <li><strong>Email:</strong> {{ $model->email }}</li>
            <li><strong>Deleted By:</strong> {{ $user->name }}</li>
            <li><strong>Deleted At:</strong> {{ now()->toDateTimeString() }}</li>
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