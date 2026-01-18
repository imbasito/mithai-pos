<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\LicenseHelper;

class CheckLicense
{
    /**
     * Routes that don't require license check
     */
    protected $except = [
        'backend.admin.license',
        'backend.admin.license.activate',
        'login',
        'logout',
    ];

    /**
     * Check if app is licensed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip license check for excepted routes
        $currentRoute = $request->route()->getName();
        if (in_array($currentRoute, $this->except)) {
            return $next($request);
        }

        // Check if license is valid
        if (!LicenseHelper::isActivated()) {
            return redirect()->route('backend.admin.license')
                ->with('warning', 'Please activate your license to use the application.');
        }

        return $next($request);
    }
}
