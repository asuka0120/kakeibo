<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * コンポーネントを表すビュー（表示内容）を取得する。
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
