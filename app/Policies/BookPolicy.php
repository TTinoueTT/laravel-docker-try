<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;
use App\Models\Book;

class BookPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
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
