<?php

namespace LPF;

use PDO;
use PDOException;
// Подключение к базе данных
require_once 'dbconnection.php';

header('Content-Type: application/json');

try {
    // Запрос на получение данных о технике и связанных ЛПФ
    $query = "
        SELECT 
            equipment.eq_id AS eq_id,
            equipment.registration_number AS registration_number,
            equipment.operator_name AS operator_name,
            COALESCE (
                json_agg(
                    DISTINCT json_build_object(
                        'lpf_id', LPF.lpf_id,
                        'lpf_name', LPF.lpf_name,
                        'lpf_adress', LPF.lpf_adress
                    )::text
                )FILTER (WHERE LPF.lpf_id IS NOT NULL),
                '[]'
            )::json AS linked_lpf
        FROM Equipment
        LEFT JOIN Equipment_lpf ON Equipment.eq_id = Equipment_lpf.eq_id
        LEFT JOIN LPF ON Equipment_lpf.lpf_id = LPF.lpf_id
        GROUP BY Equipment.eq_id
        ORDER BY Equipment.registration_number;
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $equipmentData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $equipmentData = array_map(function($item) {
        // Если linked_lpf = NULL, заменяем на пустой массив
        $item['linked_lpf'] = $item['linked_lpf'] ?? [];
        return $item;
    }, $equipmentData);
    // Формируем ответ в формате JSON
    echo json_encode(["success" => true, "data" => $equipmentData]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Ошибка при получении данных: " . $e->getMessage()]);
}
