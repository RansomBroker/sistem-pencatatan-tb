<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InstallerController extends Controller
{
    public function stepOne()
    {
        return view('install.step_one');
    }

    public function stepOneProcess(Request $request)
    {
        $validator = $request->validate([
            'check' => 'required|in:1'
        ], [
            'check.required' => 'Anda harus klik check pada checkbox di bawah untuk melanjutkan installasi'
        ]);

        return redirect()->route('install.step.two');
    }

    public function stepTwo()
    {
        return view('install.step_two');
    }

    public function stepTwoProcess(Request $request)
    {
        $validator = $request->validate([
            'db_host' => 'required',
            'db_connection' => 'required',
            'db_port' => 'required'
        ]);

        // write to env file
        $envFile = app()->environmentFilePath();
        $envFileContents = File::get($envFile);
        $envFileContents = str_replace('DB_HOST=' . env('DB_HOST'), 'DB_HOST='.$validator['db_host'], $envFileContents);
        $envFileContents = str_replace('DB_CONNECTION=' . env('DB_CONNECTION'), 'DB_CONNECTION='.$validator['db_connection'], $envFileContents);
        $envFileContents = str_replace('DB_PORT=' . env('DB_PORT'), 'DB_PORT='.$validator['db_port'], $envFileContents);
        File::put($envFile, $envFileContents);

        // check conncetion
        try {
            DB::connection()->getPdo();
            $request->session()->flash('success', 'Berhasil terhubung ke Database Server');
            return redirect()->route('install.step.three');
        } catch (\Exception $e) {
            $request->session()->flash('error', 'Gagal terhubung ke Database server ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function stepThree()
    {
        return view('install.step_three');
    }

    public function stepThreeProcess(Request $request)
    {
        $validation = $request->validate([
            'db_name' => 'required|regex:/^\S*$/|max:255',
            'db_username' => 'required|regex:/^\S*$/|max:255',
            'db_password' => 'required|regex:/^\S*$/|max:255'
        ]);

        $dbName = $validation['db_name'];
        $dbUsername = $validation['db_username'];
        $dbPassword = $validation['db_password'];

        try {
            // create database
            DB::connection()->getPdo()->exec("CREATE DATABASE $dbName");

            // write to env file
            $envFile = app()->environmentFilePath();
            $envFileContents = File::get($envFile);
            $envFileContents = str_replace('DB_DATABASE=' . env('DB_DATABASE'), 'DB_DATABASE='.$dbName, $envFileContents);
            $envFileContents = str_replace('DB_USERNAME=' . env('DB_USERNAME'), 'DB_USERNAME='.$dbUsername, $envFileContents);
            $envFileContents = str_replace('DB_PASSWORD=' . env('DB_PASSWORD'), 'DB_PASSWORD='.$dbPassword, $envFileContents);
            File::put($envFile, $envFileContents);

            $request->session()->flash('success', 'Berhasil membuat database baru');
            return redirect()->route('install.step.three');
        } catch (\Exception $e) {
            $request->session()->flash('error', 'Gagal membuat database ' . $e->getMessage());
            return redirect()->back();
        }

    }

}
