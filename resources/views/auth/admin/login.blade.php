<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ログイン</title>
</head>

<body>
    @if ($errors->any())
        <x-alert class="danger">
            <x-error-messages :$errors />
        </x-alert>
    @endif
    <form action="{{ route('admin.create') }}" method="post">
        @csrf
        <div>
            <div>
                <label for="login_id">ログインID: </label>
                <input type="text" name="login_id" id="login_id">
            </div>
            <div>
                <label for="password">パスワード： </label>
                <input type="password" name="password" id="password">
            </div>
        </div>
        <div>
            <input type="submit" value="ログイン">
        </div>
    </form>
</body>

</html>
