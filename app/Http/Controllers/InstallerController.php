<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InstallerController extends Controller
{

    private $_minPhpVersion = '7.0.0';

    public function installWelcome()
    {
        return view('install.welcome');
    }

    public function installWelcomeProcess(Request $request)
    {
        $validator = $request->validate([
            'check' => 'required|in:1'
        ], [
            'check.required' => 'Anda harus klik check pada checkbox di bawah untuk melanjutkan installasi'
        ]);

        return redirect()->route('install.requirement.check');
    }

    public function requirementCheck()
    {
        $requirement = config('install');
        return view('install.requirement_check', compact('requirement'));
    }

    public function requirementCheckProcess(Request $request)
    {
        $phpVersionSupport = $this->checkPHPversion(config('installer.core.minPhpVersion'));
        $requirementsCheck  = $this->check(config('install.requirements'));

        if ($phpVersionSupport['supported'] == false) {
              $request->session()->flash('error', 'Versi PHP tidak support silahkan update versi PHP anda. Supported PHP version min '. $phpVersionSupport['minimum'] . ' current PHP version '. $phpVersionSupport['current']);
              return redirect()->back();
        }

        if ($requirementsCheck['errors']) {
            $request->session()->flash('error', 'Ekstensi PHP Tidak memenuhi requirement, silahkan aktifkan ekstensi PHP yang dibutuhkan oleh website.');
            $request->session()->flash('error_list', $requirementsCheck['requirements']['php']);
            return redirect()->back();
        }

        return redirect()->route('install.step.one');
    }

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

        // make .env empty
        // write to env file
        try {
            $envFile = app()->environmentFilePath();
            $envFileContents = File::get($envFile);
            $envFileContents = str_replace('DB_HOST=' . env('DB_HOST'), 'DB_HOST=', $envFileContents);
            $envFileContents = str_replace('DB_CONNECTION=' . env('DB_CONNECTION'), 'DB_CONNECTION=', $envFileContents);
            $envFileContents = str_replace('DB_PORT=' . env('DB_PORT'), 'DB_PORT=', $envFileContents);
            $envFileContents = str_replace('NEW_DB=' . env('NEW_DB'), 'NEW_DB=', $envFileContents);
            $envFileContents = str_replace('DB_USERNAME=' . env('DB_USERNAME'), 'DB_USERNAME=', $envFileContents);
            $envFileContents = str_replace('DB_PASSWORD=' . env('DB_PASSWORD'), 'DB_PASSWORD=', $envFileContents);
            File::put($envFile, $envFileContents);

            $request->session()->flash('success', 'Berhasil reset konfigurasi');
            return redirect()->route('install.step.two');
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect()->back();
        }

    }

    public function stepTwo()
    {
        return view('install.step_two');
    }

    public function stepTwoConfirmation()
    {
        $dbHost = env('DB_HOST');
        $dbConnection = env('DB_CONNECTION');
        $dbPort = env('DB_PORT');
        $dbDatabase = env('NEW_DB');
        $dbUsername = env('DB_USERNAME');
        $dbPassword = 'password';

        return view('install.step_two_confirmation', compact('dbHost', 'dbConnection', 'dbPort', 'dbDatabase', 'dbUsername', 'dbPassword'));
    }

    public function stepTwoProcess(Request $request)
    {
        $validator = $request->validate([
            'db_host' => 'required',
            'db_connection' => 'required',
            'db_port' => 'required',
            'db_name' => 'required|regex:/^\S*$/|max:255',
            'db_username' => 'required|regex:/^\S*$/|max:255',
            'db_password' => 'required|regex:/^\S*$/|max:255'
        ]);

        $dbName = $validator['db_name'];
        $dbUsername = $validator['db_username'];
        $dbPassword = $validator['db_password'];

        // write to env file
        $envFile = app()->environmentFilePath();
        $envFileContents = File::get($envFile);
        $envFileContents = str_replace('DB_HOST=' . env('DB_HOST'), 'DB_HOST='.$validator['db_host'], $envFileContents);
        $envFileContents = str_replace('DB_CONNECTION=' . env('DB_CONNECTION'), 'DB_CONNECTION='.$validator['db_connection'], $envFileContents);
        $envFileContents = str_replace('DB_PORT=' . env('DB_PORT'), 'DB_PORT='.$validator['db_port'], $envFileContents);
        $envFileContents = str_replace('NEW_DB=' . env('NEW_DB'), 'NEW_DB='.$dbName, $envFileContents);
        $envFileContents = str_replace('DB_USERNAME=' . env('DB_USERNAME'), 'DB_USERNAME='.$dbUsername, $envFileContents);
        $envFileContents = str_replace('DB_PASSWORD=' . env('DB_PASSWORD'), 'DB_PASSWORD='.$dbPassword, $envFileContents);
        File::put($envFile, $envFileContents);

        return redirect()->route('install.step.two.confirmation');
    }

    public function stepTwoInstall(Request $request)
    {
        try {
            $dbDatabase = env('NEW_DB');
            // create database
            DB::connection()->getPdo()->exec("CREATE DATABASE $dbDatabase");

            // write db files
            $envFile = app()->environmentFilePath();
            $envFileContents = File::get($envFile);
            $envFileContents = str_replace('DB_DATABASE=' . env('DB_DATABASE'), 'DB_DATABASE='.$dbDatabase, $envFileContents);
            $envFileContents = str_replace('NEW_DB=' . env('NEW_DB'), 'NEW_DB=', $envFileContents);
            File::put($envFile, $envFileContents);

            $request->session()->flash('success', 'Berhasil membuat database baru');
            return redirect()->route('install.step.three');
        } catch (\Exception $e) {
            if ($e->getCode() == "42P04") {
                $request->session()->flash('success', 'Database sudah ada.');
                return redirect()->route('install.step.three');
            }
            $request->session()->flash('error', 'Gagal installasi database ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function stepThree()
    {
        $migrationFiles = File::files(database_path('migrations'));

        return view('install.step_three', compact('migrationFiles'));
    }

    public function stepThreeProcess(Request $request)
    {
        try {
            Artisan::call('migrate');
            $request->session()->flash('success', Artisan::output());
            return redirect()->route('install.step.four');
        }catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function stepFour()
    {
        return view('install.step_four');
    }

    public function stepFourProcess(Request $request, User $user)
    {
        $validator = $request->validate([
            'name' => 'required',
            'password' => 'required',
            'role' => 'required'
        ]);

        $data = $validator;
        $data['password'] = bcrypt($data['password']);

        try {
            $user->create($data);

            $request->session()->flash('success', 'suskses membuat akun admin');
            return redirect()->route('install.step.five');
        } catch (\Exception $e) {
            $request->session()->flash('error', $e->getMessage());
            return redirect()->back();
        }

    }

    public function stepFive(User $user)
    {
        $admin = $user->first();

        return view('install.step_five', compact('admin'));
    }

    // other helper method

    /**
     * Check for the server requirements.
     *
     * @param  array  $requirements
     * @return array
     */
    private function check(array $requirements)
    {
        $results = [];
        $results['errors'] = false;

        foreach ($requirements as $type => $requirement) {
            switch ($type) {
                // check php requirements
                case 'php':
                    foreach ($requirements[$type] as $requirement) {
                        $results['requirements'][$type][$requirement] = true;

                        if (! extension_loaded($requirement)) {
                            $results['requirements'][$type][$requirement] = false;

                            $results['errors'] = true;
                        }
                    }
                    break;
                // check apache requirements
                case 'apache':
                    foreach ($requirements[$type] as $requirement) {
                        // if function doesn't exist we can't check apache modules
                        if (function_exists('apache_get_modules')) {
                            $results['requirements'][$type][$requirement] = true;

                            if (! in_array($requirement, apache_get_modules())) {
                                $results['requirements'][$type][$requirement] = false;

                                $results['errors'] = true;
                            }
                        }
                    }
                    break;
            }
        }

        return $results;
    }

    /**
     * Check PHP version requirement.
     *
     * @return array
     */
    private function checkPHPversion(string $minPhpVersion = null)
    {
        $minVersionPhp = $minPhpVersion;
        $currentPhpVersion = $this->getPhpVersionInfo();
        $supported = false;

        if ($minPhpVersion == null) {
            $minVersionPhp = $this->_minPhpVersion;
        }

        if (version_compare($currentPhpVersion['version'], $minVersionPhp) >= 0) {
            $supported = true;
        }

        $phpStatus = [
            'full' => $currentPhpVersion['full'],
            'current' => $currentPhpVersion['version'],
            'minimum' => $minVersionPhp,
            'supported' => $supported,
        ];

        return $phpStatus;
    }

    /**
     * Get current Php version information.
     *
     * @return array
     */

    private static function getPhpVersionInfo()
    {
        $currentVersionFull = PHP_VERSION;
        preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
        $currentVersion = $filtered[0];

        return [
            'full' => $currentVersionFull,
            'version' => $currentVersion,
        ];
    }

}
