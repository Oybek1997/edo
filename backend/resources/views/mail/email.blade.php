<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
    @foreach (json_decode($details['content']) as $key => $item)
        @if ($key == 'Link')
            <p><b>{{ $key }}: </b> <a href="{{ $item }}">{{ $item }}</a></p>
        @else
            <p><b>{{ $key }}: </b> {{ $item }}</p>
        @endif
    @endforeach
    <p>{{ $details['footer'] }}</p>
</body>

</html>
