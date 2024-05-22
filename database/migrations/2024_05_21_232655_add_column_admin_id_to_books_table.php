<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('admin_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            /*
             * dropForeign は外部キー制約の解除を行う
             * フィールドの削除を行う前に制約の解除を先に行う必要がある
             */
            $table->dropForeign('books_admin_id_foreign');
            $table->dropColumn('admin_id');
        });
    }
};
