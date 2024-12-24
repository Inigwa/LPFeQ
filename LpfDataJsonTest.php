<?php
namespace LPF\Tests;

use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;

require_once __DIR__ . '/../vendor/autoload.php';

class LpfDataJsonTest extends TestCase
{
    public function testLpfDataReturnsJson()
    {
        // Мокаем запрос к базе данных
        $pdo = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);

        $pdo->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchAll')->willReturn([
            [
                'lpf_id' => 1,
                'lpf_name' => 'ЛПФ-1',
                'lpf_adress' => 'ул. Лесная, 10',
                'lpf_work_schedule' => '8:00-18:00',
                'linked_equipment' => '[{"equipment_id":1,"reg_number":12345,"operator_name":"Иванов Иван"}]'
            ]
        ]);

        // Подключаем скрипт
        ob_start();
        require 'path/to/your/script.php';
        $output = ob_get_clean();

        // Проверяем, что это корректный JSON
        $this->assertJson($output);

        // Преобразуем JSON в массив
        $data = json_decode($output, true);

        // Проверяем структуру JSON-ответа
        $this->assertArrayHasKey('success', $data);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('data', $data);

        // Проверяем данные в массиве
        $this->assertCount(1, $data['data']);
        $this->assertEquals(1, $data['data'][0]['lpf_id']);
        $this->assertEquals('ЛПФ-1', $data['data'][0]['lpf_name']);
    }
}

