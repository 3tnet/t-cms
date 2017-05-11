<?php

namespace App\Console\Commands;

use App\Console\Commands\Tools\EnvSettingManager;
use ClassesWithParents\D;
use Illuminate\Console\Command;
use PDO;
use Exception;
use Illuminate\Support\Str;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Initialize Command';

    protected $envSettingManager;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EnvSettingManager $envSettingManager)
    {
        $this->envSettingManager = $envSettingManager;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = $this->ask('Application URL', $this->envSettingManager->getDefaultValue('APP_URL')?:false)?:'';
        $this->envSettingManager->setEnv('APP_URL', $url);

        $appEnv = $this->choice('App Environment', ['local', 'production'], 0);
        $this->envSettingManager->setEnv('APP_ENV', $appEnv);

        $debug = (bool) $this->confirm('Enable Debug Mode?', true)?'true':'false';
        $this->envSettingManager->setEnv('APP_DEBUG', $debug);
        $this->envSettingManager->setEnv('API_DEBUG', $debug);

        $this->setupDatabaseConfig();

        $this->envSettingManager->writeToEnv();
        $this->call('key:generate');
        $this->call('storage:link');

        //$this->call('migrate');
        //$this->call('db:seed');
    }

    //
    // Database config
    //
    protected function setupDatabaseConfig()
    {
        $type = $this->choice('Database type', ['MySQL', 'Postgres', 'SQLite', 'SQL Server'], 0);
        $typeMap = [
            'SQLite' => 'sqlite',
            'MySQL' => 'mysql',
            'Postgres' => 'pgsql',
            'SQL Server' => 'sqlsrv',
        ];
        $driver = array_get($typeMap, $type);
        $method = 'setupDatabase' . Str::studly($driver);
        $newConfigs = $this->$method();
        $newConfigs['DB_CONNECTION'] = $driver;
        foreach ($newConfigs as $key => $value)
        {
            $this->envSettingManager->setEnv($key, $value);
        }
    }

    protected function setupDatabaseMysql()
    {
        $result = [];

        $dbHost = $this->envSettingManager->getDefaultValue('DB_HOST', '127.0.0.1');
        $result['DB_HOST'] = $this->ask('MySQL Host', $dbHost);

        $dbPort = $this->envSettingManager->getDefaultValue('DB_PORT', '3306');
        $result['DB_PORT'] = $this->output->ask('MySQL Port', $dbPort);

        $databaseName = $this->envSettingManager->getDefaultValue('DB_DATABASE', '');
        $result['DB_DATABASE'] = $this->ask('Database Name', $databaseName?:false) ?: '';

        $dbUserName = $this->envSettingManager->getDefaultValue('DB_USERNAME', 'root');
        $result['DB_USERNAME'] = $this->ask('MySQL Login', $dbUserName);

        $dbPassword = $this->envSettingManager->getDefaultValue('DB_PASSWORD', '');
        $result['DB_PASSWORD'] = $this->ask('MySQL Password', $dbPassword?:false) ?: '';

        return $result;
    }

    protected function setupDatabasePgsql()
    {
        $result = [];

        $dbHost = $this->envSettingManager->getDefaultValue('DB_HOST', '127.0.0.1');
        $result['DB_HOST'] = $this->ask('Postgres Host', $dbHost);

        $dbPort = $this->envSettingManager->getDefaultValue('DB_PORT', '3306');
        $result['DB_PORT'] = $this->ask('Postgres Port', $dbPort);

        $databaseName = $this->envSettingManager->getDefaultValue('DB_DATABASE', '');
        $result['DB_DATABASE'] = $this->ask('Database Name', $databaseName?:false) ?: '';

        $dbUserName = $this->envSettingManager->getDefaultValue('DB_USERNAME', 'root');
        $result['DB_USERNAME'] = $this->ask('Postgres Login', $dbUserName);

        $dbPassword = $this->envSettingManager->getDefaultValue('DB_PASSWORD', '');
        $result['DB_PASSWORD'] = $this->ask('Postgres Password', $dbPassword?:false) ?: '';

        return $result;
    }

    protected function setupDatabaseSqlite()
    {
        $filename = $this->ask('Database path');
        try {
            if (!file_exists($filename)) {
                $directory = dirname($filename);
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                }
                new PDO('sqlite:' . $filename);
            }
        } catch (Exception $ex) {
            $this->error($ex->getMessage());
            $this->setupDatabaseSqlite();
        }
        return ['DB_DATABASE' => $filename];
    }

    protected function setupDatabaseSqlsrv()
    {
        $result = [];

        $dbHost = $this->envSettingManager->getDefaultValue('DB_HOST', '127.0.0.1');
        $result['DB_HOST'] = $this->ask('SQL Host', $dbHost);

        $dbPort = $this->envSettingManager->getDefaultValue('DB_PORT', '3306');
        $result['DB_PORT'] = $this->ask('SQL Port', $dbPort);

        $databaseName = $this->envSettingManager->getDefaultValue('DB_DATABASE', '');
        $result['DB_DATABASE'] = $this->ask('Database Name', $databaseName?:false) ?: '';

        $dbUserName = $this->envSettingManager->getDefaultValue('DB_USERNAME', 'root');
        $result['DB_USERNAME'] = $this->ask('SQL Login', $dbUserName);

        $dbPassword = $this->envSettingManager->getDefaultValue('DB_PASSWORD', '');
        $result['DB_PASSWORD'] = $this->ask('SQL Password', $dbPassword?:false) ?: '';

        return $result;
    }


}