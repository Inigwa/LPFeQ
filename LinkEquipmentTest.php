<?php

namespace lpf\Tests;

use PHPUnit\Framework\TestCase;
use PDO;
use PDOException;

require_once __DIR__ . '/../vendor/autoload.php';

class LinkEquipmentTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // Настраиваем подключение к тестовой базе данных
        $this->pdo = new PDO('pgsql:host=localhost;dbname=test_db', 'user', 'password');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Очищаем тестовые таблицы
        $this->pdo->exec('TRUNCATE equipment_lpf, equipment, lpf RESTART IDENTITY CASCADE');
    }

    public function testCreateLpf()
    {
        $_POST = [
            'action' => 'create_lpf',
            'name' => 'Test LPF',
            'address' => 'Test Address',
            'schedule' => '9:00 - 18:00'
        ];

        ob_start();
        require 'your_php_file.php';
        $output = ob_get_clean();
        $response = json_decode($output, true);

        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['id']);

        $stmt = $this->pdo->prepare('SELECT * FROM lpf WHERE lpf_id = :id');
        $stmt->execute(['id' => $response['id']]);
        $lpf = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($lpf);
        $this->assertEquals('Test LPF', $lpf['lpf_name']);
        $this->assertEquals('Test Address', $lpf['lpf_adress']);
    }

    public function testCreateEquipment()
    {
        $_POST = [
            'action' => 'create_equipment',
            'reg_number' => 123456,
            'operator_name' => 'Test Operator'
        ];

        ob_start();
        require 'your_php_file.php';
        $output = ob_get_clean();
        $response = json_decode($output, true);

        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['id']);

        $stmt = $this->pdo->prepare('SELECT * FROM equipment WHERE eq_id = :id');
        $stmt->execute(['id' => $response['id']]);
        $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($equipment);
        $this->assertEquals(123456, $equipment['registration_number']);
        $this->assertEquals('Test Operator', $equipment['operator_name']);
    }

    public function testLinkEquipment()
    {
        // Создаем тестовые записи
        $stmt = $this->pdo->prepare('INSERT INTO lpf (lpf_name, lpf_adress, lpf_work_schedule) VALUES (:name, :address, :schedule) RETURNING lpf_id');
        $stmt->execute(['name' => 'Test LPF', 'address' => 'Test Address', 'schedule' => '9:00 - 18:00']);
        $lpfId = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare('INSERT INTO equipment (registration_number, operator_name) VALUES (:reg_number, :operator_name) RETURNING eq_id');
        $stmt->execute(['reg_number' => 123456, 'operator_name' => 'Test Operator']);
        $equipmentId = $stmt->fetchColumn();

        // Привязываем технику к ЛПФ
        $_POST = [
            'action' => 'link_equipment',
            'eq_id' => $equipmentId,
            'lpf_id' => $lpfId
        ];

        ob_start();
        require 'your_php_file.php';
        $output = ob_get_clean();
        $response = json_decode($output, true);

        $this->assertTrue($response['success']);

        $stmt = $this->pdo->prepare('SELECT * FROM equipment_lpf WHERE eq_id = :eq_id AND lpf_id = :lpf_id');
        $stmt->execute(['eq_id' => $equipmentId, 'lpf_id' => $lpfId]);
        $link = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($link);
        $this->assertEquals($equipmentId, $link['eq_id']);
        $this->assertEquals($lpfId, $link['lpf_id']);
    }

    public function testDeleteLpf()
    {
        // Создаем тестовую запись
        $stmt = $this->pdo->prepare('INSERT INTO lpf (lpf_name, lpf_adress, lpf_work_schedule) VALUES (:name, :address, :schedule) RETURNING lpf_id');
        $stmt->execute(['name' => 'Test LPF', 'address' => 'Test Address', 'schedule' => '9:00 - 18:00']);
        $lpfId = $stmt->fetchColumn();

        // Удаляем запись
        $_POST = [
            'action' => 'delete_lpf',
            'id' => $lpfId
        ];

        ob_start();
        require 'your_php_file.php';
        $output = ob_get_clean();
        $response = json_decode($output, true);

        $this->assertTrue($response['success']);

        $stmt = $this->pdo->prepare('SELECT * FROM lpf WHERE lpf_id = :id');
        $stmt->execute(['id' => $lpfId]);
        $lpf = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEmpty($lpf);
    }
}

