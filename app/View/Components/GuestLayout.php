<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public string $maxWidth;

    public function __construct(string $maxWidth = 'sm:max-w-md')
    {
        $this->maxWidth = $maxWidth;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
