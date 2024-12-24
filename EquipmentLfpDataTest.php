<?php
namespace lpf\Tests;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

class EquipmentLfpDataTest extends TestCase
{
    public function testEquipmentLfpDataReturnsJson()
    {
        // Эмулируем запрос
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // Включаем вывод в буфер для захвата ответа
        ob_start();

        // Включаем PHP-скрипт
        require_once 'EqulpmentLfpData.php';

        // Получаем вывод из буфера
        $output = ob_get_clean();

        // Проверяем, что это корректный JSON
        $this->assertJson($output);

        // Преобразуем JSON-ответ в массив
        $data = json_decode($output, true);

        // Проверяем, что ответ содержит нужные данные
        $this->assertArrayHasKey('success', $data);
        $this->assertArrayHasKey('data', $data);
    }
}

