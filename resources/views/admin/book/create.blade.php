<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>書籍登録</title>
</head>

<body>
    <main>
        <h1>書籍登録</h1>
        {{-- リダイレクト時のエラー文 --}}
        @if ($errors->any())
            <x-error-messages :$errors />
        @endif
        <form action="{{ route('admin.book.store') }}" method="post">
            @csrf
            <div>
                <label for="category">カテゴリ</label>
                <select name="category_id" id="category">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $category->title == 'management' ? $category->id : null) == $category->id)>
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="title">タイトル</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}">
            </div>
            <div>
                <label for="price">価格</label>
                <input type="text" name="price" id="price" value="{{ old('price') }}">
            </div>
            <input type="submit" value="送信">
        </form>
    </main>
</body>

</html>
