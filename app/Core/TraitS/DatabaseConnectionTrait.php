<?php

namespace App\Core\TraitS;

use Dotenv\Dotenv;
use Exception;
use PDO;
use PDOException;

trait DatabaseConnectionTrait
{
    protected static $pdo;

    public function getPDO()
    {
        if (self::$pdo === null) {
            $host = $_ENV['DB_HOST'];
            $user = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];
            $database = $_ENV['DB_DATABASE'];

            try {
                self::$pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                error_log("Connection successful: Established connection to database.");
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }

        return self::$pdo;
    }
    protected function executeTransaction(callable $callback): void
    {
        $this->pdo->beginTransaction();
        try {
            $callback();
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    protected function runSql()
    {
        $sql = file_get_contents(__DIR__ . '/task.sql');
        $this->pdo->exec($sql);
    }
}
