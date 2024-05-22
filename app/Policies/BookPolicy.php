<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use App\Models\Book;
use Illuminate\Support\Facades\Gate;

class BookPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(Admin $admin): bool
    {
        return true;
    }

    public function view(Admin $admin, Book $book): bool
    {
        // return true;
        return Gate::allows('example-com-user');
    }

    public function create(Admin $admin): bool
    {
        // return substr($admin->login_id, -11) === 'example.com';
        return Gate::allows('example-com-user');
        // return true;
    }

    public function update(Admin $admin, Book $book): bool
    {
        return $admin->id === $book->admin_id;
    }

    public function delete(Admin $admin, Book $book): bool
    {
        return $admin->id === $book->admin_id;
    }
}
