<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard landing page.
     */
    public function __invoke(): View
    {
        return $this->page('Dashboard');
    }
}
