<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\Component;

class ErrorMessages extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ViewErrorBag $errors
    ) {
    }

    /**
     * エラーが2件以上あるかどうかを返す
     */
    public function has2MoreErrors(): bool
    {
        return count($this->errors) > 2;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.error-messages');
    }
}
