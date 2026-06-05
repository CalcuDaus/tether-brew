<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = AppSetting::forBranch(activeBranchId())->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            AppSetting::setValue($key, $value, activeBranchId());
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
