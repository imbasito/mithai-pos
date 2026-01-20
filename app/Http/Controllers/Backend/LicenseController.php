<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Helpers\LicenseHelper;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    /**
     * Show license activation page in dashboard (System & Updates)
     */
    public function index()
    {
        $licenseInfo = LicenseHelper::getInfo();
        $machineId = LicenseHelper::getMachineId();
        
        return view('backend.license.index', compact('licenseInfo', 'machineId'));
    }

    /**
     * Show standalone activation page for guest/unlicensed users
     */
    public function showActivate()
    {
        if (LicenseHelper::isActivated()) {
            return redirect()->route('login');
        }
        
        $machineId = LicenseHelper::getMachineId();
        return view('activate', compact('machineId'));
    }

    /**
     * Activate license from public page
     */
    public function activatePublic(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string'
        ]);

        $licenseKey = trim($request->license_key);
        $result = LicenseHelper::activate($licenseKey);

        if ($result['valid']) {
            return redirect()->route('login')->with('success', 'License activated successfully! You can now log in.');
        } else {
            return redirect()->back()->with('error', $result['error']);
        }
    }

    /**
     * Activate license from admin dashboard (updates)
     */
    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string'
        ]);

        $licenseKey = trim($request->license_key);
        $result = LicenseHelper::activate($licenseKey);

        if ($result['valid']) {
            return redirect()->back()->with('success', 'License activated successfully! Licensed to: ' . $result['shop']);
        } else {
            return redirect()->back()->with('error', $result['error']);
        }
    }

    /**
     * Deactivate license (for testing)
     */
    public function deactivate()
    {
        writeConfig('license_key', '');
        writeConfig('licensed_to', '');
        
        return redirect()->back()->with('success', 'License removed.');
    }
}
