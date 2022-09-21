<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Support\Facades\DB;
use Composer\Semver\Comparator;

class IController extends Controller
{
    protected $appVersion;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->appVersion = config('author.app_version');
    }

    public function index()
    {
        //Check for .env file
        $this->isInstalled();
        $this->installSettings();

        return view('ic.index');
    }

    public function details()
    {
    	//Check for .env file
        $this->isInstalled();
        $this->installSettings();

        //Check if .env.example is present or not.
        $env_example = base_path('.env.example');
        if (!file_exists($env_example)) {
            die("<b>.env.example file not found in <code>$env_example</code></b> <br/><br/> - In the downloaded codebase you will find .env.example file, please upload it and refresh this page.");
        }

        return view('ic.details');
    }

    public function postDetails(Request $request)
    {
        //Check for .env file
        $this->isInstalled();
        $this->installSettings();

        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');
            
            $validatedData = $request->validate(
                [
                'APP_NAME' => 'required',
                'ENVATO_PURCHASE_CODE' => 'required',
                'DB_DATABASE' => 'required',
                'DB_USERNAME' => 'required',
                'DB_PASSWORD' => 'required',
                'DB_HOST' => 'required',
                'DB_PORT' => 'required'
                ],
                [
                    'APP_NAME.required' => 'App Name is required',
                    'ENVATO_PURCHASE_CODE.required' => 'Envaot Purchase code is required',
                    'DB_DATABASE.required' => 'Database Name is required',
                    'DB_USERNAME.required' => 'Database Username is required',
                    'DB_PASSWORD.required' => 'Database Password is required',
                    'DB_HOST.required' => 'Database Host is required',
                    'DB_PORT.required' => 'Database port is required',
                ]
            );

            $this->outputLog = new BufferedOutput;

            $input = $request->only(['APP_NAME', 'APP_TITLE', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'ENVATO_PURCHASE_CODE',
                'ENVATO_EMAIL', 'ENVATO_USERNAME']);

            $input['APP_DEBUG'] = "false";
            $input['APP_URL'] = url("/");
            $input['APP_ENV'] = 'live';

            //Check for database details
            $mysql_link = @mysqli_connect($input['DB_HOST'], $input['DB_USERNAME'], $input['DB_PASSWORD'], $input['DB_DATABASE'], $input['DB_PORT']);
            if (mysqli_connect_errno()) {
                $msg = "<b>ERROR</b>: Failed to connect to MySQL: " . mysqli_connect_error();
                $msg .= "<br/>Provide correct details for 'Database Host', 'Database Port', 'Database Name', 'Database Username', 'Database Password'.";
                return redirect()
                    ->back()
                    ->with('error', $msg);
            }

            //pos boot
            $return = pos_boot($input['APP_URL'], __DIR__, $input['ENVATO_PURCHASE_CODE'], $input['ENVATO_EMAIL'], $input['ENVATO_USERNAME']);
            if (!empty($return)) {
                return $return;
            }

            //Get .env file details and write the contents in it.
            $envPathExample = base_path('.env.example');
            $envPath = base_path('.env');

            $env_lines = file($envPathExample);
            foreach ($input as $index => $value) {
                foreach ($env_lines as $key => $line) {
                    //Check if present then replace it.
                    if (strpos($line, $index) !== false) {
                        $env_lines[$key] = $index . '="' . $value . '"' . PHP_EOL;
                    }
                }
            }
            
            //TODO: Remove false & automate the process of creating .env file.
            if (false) {
                // $fp = fopen($envPath, 'w');
                // fwrite($fp, implode('', $env_lines));
                // fclose($fp);

                // //Artisan commands
                // $this->runArtisanCommands();

                // return redirect()->route('install.success');
            } else {
                $this->deleteEnv();

                //Show intermediate steps if not able to copy file.
                $envContent = implode('', $env_lines);
                return view('ic.envText')
                    ->with(compact('envContent'));
            }
        } catch (Exception $e) {
            $this->deleteEnv();

            return redirect()->back()
                ->with('error', 'Something went wrong, please try again!!');
        }
    }

    public function installAlternate(Request $request)
    {
        try {
            $this->installSettings();

            //Check if no .env file than redirect back.
            $envPath = base_path('.env');
            if (!file_exists($envPath)) {
                return redirect()->route('install.details')
                    ->with('error', 'Looks like you haven\'t created the .env file ' . $envPath);
            }

            $this->runArtisanCommands();
            return redirect()->route('install.success');
        } catch (Exception $e) {
            $this->deleteEnv();

            return redirect()->back()
                ->with('error', 'Something went wrong, please try again!!');
        }
    }

    public function successfull() {
        return view('ic.success');
    }

    public function updateConfirmation(){
        $db_version = \App\System::getVersion();
        
        if (Comparator::greaterThan($this->appVersion, $db_version)) {
            return view('ic.update_confirmation');
        } else {
            abort(404);
        }
    }

    //Updating
    public function update(Request $request)
    {
        $version = null;

        try {
            DB::beginTransaction();

            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');

            $input = $request->only(['ENVATO_PURCHASE_CODE', 'ENVATO_USERNAME', 'ENVATO_EMAIL']);

            $db_version = \App\System::getVersion();
            if (Comparator::greaterThan($this->appVersion, $db_version)) {
                ini_set('max_execution_time', 0);
                ini_set('memory_limit', '512M');
                $this->installSettings();
                
                $return = pos_boot(config('app.url'), __DIR__, $input['ENVATO_PURCHASE_CODE'], $input['ENVATO_EMAIL'], $input['ENVATO_USERNAME'], 1);
                if (!empty($return)) {
                    return $return;
                }

                DB::statement('SET default_storage_engine=INNODB;');
                Artisan::call('migrate', ["--force"=> true]);

                \App\System::updateSystem(['version' => $this->appVersion]);
            } else {
                abort(404);
            }
            
            DB::commit();
            
            $output = ['success' => 1,
                        'msg' => 'Updated Succesfully to version ' . $this->appVersion . ' !!'
                    ];
            return redirect('login')->with('status', $output);
        } catch (Exception $e) {
            DB::rollBack();
            die($e->getMessage());
        }
    }

    private function isInstalled()
    {
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            abort(404);
        }
    }

    private function installSettings()
    {
        config(['app.debug' => true]);
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    }

    private function deleteEnv()
    {
        $envPath = base_path('.env');
        if ($envPath && file_exists($envPath)) {
            unlink($envPath);
        }
        return true;
    }

    private function runArtisanCommands()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $this->installSettings();
        
        DB::statement('SET default_storage_engine=INNODB;');
        Artisan::call('install');
        Artisan::call('db:seed');
    }

}
