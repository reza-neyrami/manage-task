<?php

namespace App\Core\TraitS;

use Dotenv\Dotenv;
use Exception;
use PDO;
use PDOException;

trait DatabaseConnectionTrait
{
    protected $pdo;


    public function __construct()
    {
        // $env = Dotenv::createImmutable(__DIR__."../../../.env");
        // $env->load();
        // $host = $_ENV['DB_HOST'];
        // $user = $_ENV['DB_USER'];
        // $password = $_ENV['DB_PASSWORD'];
        // $database = $_ENV['DB_DATABASE'];

        $host = "127.0.0.1";
        $user = "root";
        $password = "secret";
        $database = "taskmanage";
        try {

            $this->pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // $sql = file_get_contents(__DIR__.'/task.sql');
            // $this->pdo->exec($sql);
            error_log("Connection successful: Established connection to database.");
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
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
}
