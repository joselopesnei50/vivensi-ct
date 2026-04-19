<?php
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require ROOT . '/config/database.php';
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                die(json_encode(['error' => 'Falha na conexão com o banco: ' . $e->getMessage()]));
            }
        }
        return self::$instance;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function select(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function selectOne(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row ?: null;
    }

    public static function insert(string $table, array $data): int
    {
        $cols = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$cols}) VALUES ({$placeholders})";
        self::query($sql, array_values($data));
        return (int)self::getInstance()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(',', array_map(fn($k) => "{$k}=?", array_keys($data)));
        $sql = "UPDATE {$table} SET {$set}, updated_at=NOW() WHERE {$where}";
        $stmt = self::query($sql, [...array_values($data), ...$whereParams]);
        return $stmt->rowCount();
    }
}
