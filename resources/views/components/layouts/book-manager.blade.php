<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
</head>

<body>
    <header>
        書籍管理システム
        <form action="{{ route('admin.destroy') }}" method="post">
            @csrf
            <input type="submit" value="ログアウト">
        </form>
        <hr>
    </header>
    <main>
        {{ $slot }}
    </main>
    <footer>
        <hr>
        @Laravel
    </footer>
</body>

</html>
