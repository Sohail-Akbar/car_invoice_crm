<?php
require_once('includes/db.php');

// Vehicle Registration Number
$vehicleReg = isset($_GET['reg']) ? strtoupper(trim($_GET['reg'])) : '';

if (empty($vehicleReg)) {
    die(json_encode(["error" => "Please provide ?reg=VEHICLE_REG parameter"]));
}

function getAccessToken()
{
    $tokenUrl = "https://login.microsoftonline.com/a455b827-244f-4c97-b5b4-ce5d13b4d00c/oauth2/v2.0/token";

    $data = [
        'grant_type' => 'client_credentials',
        'client_id' => MOT_HISTORY_CLIENT_ID,
        'client_secret' => MOT_HISTORY_CLIENT_SECRET,
        'scope' => 'https://tapi.dvsa.gov.uk/.default'
    ];

    $ch = curl_init($tokenUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: Mozilla/5.0 (compatible; DVSA-API-Client/1.0)'
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ["error" => "CURL Error: " . $error];
    }

    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['access_token'])) {
        return $result['access_token'];
    } else {
        return ["error" => "Token error: " . ($result['error_description'] ?? 'Unknown error')];
    }
}

function getMotHistory($vehicleReg)
{
    // Small delay to avoid rate limiting
    usleep(500000); // 0.5 second

    $tokenResult = getAccessToken();

    if (is_array($tokenResult) && isset($tokenResult['error'])) {
        return ["error" => "Token Error: " . $tokenResult['error']];
    }

    $accessToken = $tokenResult;

    $apiUrl = "https://history.mot.api.gov.uk/v1/trade/vehicles/registration/" . urlencode($vehicleReg);

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => false, // Don't follow redirects
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            "x-api-key: " . MOT_HISTORY_API_KEY,
            "Accept: application/json",
            "Content-Type: application/json",
            "User-Agent: DVSA-API-Client/1.0",
            "Cache-Control: no-cache"
        ],
    ]);

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    curl_close($ch);

    $result = [
        "status" => $statusCode,
        "vehicle_reg" => $vehicleReg
    ];

    if ($curlError) {
        $result["error"] = $curlError;
        $result["response"] = null;
    } else {
        // Check if response is HTML (WAF block) or JSON
        if (strpos($response, '<html') !== false || strpos($response, 'Incapsula') !== false) {
            $result["error"] = "Blocked by WAF/Incapsula";
            $result["response"] = null;
            $result["waf_block"] = true;
        } else {
            $result["response"] = json_decode($response, true);
        }
    }

    return $result;
}

// header('Content-Type: application/json');
// $result = getMotHistory($vehicleReg);
// echo json_encode($result, JSON_PRETTY_PRINT);


function getVehicleData($reg)
{
    $url = "https://devmedqon.com/test/test.php?reg=" . urlencode($reg);

    // Get response from URL
    $response = file_get_contents($url);

    if ($response === FALSE) {
        return null; // error handling
    }

    // Decode JSON response (agar JSON ho)
    $data = json_decode($response, true);
    return $data;
}

// Usage
$regNumber = "AB12CDE";
$vehicleData = getVehicleData($regNumber);
print_r($vehicleData);
