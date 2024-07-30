<?php
session_start();
require_once('../db_connection.php');

function deleteFromGoogleSheet($id, $slot) {
    $sheetUrl = 'https://sheetdb.io/api/v1/98locb0xjprmo/search?id=' . $id;
    $updateUrl = 'https://sheetdb.io/api/v1/98locb0xjprmo/id/' . $id;

    // Fetch current data from Google Sheet
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $sheetUrl,
        CURLOPT_RETURNTRANSFER => true,
    ]);
    $result = curl_exec($ch);
    curl_close($ch);

    $existingData = json_decode($result, true);

    if (!empty($existingData)) {
        $data = $existingData[0];
        if ($slot === 'slot1') {
            $data['medicine_alert_time'] = '';
            $data['ocr_scans_text'] = '';
            $data['access_token'] = '';
            $data['ocr_image_data'] = '';
            $data['image'] = '';
        } elseif ($slot === 'slot2') {
            $data['medicine_alert_time2'] = '';
            $data['ocr_scans_text2'] = '';
            $data['access_token2'] = '';
            $data['ocr_image_data2'] = '';
            $data['image2'] = '';
        }

        // Update Google Sheet
        $payload = json_encode($data);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $updateUrl,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200 && $httpcode !== 201) {
            error_log("Error updating Google Sheet. HTTP status code: $httpcode. Response: $result");
            return false;
        }

        return true;
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['slot'])) {
    $ocr_id = $_POST['id'];
    $slot = $_POST['slot'];

    try {
        if ($slot === 'slot1') {
            $stmt = $db->prepare("
                UPDATE users 
                SET ocr_scans_text = NULL, ocr_image_data = NULL, medicine_alert_time = NULL, access_token = NULL, image = NULL 
                WHERE id = :ocr_id
            ");
        } elseif ($slot === 'slot2') {
            $stmt = $db->prepare("
                UPDATE users 
                SET ocr_scans_text2 = NULL, ocr_image_data2 = NULL, medicine_alert_time2 = NULL, access_token2 = NULL, image2 = NULL 
                WHERE id = :ocr_id
            ");
        }

        $stmt->bindParam(':ocr_id', $ocr_id, PDO::PARAM_INT);
        $stmt->execute();

        // Delete from Google Sheet
        if (deleteFromGoogleSheet($ocr_id, $slot)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update Google Sheet']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit();
}
?>
