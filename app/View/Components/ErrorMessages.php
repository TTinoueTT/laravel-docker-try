<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\Component;

class ErrorMessages extends Component
{
    // 定義したメンバ変数はビューから参照可能(publicである必要がある)
    public ViewErrorBag $errors;
    /**
     * Create a new component instance.
     */
    public function __construct(ViewErrorBag $errors)
    {
        // メンバ変数の値はコンストラクタ引数で外から受け取る
        $this->errors = $errors;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.error-messages');
    }
}
