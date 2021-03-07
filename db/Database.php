<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 06-Mar-21
 * Time: 10:45
 */

namespace emcodepro\mvc\db;

use emcodepro\mvc\Application;
use PDO;

class Database
{
    public $pdo;

    public function __construct($config)
    {
        $dsn = $config['dsn'];
        $user = $config['user'];
        $password = $config['password'];
        $this->pdo = new PDO($dsn, $user, $password);

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param $down
     * @return bool|int
     */
    public function applyMigrations($down)
    {
        if($down){
            return $this->downMigrations();
        }

        $this->createMigrationsTable();
        $getAppliedMigrations = $this->getAppliedMigrations();
        $files = scandir(Application::$ROOT_PATH . '/migrations');
        $migrations = array_diff($files, $getAppliedMigrations);

        $toApplyMigrations = [];

        foreach ($migrations as $migration)
        {
            if($migration === '.' || $migration === '..'){
                continue;
            }

            $toApplyMigrations[] = $migration;

            $className = pathinfo($migration, PATHINFO_FILENAME);

            $this->log("Applying migration $migration");
            require_once  Application::$ROOT_PATH . '/migrations/' . $migration;

            $intance = new $className();
            $intance->up();
            $this->log("Applied migration $migration");
        }

        if(empty($toApplyMigrations)){
            return $this->log("All migrations applied!");
        }

        $this->applyNewMigrations($toApplyMigrations);
    }

    /**
     * @param $message
     * @return bool
     */
    public function log($message)
    {
        echo '[' . date("Y.m.d H:i:s") . '] - ' . $message . PHP_EOL;
        return true;
    }

    /**
     * Create table migrations if not exists
     */
    public function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations(
                                        id INT(11) AUTO_INCREMENT PRIMARY KEY,
                                        migration VARCHAR(250) NOT NULL,
                                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                   ) ENGINE=INNODB");
    }

    /**
     * @return array
     */
    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Apply new migrations
     * @param $migrations
     * @return int
     */
    public function applyNewMigrations($migrations)
    {
        $sql = implode(",", array_map( fn($el) => "('$el')", $migrations ));
        return $this->pdo->exec("INSERT INTO migrations(migration) VALUES $sql");
    }

    public function downMigrations()
    {
        $getAppliedMigrations = array_reverse($this->getAppliedMigrations());

        if(empty($getAppliedMigrations)){
            return $this->log("No migrations found!");
        }

        foreach ($getAppliedMigrations as $appliedMigration){
            require_once  Application::$ROOT_PATH . '/migrations/' . $appliedMigration;

            $className = pathinfo($appliedMigration, PATHINFO_FILENAME);

            $this->log("Downing migration $className");
            $intance = new $className();
            $intance->down();
            $this->log("Done");
        }

        return $this->pdo->exec("TRUNCATE TABLE migrations");
    }
}