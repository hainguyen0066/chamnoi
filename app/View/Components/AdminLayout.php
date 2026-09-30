<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Layout khu vực quản trị (/admin/*): sidebar điều hướng + header + flash message.
 * Dùng trong Blade: <x-admin-layout><x-slot name="header">Tiêu đề</x-slot>...</x-admin-layout>
 */
class AdminLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.admin');
    }
}
