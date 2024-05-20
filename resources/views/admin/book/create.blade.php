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
        <x-alert class="danger">
            <x-error-messages :$errors />
        </x-alert>
    @endif
    <form action="{{ route('admin.book.store') }}" method="post">
        @csrf
        <x-book-form :$categories :$authors />
        <input type="submit" value="送信">
    </form>
</x-layouts.book-manager>
