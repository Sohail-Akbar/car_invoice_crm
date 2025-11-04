<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');


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


// Define MOT Error Data
function getMotErrorMessage($errorCode)
{
    $errorData = [
        "MOTH-UA-01" => [
            "category" => "Authentication & Authorisation Errors",
            "detail" => "MOTH-UA-01 – Authorisation failed",
            "message" => "We couldn’t retrieve MOT data at the moment. Please try again later."
        ],
        "MOTH-FB-01" => [
            "category" => "Authentication & Authorisation Errors",
            "detail" => "MOTH-FB-01 – Permissions, expired/missing token or invalid API key",
            "message" => "There was a connection issue while retrieving MOT records. Our team has been notified."
        ],
        "MOTH-RL-01" => [
            "category" => "Rate Limiting Errors",
            "detail" => "MOTH-RL-01 – Exceeded daily usage limit",
            "message" => "We’ve reached the limit for MOT checks today. Please try again tomorrow."
        ],
        "MOTH-RL-02" => [
            "category" => "Rate Limiting Errors",
            "detail" => "MOTH-RL-02 – Too many requests in short time",
            "message" => "We’re currently processing a high number of MOT checks. Please try again shortly."
        ],
        "MOTH-NP-01" => [
            "category" => "Missing Parameters (System-side error)",
            "detail" => "MOTH-NP-01 – Missing DVLA ID, VRM, etc.",
            "message" => "We couldn’t complete the MOT check due to missing vehicle details. This has been flagged for review."
        ],
        "MOTH-IV-01" => [
            "category" => "Invalid Parameters (System-side error)",
            "detail" => "MOTH-IV-01 – Invalid DVLA ID, VRM, etc.",
            "message" => "We had trouble validating this vehicle’s details. Our team is looking into it."
        ],
        "MOTH-NF-01" => [
            "category" => "Not Found",
            "detail" => "MOTH-NF-01 – Vehicle not found",
            "message" => "No MOT history could be found for this vehicle. It may not have an MOT record yet."
        ],
        "MOTH-NF-02" => [
            "category" => "Not Found",
            "detail" => "MOTH-NF-02 – URL not found",
            "message" => "We encountered an error retrieving MOT data. Please try again later."
        ],
        "MOTH-PC-01" => [
            "category" => "General / Unknown Issues",
            "detail" => "MOTH-PC-01 – Limit of client secrets reached",
            "message" => "We’re currently unable to process MOT checks. Please try again later."
        ],
        "MOTH-BR-01" => [
            "category" => "General / Unknown Issues",
            "detail" => "MOTH-BR-01 – Bad request",
            "message" => "We encountered a technical issue during the MOT check. This has been logged."
        ],
        "MOTH-UN-01" => [
            "category" => "General / Unknown Issues",
            "detail" => "MOTH-UN-01 – Unknown error",
            "message" => "Something went wrong while retrieving MOT data. Please try again later."
        ]
    ];

    return $errorData[$errorCode] ?? null;
}

// Fetch Registeration Car Data
if (isset($_POST['fetchRegistrationCar'])) {
    $regNumber = arr_val($_POST, 'reg', '');
    if (!$regNumber) $regNumber = arr_val($_POST, 'reg_number', '');
    $customerId = arr_val($_POST, 'customer_id', '');
    $customerSave = arr_val($_POST, 'customerSave', '');
    $vehicleData = getVehicleData($regNumber); // Assuming your function returns the array you showed

    // ✅ Check for API error response
    if (isset($vehicleData['response']['errorCode'])) {
        $errorCode = $vehicleData['response']['errorCode'];
        $errorInfo = getMotErrorMessage($errorCode);
        if ($errorInfo) {
            returnError($errorInfo['message']);
        } else {
            returnError("An unknown MOT API error occurred.");
        }
        exit;
    }

    if ($vehicleData && isset($vehicleData['response'])) {
        $vehicleInfo = $vehicleData['response'];

        // Extract main vehicle details
        $mainDetails = [
            'reg_number' => $vehicleInfo['registration'] ?? '',
            'make' => $vehicleInfo['make'] ?? '',
            'model' => $vehicleInfo['model'] ?? '',
            'firstUsedDate' => $vehicleInfo['firstUsedDate'] ?? '',
            'fuelType' => $vehicleInfo['fuelType'] ?? '',
            'primaryColour' => $vehicleInfo['primaryColour'] ?? '',
            'registrationDate' => $vehicleInfo['registrationDate'] ?? '',
            'manufactureDate' => $vehicleInfo['manufactureDate'] ?? '',
            'engineSize' => $vehicleInfo['engineSize'] ?? '',
            'hasOutstandingRecall' => $vehicleInfo['hasOutstandingRecall'] ?? '',
            "expiryDate" => $vehicleInfo['motTests'][0]['expiryDate'] ?? ''
        ];

        if ($customerId) {
            $existingRecord = $db->select("customer_car_history", "*", [
                'reg_number' => $regNumber,
                "company_id" => LOGGED_IN_USER['company_id'],
                "agency_id" => LOGGED_IN_USER['agency_id'],
                "customer_id" => $customerId
            ]);

            $data = [
                "company_id" => LOGGED_IN_USER['company_id'],
                "agency_id" => LOGGED_IN_USER['agency_id'],
                "customer_id" => $customerId,
                "reg_number" =>  $mainDetails['reg_number'],
                "make" => ["encrypt" => $mainDetails['make']],
                "model" => ["encrypt" => $mainDetails['model']],
                "firstUsedDate" => ["encrypt" => $mainDetails['firstUsedDate']],
                "primaryColour" => ["encrypt" => $mainDetails['primaryColour']],
                "registrationDate" => ["encrypt" => $mainDetails['registrationDate']],
                "fuelType" => ["encrypt" => $mainDetails['fuelType']],
                "manufactureDate" => ["encrypt" => $mainDetails['manufactureDate']],
                "engineSize" => ["encrypt" => $mainDetails['engineSize']],
                "hasOutstandingRecall" => ["encrypt" => $mainDetails['hasOutstandingRecall']],
                "ExpiryDate" => ["encrypt" => $mainDetails['expiryDate']]
            ];

            $save = null;
            if (!count($existingRecord)) {
                $save = $db->insert("customer_car_history", $data);
            } else {
                $save =  $db->update("customer_car_history", $data, [
                    'reg_number' => $regNumber,
                    "company_id" => LOGGED_IN_USER['company_id'],
                    "agency_id" => LOGGED_IN_USER['agency_id'],
                    "customer_id" => $customerId
                ]);
            }

            if ($save) {
                returnSuccess("Vehicle data saved successfully.", [
                    "redirect" => "registration-car",
                ]);
            } else {
                returnError('Failed to save vehicle data.');
            }
        } else {
            returnSuccess($vehicleInfo, $mainDetails);
        }
    } else {
        returnError('No data found for this registration.');
    }
}
