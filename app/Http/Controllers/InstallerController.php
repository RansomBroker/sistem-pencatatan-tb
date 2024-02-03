<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstallerController extends Controller
{
    public function stepOne()
    {
        return view('install.step_one');
    }

    public function stepOneProcess(Request $request)
    {
        $validator = $request->validate([
            'done' => 'required'
        ], [
            'done.required' => 'Anda harus klik check pada checkbox di bawah untuk melanjutkan installasi'
        ]);

        if (!file_exists(base_path('.env'))) {
            $request->session()->flash('error', 'file .env tidak terdeteksi silahkan ikuti step 1');
            return redirect()->back();
        }

        return redirect()->route();

    }
}
