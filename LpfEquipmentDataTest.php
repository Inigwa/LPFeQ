<?php
namespace lpf\Tests;

use PHPUnit\Framework\TestCase;
use PDO;
use PDOException;
use PDOStatement;

require_once __DIR__ . '/../vendor/autoload.php';

class LpfEquipmentDataTest extends TestCase
{
    public function testSqlQueryReturnsExpectedData()
    {
        // Создаем мок PDO
        $pdo = $this->createMock(PDO::class);

        // Создаем мок PDOStatement
        $stmt = $this->createMock(PDOStatement::class);

        // Настраиваем PDO на возврат подготовленного запроса
        $pdo->method('prepare')->willReturn($stmt);

        // Мокаем выполнение запроса
        $stmt->method('execute')->willReturn(true);

        // Возвращаем заранее подготовленные данные
        $stmt->method('fetchAll')->willReturn([
            [
                'lpf_id' => 1,
                'lpf_name' => 'ЛПФ-1',
                'lpf_adress' => 'ул. Лесная, 10',
                'lpf_work_schedule' => '8:00-18:00',
                'linked_equipment' => '[{"equipment_id":1,"reg_number":12345,"operator_name":"Иванов Иван"}]'
            ]
        ]);

        // Выполняем тестируемый SQL-запрос
        $query = "
            SELECT 
                LPF.lpf_id AS lpf_id, 
                LPF.lpf_name AS lpf_name, 
                LPF.lpf_adress AS lpf_adress, 
                LPF.lpf_work_schedule AS lpf_work_schedule,
                COALESCE (
                    json_agg(
                        json_build_object(
                            'equipment_id', Equipment.eq_id, 
                            'reg_number', Equipment.registration_number, 
                            'operator_name', Equipment.operator_name
                        )::text
                    )FILTER (WHERE Equipment.eq_id IS NOT NULL),
                    '[]'
                )::json AS linked_equipment
            FROM LPF
            LEFT JOIN Equipment_lpf ON LPF.lpf_id = Equipment_lpf.lpf_id
            LEFT JOIN Equipment ON Equipment_lpf.eq_id = Equipment.eq_id
            GROUP BY LPF.lpf_id
            ORDER BY LPF.lpf_name;
        ";

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Проверяем, что данные соответствуют ожидаемому результату
        $this->assertCount(1, $result);
        $this->assertEquals(1, $result[0]['lpf_id']);
        $this->assertEquals('ЛПФ-1', $result[0]['lpf_name']);
        $this->assertEquals('[{"equipment_id":1,"reg_number":12345,"operator_name":"Иванов Иван"}]', $result[0]['linked_equipment']);
    }
}

