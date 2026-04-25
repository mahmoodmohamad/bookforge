<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Clinic;
use App\Models\Client;
use App\Models\Physicain;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $clients=Client::count();
        $clinics=Clinic::count();
        $providers = Physicain::count();
        return view('admin.dashboard', compact('clients','clinics','providers'));
    }
}
