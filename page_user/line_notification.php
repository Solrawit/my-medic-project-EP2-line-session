<?php

function sendLineNotification($access_token, $message, $image_url = null) {
    $url = 'https://notify-api.line.me/api/notify';
    $headers = [
        'Authorization: Bearer ' . $access_token,
    ];

    $data = [
        'message' => $message,
    ];

    if ($image_url) {
        $data['imageThumbnail'] = $image_url;
        $data['imageFullsize'] = $image_url;
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
    ]);

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        error_log("Error sending LINE notification. HTTP status code: $httpcode. Response: $result");
        return false;
    }

    return true;
}

function sendToGoogleSheet($data, $sheetUrl) {
    $id = $data['id'];
    $check_url = "$sheetUrl/search?id=$id";
    $update_url = "$sheetUrl/id/$id";
    $insert_url = $sheetUrl;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $check_url,
        CURLOPT_RETURNTRANSFER => true,
    ]);

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $existingData = json_decode($result, true);

    if (!empty($existingData)) {
        // Update existing entry
        $payload = json_encode($data);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $update_url,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);
    } else {
        // Insert new entry
        $payload = json_encode([$data]);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $insert_url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);
    }

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200 && $httpcode !== 201) {
        error_log("Error sending data to Google Sheet. HTTP status code: $httpcode. Response: $result");
        return false;
    }

    return true;
}
?>
