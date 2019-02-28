<?php

namespace Heyday\AnonymisedSQLDumps\Commands;

use Illuminate\Console\Command;
use Ifsnop\Mysqldump as IMysqldump;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;

class ExportAnonymisedDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anonymised-db-dumps:export {file? : dump file name (without extension}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a SQL dump with anonymised data';

    /**
     * @var \Faker\Generator
     */
    private $faker;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = Faker::create();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $dump = $this->getDumpInstance();
            $dump->setTransformColumnValueHook(function ($tableName, $colName, $colValue) {
                return $this->anonymise($tableName, $colName, $colValue);
            });
            $dump->start($this->getFullPath());
            echo "Database successfully exported at " . $this->getFullPath() . "\n";
        } catch (\Exception $e) {
            echo 'Error generating DB dump: ' . $e->getMessage();
        }
    }

    /**
     * @return IMysqldump\Mysqldump
     * @throws \Exception
     */
    protected function getDumpInstance()
    {
        $database = DB::connection()->getConfig('database');
        $host = DB::connection()->getConfig('host');
        $username = DB::connection()->getConfig('username');
        $password = DB::connection()->getConfig('password');
        $dsn = 'mysql:host=' . $host . ';dbname=' . $database;

        return new IMysqldump\Mysqldump($dsn, $username, $password);
    }

    /**
     * @return string
     */
    protected function getFullPath(): string
    {
        $path = storage_path('db-dumps');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $file = $this->argument('file') ?? date('Y-m-d_H:i:s');

        return $path . '/' . $file . '.sql';
    }

    /**
     * @param $tableName
     * @param $colName
     * @param $colValue
     * @return string|null
     */
    private function anonymise($tableName, $colName, $colValue)
    {
        $config = config('db-export');

        if (isset($config[$tableName]) && isset($config[$tableName][$colName])) {
            if (is_array($config[$tableName][$colName])) { //JSON
                $json = json_decode($colValue, true);
                if (!empty($json)) {
                    $colValue = $this->handleJSONColumns($config[$tableName][$colName], $json);
                }
            } else { // Not JSON
                $fakerType = $config[$tableName][$colName];
                $colValue = $this->faker->{$fakerType};
            }
        }

        return $colValue;
    }

    /**
     * TODO further nesting if required
     * @param array $jsonConfig
     * @param array $jsonValue
     * @return false|string
     */
    private function handleJSONColumns(array $jsonConfig, array $jsonValue): string
    {
        foreach ($jsonValue as $column => $value) {
            echo $column . "\n";
            if (isset($jsonConfig[$column])) {
                $fakerType = $jsonConfig[$column];
                $jsonValue[$column] = $this->faker->{$fakerType};
            }
        }

        return json_encode($jsonValue);
    }

}