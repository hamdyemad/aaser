<!DOCTYPE html>
<html>
<head>
    <title>Welcome In Hona Asseer Application</title>
</head>
<body>
    <h1>Welcome, {{ $user->name }}!</h1>
    <p>The message is => {{ $customMessage }}</p>
    <p>The link is => <a href="{{ $link }}">{{ $link }}</a></p>

    @if($image)
        <p>The image is => <a href="{{ asset('storage/' . $image) }}">Download image</a></p>
    @endif

    @if($file)
        <p>The file is => <a href="{{ asset('storage/' . $file) }}">Download File</a></p>
    @endif
</body>
</html>
