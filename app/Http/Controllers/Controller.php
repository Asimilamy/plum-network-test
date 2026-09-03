<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

abstract class Controller
{
    /**
     * Render a Vue page component from `resources/js/pages` through the SPA shell.
     *
     * @param  array<string, mixed>  $props
     */
    protected function page(string $component, array $props = []): View
    {
        return view('app', [
            'page' => $component,
            'props' => $props,
        ]);
    }
}
