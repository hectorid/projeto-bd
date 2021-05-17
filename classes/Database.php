<?php
namespace App;

use PDO;
use Exception;


class Database {
    private $pdo;
    private $pdo_statement;


    function __construct() {
        if (!file_exists(DATABASE_CONNECTION_FILE))
            throw new Exception('Database connection file not found!');

        $connection = parse_ini_file(DATABASE_CONNECTION_FILE);

        $driver   = $connection['driver'];
        $host     = $connection['host'];
        $port     = $connection['port'];
        $dbname   = $connection['dbname'];
        $connection_str = "{$driver}: host={$host};port={$port};dbname={$dbname};";

        $user     = $connection['user'];
        $password = $connection['password'];

        $this->pdo = new PDO($connection_str, $user, $password);
    }


    function __destruct() {
        $this->pdo = null;
        $this->resetPDOStatement();
    }


    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }


    public function commit(): bool {
        return $this->pdo->commit();
    }


    public function fetchAll() {
        if (isset($this->pdo_statement))
            return $this->pdo_statement->fetchAll(PDO::FETCH_ASSOC);

        return false;
    }


    public function fetchRow() {
        if (isset($this->pdo_statement))
            return $this->pdo_statement->fetch(PDO::FETCH_ASSOC);

        return false;
    }


    public function getErrorInfo(): array {
        if (isset($this->pdo_statement))
            return $this->pdo_statement->errorInfo();

        return [];
    }


    public function query(String $sql, array $params = []): bool {
        $this->resetPDOStatement();

        $this->pdo_statement = $this->pdo->prepare($sql);

        foreach ($params as $param => &$value) {
            switch (gettype($value)) {
                case 'NULL':
                    $data_type = PDO::PARAM_NULL;
                    break;
                case 'boolean':
                    $data_type = PDO::PARAM_BOOL;
                    break;
                case 'integer':
                    $data_type = PDO::PARAM_INT;
                    break;
                case 'double':
                case 'string':
                    $data_type = PDO::PARAM_STR;
                    break;
                case 'resource':
                    // Only accept file resources
                    if (get_resource_type($value) !== 'file')
                        throw new Exception("Error: Invalid resource type on parameter '{$param}' => {$value}");

                    $data_type = PDO::PARAM_LOB;
                    break;
                default:
                    throw new Exception("Error: Invalid parameter type'{$param}' => {$value}");
            }

            $bind_result = $this->pdo_statement->bindParam($param, $value, $data_type);
            if (!$bind_result)
                throw new Exception("Error: Couldn't bind parameter '{$param}' => {$value}");
        }

        return $this->pdo_statement->execute();
    }


    public function rollBack(): bool {
        return $this->pdo->rollBack();
    }


    private function resetPDOStatement(): bool {
        if (isset($this->pdo_statement))
            return $this->pdo_statement->closeCursor();

        return true;
    }
}