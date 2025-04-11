<!DOCTYPE html>
<html>
<head>
    <title>Venue Information</title>
</head>
<body>
    <h1>Venue Information</h1>
    @foreach($data as $key => $value)
        @if(is_array($value))
            <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ implode(', ', $value) }}</p>
        @elseif($value)
            <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
        @endif
    @endforeach
</body>
</html>
