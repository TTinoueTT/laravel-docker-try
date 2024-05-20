<table border="1">
    <tr>
        <th>カテゴリ</th>
        <th>書籍名</th>
        <th>価格</th>
        <th>更新</th>
    </tr>
    @foreach ($books as $book)
        <tr @if ($loop->even) style="background: #EEE" @endif>
            <td>{{ $book->category->title }}</td>
            <td>
                {{--
                    route関数に $book を渡すと、ID を取り出してパラメータとしてくれる
                    生成される URL は /admin/books/{id} とこれまで通り
                --}}
                <a href="{{ route('admin.book.show', $book) }}">
                    {{ $book->title }}
                </a>
            </td>
            <td>{{ $book->price }}</td>
            <td>
                <a href="{{ route('admin.book.edit', $book) }}">
                    <button>更新</button>
                </a>
            </td>
        </tr>
    @endforeach
</table>
