<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): Collection
    {
        // 書籍一覧を取得
        $books = Book::all();
        // 書籍一覧をレスポンス
        return $books;
    }

    public function show(string $id): Book
    {
        // 書籍を一件取得
        $book = Book::findOrFail($id);

        // 取得した書籍をレスポンスとして返す
        return $book;
    }

    public function create(): View
    {
        // View にカテゴリー一覧を表示するために全件取得
        $categories = Category::all();

        // View オブジェクトを返す、'admin/book/create' の代わりに、admin.book.create とすることもできる
        return view('admin/book/create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request): Book
    {
        // 書籍データ登録用のオブジェクトを作成する
        $book = new Book();

        // リクエストオブジェクトからパラメータを取得
        $book->category_id = $request->category_id;
        $book->title = $request->title;
        $book->price = $request->price;

        // 保存
        $book->save();

        // 保存した書籍情報をレスポンスとして返す
        return $book;
    }
}
