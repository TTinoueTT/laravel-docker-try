<?php

namespace App\Contexts;

// ランダム生成クラス
// 引数で指定した桁数のランダム文字列を生成
class RandomComponent
{
    public static function Generate($length = 8, $randomType = null)
    {

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        switch ($randomType) {
            case 2;
                $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 3;
                $chars = '0123456789';
                break;
        }
        $count = mb_strlen($chars);
        $result = "";
        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            if ($i == 0  && $chars[$index] == "0") {
                $index += 1;
            }
            $result .= mb_substr($chars, $index, 1);
        }
        return $result;
    }
}
