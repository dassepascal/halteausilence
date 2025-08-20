<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $newsletter->title }}</title>
</head>
<body>
    <div style="max-width: 800px; margin: 20px auto; padding: 20px;">
        <h1>{{ $newsletter->title }}</h1>
        <div>{!! $newsletter->content !!}</div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #666;">
            Newsletter vue dans le navigateur
        </div>
    </div>
</body>
</html>
