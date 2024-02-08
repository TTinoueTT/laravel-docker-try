<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;

    protected $attributes = [
        // self::CREATED_AT => now(),
        // self::UPDATED_AT => now(),
    ];

    // 定数の代わりにインスタンスメソッドを使用
    public function getID()
    {
        return $this->primaryKey;
    }

    // const ID = $this->primaryKey;
    const CREATED_AT = "created_at";
    const UPDATED_AT = "updated_at";
}
