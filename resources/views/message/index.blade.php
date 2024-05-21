<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Message Sample</title>
</head>

<body>
    <main>
        <h1>メッセージ</h1>
        <form action="/messages" method="POST">
            @csrf
            <input type="text" name="body">
            <input type="submit" value="投稿">
        </form>
        <ul>
            @foreach ($messages as $message)
                <li>
                    {{ $message->body }}/
                    <a href="{{ route('messages.destroy', $message) }}">削除</a>
                    {{-- <a href="/messages/{{ $message->id }}/delete">削除</a> --}}
                </li>
                {{-- <li>{!! $message->body !!}</li> 脆弱性あり --}}
                {{-- <li><a href="{{ $message->body }}">{{ $message->body }}</a></li>  // 脆弱性あり --}}
            @endforeach
        </ul>
    </main>
</body>

</html>
