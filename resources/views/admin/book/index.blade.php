<x-layouts.book-manager>
    <x-slot:title>
        {{--
            slot で呼び出されるコンポーネント側から
            layouts 側に渡す変数の値を指定
        --}}
        書籍一覧
    </x-slot:title>
    <h1>書籍一覧</h1>
    @if (session('message'))
        <div style="color: blue">
            {{ session('message') }}
        </div>
    @endif
    <a href="{{ route('admin.book.create') }}">追加</a>
    <x-book-table :$books />
</x-layouts.book-manager>
