<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Maintenance Mode</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: Inter, system-ui, sans-serif;
            background: #0f0f11;
            color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .card {
            max-width: 520px;
            background: #1f1f23;
            border: 1px solid #2d2d32;
            border-radius: 16px;
            padding: 2.25rem;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, .6);
        }

        h1 {
            font-size: 1.5rem;
            margin: 0 0 1rem;
            line-height: 1.2;
        }

        p {
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0 0 1.25rem;
            color: #9ca3af;
        }

        .tag {
            display: inline-block;
            background: #6b7280;
            color: #111827;
            font-size: 0.65rem;
            letter-spacing: .05em;
            font-weight: 600;
            padding: .35rem .55rem;
            border-radius: .375rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="tag">MAINTENANCE</div>
        <h1>We’ll be right back.</h1>
        <p>{{ $message ?? 'The application is temporarily unavailable while we perform updates.' }}</p>
        <p style="font-size:0.75rem; color:#6b7280;">HTTP 503 • {{ now()->toDayDateTimeString() }}</p>
    </div>
</body>

</html>