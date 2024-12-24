<?php
namespace LPF\Tests;

use \PDO;
use \PDOException;
use PDOStatement;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/vendor/autoload.php';
require_once 'dbconnection.php';
require_once 'ManagerRequests.php';
require_once 'LpfEquipmentData.php';
require_once 'EqulpmentLfpData.php';

class OperatorRequestsTest extends TestCase {
    private $pdo;

    protected function setUp(): void {
        // Подключение к тестовой базе данных
        $this->pdo = new PDO('pgsql:host=localhost;dbname=test_database', 'test_user', 'test_password');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Очистка данных перед каждым тестом
        $this->pdo->exec('TRUNCATE TABLE lpf, equipment, lpf_equipment RESTART IDENTITY CASCADE');
    }

    public function testAddEquipment() {
        $_POST = [
            'action' => 'add_equipment',
            'reg_number' => '123-ABC',
            'operator_name' => 'Operator1'
        ];

        ob_start();
        createEquipment($this->pdo);
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);

        // Проверка наличия данных в базе
        $stmt = $this->pdo->query('SELECT * FROM equipment WHERE reg_number = "123-ABC" AND operator_name = "Operator1"');
        $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($equipment);
        $this->assertEquals('123-ABC', $equipment['reg_number']);
        $this->assertEquals('Operator1', $equipment['operator_name']);
    }

    public function testCreateLpf() {
        $_POST = [
            'action' => 'create_lpf',
            'name' => 'LPF1',
            'address' => 'Address1',
            'schedule' => '9:00-18:00'
        ];

        ob_start();
        createLpf($this->pdo);
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);

        // Проверка наличия данных в базе
        $stmt = $this->pdo->query('SELECT * FROM lpf WHERE name = "LPF1" AND address = "Address1"');
        $lpf = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($lpf);
        $this->assertEquals('LPF1', $lpf['name']);
        $this->assertEquals('Address1', $lpf['address']);
        $this->assertEquals('9:00-18:00', $lpf['schedule']);
    }

    public function testLinkEquipment() {
        // Подготовка тестовых данных
        $this->pdo->exec("INSERT INTO lpf (name, address) VALUES ('LPF1', 'Address1')");

        $_POST = [
            'action' => 'link_equipment',
            'lpf_id' => 1,
            'equipment_data' => [
                ['reg_number' => '123-ABC', 'operator_name' => 'Operator1']
            ]
        ];

        ob_start();
        linkEquipment($this->pdo);
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        $this->assertArrayHasKey('success', $response);
        $this->assertTrue($response['success']);

        // Проверка привязки
        $stmt = $this->pdo->query('SELECT * FROM lpf_equipment WHERE lpf_id = 1');
        $link = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($link);
        $this->assertEquals(1, $link['lpf_id']);
    }

    protected function tearDown(): void {
        // Очистка данных после каждого теста
        $this->pdo->exec('TRUNCATE TABLE lpf, equipment, lpf_equipment RESTART IDENTITY CASCADE');
        $this->pdo = null;
    }
}
?>
