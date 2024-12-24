<?php
namespace LPF\tests;

use LPF\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use PDO;
use PDOException;

require_once __DIR__ . '/../vendor/autoload.php';

class DatabaseConnectionTest extends TestCase
{
    public function testDatabaseConnection()
    {
        // Убедитесь, что подключение работает
        try {
            $pdo = new PDO("pgsql:host=localhost;dbname=postgres", "postgres", "4455");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->assertInstanceOf(PDO::class, $pdo);
        } catch (PDOException $e) {
            $this->fail("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }
}

