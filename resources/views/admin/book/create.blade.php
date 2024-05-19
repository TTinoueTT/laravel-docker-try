<x-layouts.book-manager>
    <x-slot:title>
        {{--
            slot で呼び出されるコンポーネント側から
            layouts 側に渡す変数の値を指定
        --}}
        書籍登録
    </x-slot:title>
    <h1>書籍登録</h1>
    {{-- リダイレクト時のエラー文 --}}
    @if ($errors->any())
        <x-alert>
            <x-error-messages :$errors />
        </x-alert>
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
</x-layouts.book-manager>
