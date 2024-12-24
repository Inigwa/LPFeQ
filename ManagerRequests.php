<?php
namespace LPF;

use PDO;
use \PDOException;
require_once 'dbconnection.php';

header('Content-Type: application/json');

$response = [];
$action = $_POST['action'] ?? null;

switch ($action) {
    case 'create_lpf':
        createLpf($pdo);
        break;
    case 'edit_lpf':
        editLpf($pdo);
        break;
    case 'delete_lpf':
        deleteLpf($pdo);
        break;
    case 'create_equipment':
        createEquipment($pdo);
        break;
    case 'edit_equipment':
        editEquipment($pdo);
        break;
    case 'delete_equipment':
        deleteEquipment($pdo);
        break;
    case 'link_equipment':
        linkEquipment($pdo);
        break;
    default:
        $response['error'] = 'Неизвестное действие';
}

echo json_encode($response);

// Создание ЛПФ
function createLpf($pdo) {
    global $response;

    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $schedule = $_POST['schedule'] ?? '';

    if (!$name || !$address) {
        $response['error'] = 'Название и адрес обязательны.';
        return;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO lpf (lpf_name, lpf_adress, lpf_work_schedule) VALUES (:name, :address, :schedule) RETURNING lpf_id');
        $stmt->execute(['name' => $name, 'address' => $address, 'schedule' => $schedule]);
        $response['success'] = true;
        $response['id'] = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $response['error'] = 'Ошибка при создании ЛПФ: ' . $e->getMessage();
    }
}

// Редактирование ЛПФ
function editLpf($pdo) {
    global $response;

    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $schedule = $_POST['schedule'] ?? '';

    if (!$id || !$name || !$address) {
        $response['error'] = 'ID, название и адрес обязательны.';
        return;
    }

    try {
        $stmt = $pdo->prepare('UPDATE lpf SET lpf_name = :name, lpf_adress = :address, lpf_work_schedule = :schedule WHERE lpf_id = :id');
        $stmt->execute(['id' => $id, 'name' => $name, 'address' => $address, 'schedule' => $schedule]);
        $response['success'] = true;
    } catch (PDOException $e) {
        $response['error'] = 'Ошибка при редактировании ЛПФ: ' . $e->getMessage();
    }
}

// Удаление ЛПФ
function deleteLpf($pdo) {
    global $response;

    $id = $_POST['id'] ?? null;

    if (!$id) {
        $response['error'] = 'ID ЛПФ обязателен.';
        return;
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM lpf WHERE lpf_id = :id');
        $stmt->execute(['id' => $id]);
        $response['success'] = true;
    } catch (PDOException $e) {
        $response['error'] = 'Ошибка при удалении ЛПФ: ' . $e->getMessage();
    }
}

// Создание техники
function createEquipment($pdo) {
    global $response;

    $regNumber = $_POST['reg_number'] ?? '';
    $operatorName = $_POST['operator_name'] ?? '';

    if (!$regNumber || !$operatorName) {
        $response['error'] = 'Регистрационный номер и имя оператора обязательны.';
        return;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO equipment (registration_number, operator_name) VALUES (:reg_number, :operator_name) RETURNING eq_id');
        $stmt->execute(['reg_number' => $regNumber, 'operator_name' => $operatorName]);
        $response['success'] = true;
        $response['id'] = $stmt->fetchColumn();
    } catch (PDOException $e) {
        $response['error'] = 'Ошибка при создании техники: ' . $e->getMessage();
    }
}

// Редактирование техники
function editEquipment($pdo) {
    global $response;

    $id = $_POST['id'] ?? null;
    $regNumber = $_POST['reg_number'] ?? '';
    $operatorName = $_POST['operator_name'] ?? '';

    if (!$id || !$regNumber || !$operatorName) {
        $response['error'] = 'ID, регистрационный номер и имя оператора обязательны.';
        return;
    }

    try {
        $stmt = $pdo->prepare('UPDATE equipment SET registration_number = :reg_number, operator_name = :operator_name WHERE eq_id = :id');
        $stmt->execute(['id' => $id, 'reg_number' => $regNumber, 'operator_name' => $operatorName]);
        $response['success'] = true;
    } catch (PDOException $e) {
        $response['error'] = 'Ошибка при редактировании техники: ' . $e->getMessage();
    }
}

// Удаление техники
function deleteEquipment($pdo) {
    global $response;

    $id = $_POST['id'] ?? null;

    if (!$id) {
        $response['error'] = 'ID техники обязателен.';
        return;
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM equipment WHERE eq_id = :id');
        $stmt->execute(['id' => $id]);
        $response['success'] = true;
    } catch (PDOException $e) {
        $response['error'] = 'Ошибка при удалении техники: ' . $e->getMessage();
    }
}

// Привязка техники к ЛПФ
function linkEquipment($pdo) {
    global $response;

    $equipmentId = $_POST['eq_id'] ?? null;
    $lpfId = $_POST['lpf_id'] ?? null;

    if (!$equipmentId || !$lpfId) {
        $response['error'] = 'ID техники и ЛПФ обязательны.';
        return;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO equipment_lpf (eq_id, lpf_id) VALUES (:eq_id, :lpf_id) ON CONFLICT DO NOTHING');
        $stmt->execute(['eq_id' => $equipmentId, 'lpf_id' => $lpfId]);
        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Запись уже существует или ошибка в данных.';
        }
    } catch (PDOException $e) {
        $response['error'] = 'Ошибка при привязке техники к ЛПФ: ' . $e->getMessage();
    }
}
