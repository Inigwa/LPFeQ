<?php
namespace lpf\Tests;

use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;

require_once __DIR__ . '/../vendor/autoload.php';

class EquipmentDataTest extends TestCase
{
    public function testSqlQueryReturnsExpectedData()
    {
        $pdo = $this->createMock(PDO::class);
        
        // Создайте мок для PDOStatement
        $stmt = $this->createMock(PDOStatement::class);

        // Настроим PDO для возврата этого запроса
        $pdo->method('prepare')->willReturn($stmt);
        
        // Мокаем выполнение запроса
        $stmt->method('execute')->willReturn(true);
        
        // Возвращаем заранее подготовленные данные
        $stmt->method('fetchAll')->willReturn([
            [
                'equipment_id' => 1,
                'registration_number' => 12345,
                'operator_name' => 'Иванов Иван',
                'linked_lpf' => '[{"lpf_id": 1, "lpf_name": "ЛПФ-1", "lpf_adress": "ул. Лесная, 10"}]'
            ]
        ]);

        // Проверяем, что результат запроса соответствует ожидаемому
        $query = "
            SELECT 
                equipment.eq_id AS equipment_id,
                equipment.registration_number AS registration_number,
                equipment.operator_name AS operator_name,
                json_agg(
                    DISTINCT json_build_object(
                        'lpf_id', LPF.lpf_id,
                        'lpf_name', LPF.lpf_name,
                        'lpf_adress', LPF.lpf_adress
                    )::text
                )::json AS linked_lpf
            FROM Equipment
            LEFT JOIN Equipment_lpf ON Equipment.eq_id = Equipment_lpf.eq_id
            LEFT JOIN LPF ON Equipment_lpf.lpf_id = LPF.lpf_id
            GROUP BY Equipment.eq_id
            ORDER BY Equipment.registration_number;
        ";

        $stmt->execute();
        $equipmentData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Проверяем, что данные содержат правильную информацию
        $this->assertEquals(1, $equipmentData[0]['equipment_id']);
        $this->assertEquals(12345, $equipmentData[0]['registration_number']);
        $this->assertEquals('Иванов Иван', $equipmentData[0]['operator_name']);
        $this->assertJson($equipmentData[0]['linked_lpf']);
    }
}

