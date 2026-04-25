<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class SetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Option A: from subdomain (cairo-dental.yourapp.com)
        $slug = explode('.', $request->getHost())[0];
        
        // Option B: from URL prefix (/cairo-dental/dashboard)
        // $slug = $request->route('tenant');

        $tenant = Tenant::where('slug', $slug)
                        ->where('is_active', true)
                        ->firstOrFail();

        // Make it available everywhere
        app()->instance('tenant', $tenant);
        view()->share('tenant', $tenant);

        return $next($request);
    }
}
