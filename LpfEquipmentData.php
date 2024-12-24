<?php

namespace LPF;

use PDO;
use PDOException;
// Подключение к базе данных
require_once 'dbconnection.php';

header('Content-Type: application/json');

try {
    // Запрос на получение данных о ЛПФ и связанной технике
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

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $lpfData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $lpfData = array_map(function($item) {
        // Если linked_lpf = NULL, заменяем на пустой массив
        $item['linked_equipment'] = $item['linked_equipment'] ?? [];
        return $item;
    }, $lpfData);
    echo json_encode(["success" => true, "data" => $lpfData]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Ошибка при получении данных: " . $e->getMessage()]);
}
