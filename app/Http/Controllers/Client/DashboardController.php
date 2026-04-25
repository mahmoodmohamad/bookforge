<?php

namespace App\Http\Controllers\Client;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('client.dashboard');
    }
}
