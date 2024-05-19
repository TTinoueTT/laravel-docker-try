<table border="1">
    <tr>
        <th>カテゴリ</th>
        <th>書籍名</th>
        <th>価格</th>
    </tr>
    @foreach ($books as $book)
        <tr @if ($loop->even) style="background: #EEE" @endif>
            <td>{{ $book->category->title }}</td>
            <td>
                <a href="{{ route('admin.book.show', ['id' => $book->id]) }}">
                    {{ $book->title }}
                </a>
            </td>
            <td>{{ $book->price }}</td>
        </tr>
    @endforeach
</table>
