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
    if (isset($_POST['vehicle_id'])) {
        $vehicle_id = intval(arr_val($_POST, 'vehicle_id', 0));
        $customer_id = intval(arr_val($_POST, 'customer_id', 0));

        if (!$vehicle_id || !$customer_id) {
            returnError('Invalid request: missing vehicle_id or customer_id.');
            exit;
        }

        $vehicle_data = $db->select_one("customer_car_history", "*", [
            "id" => $vehicle_id,
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "is_active" => 1
        ]);

        if (!$vehicle_data) {
            returnError('Vehicle not found or access denied.');
            exit;
        }

        $data = [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "customer_id" => $customer_id,
            "reg_number" => $vehicle_data['reg_number'],
            "make" => ["encrypt" => $vehicle_data['make'] ?? ''],
            "model" => ["encrypt" => $vehicle_data['model'] ?? ''],
            "firstUsedDate" => ["encrypt" => $vehicle_data['firstUsedDate'] ?? ''],
            "primaryColour" => ["encrypt" => $vehicle_data['primaryColour'] ?? ''],
            "registrationDate" => ["encrypt" => $vehicle_data['registrationDate'] ?? ''],
            "fuelType" => ["encrypt" => $vehicle_data['fuelType'] ?? ''],
            "manufactureDate" => ["encrypt" => $vehicle_data['manufactureDate'] ?? ''],
            "engineSize" => ["encrypt" => $vehicle_data['engineSize'] ?? ''],
            "hasOutstandingRecall" => ["encrypt" => $vehicle_data['hasOutstandingRecall'] ?? ''],
            "expiryDate" => ["encrypt" => $vehicle_data['expiryDate'] ?? ''],
            // preserve manual flag if present, otherwise mark as non-manual
            "is_manual" => isset($vehicle_data['is_manual']) ? $vehicle_data['is_manual'] : 0
        ];

        $save = $db->insert("customer_car_history", $data);

        if ($save) {
            $db->update("customer_car_history", [
                "is_active" => 0
            ], [
                "id" => $vehicle_id
            ]);

            returnSuccess("Vehicle data saved successfully.", [
                "redirect" => "registration-vehicle",
            ]);
        } else {
            returnError("Failed to save vehicle data.");
        }

        exit;
    }

    $regNumber = arr_val($_POST, 'reg', '');
    if (!$regNumber) $regNumber = arr_val($_POST, 'reg_number', '');
    $customerId = arr_val($_POST, 'customer_id', '');
    $customerSave = arr_val($_POST, 'customerSave', '');
    $vehicleData = getVehicleData($regNumber); // Assuming your function returns the array you showed

    // Existing customer vehicle information
    $existingRecord = $db->select_one("customer_car_history", "*", [
        'reg_number' => $regNumber,
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "is_active" => 1
    ]);
    if (empty($existingRecord)) $existingRecord = [];


    if (count($existingRecord)) {
        if ($vehicleData && isset($vehicleData['response'])) $vehicleInfo = $vehicleData['response'];

        $customer = $db->select_one("customers", "*", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "id" => $existingRecord['customer_id']
        ]);

        $data = [
            "vehicleInfo" => isset($vehicleData['response']['errorCode']) ? [] : $vehicleInfo,
            "existingRecord" => $existingRecord,
            "customer" => $customer,
            "isExistingRecord" => true
        ];
        returnSuccess($data);
    }


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

            $save = $db->insert("customer_car_history", $data);


            if ($save) {
                returnSuccess("Vehicle data saved successfully.", [
                    "redirect" => "registration-vehicle",
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


if (isset($_POST['manuallyRegistrationCar'])) {
    $customerId = arr_val($_POST, 'customer_id', '');
    $reg_number = arr_val($_POST, 'reg_number', '');
    $make = arr_val($_POST, 'make', '');
    $model = arr_val($_POST, 'model', '');
    $firstUsedDate = arr_val($_POST, 'firstUsedDate', '');
    $fuelType = arr_val($_POST, 'fuelType', '');
    $primaryColour = arr_val($_POST, 'primaryColour', '');
    $registrationDate = arr_val($_POST, 'registrationDate', '');
    $manufactureDate = arr_val($_POST, 'manufactureDate', '');
    $engineSize = arr_val($_POST, 'engineSize', '');
    $hasOutstandingRecall = arr_val($_POST, 'hasOutstandingRecall', '');
    $expiryDate = arr_val($_POST, 'expiryDate', '');

    // Vehicle reg no validation
    $existingRecordReg = $db->select_one("customer_car_history", "id", [
        "reg_number" =>  $reg_number,
        "is_active" => 1
    ]);

    if ($existingRecordReg) {
        returnError("A record with this registration number already exists. Please check and try again.");
        exit;
    }

    $data = [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "customer_id" => $customerId,
        "reg_number" =>  $reg_number,
        "make" => $make,
        "model" => $model,
        "firstUsedDate" => $firstUsedDate,
        "primaryColour" => $primaryColour,
        "registrationDate" => $registrationDate,
        "fuelType" => $fuelType,
        "manufactureDate" => $manufactureDate,
        "engineSize" => $engineSize,
        "hasOutstandingRecall" => $hasOutstandingRecall,
        "expiryDate" => $expiryDate,
        "is_manual" => 1
    ];

    $save = $db->insert("customer_car_history", $data);

    if ($save) {
        returnSuccess("Vehicle data saved successfully.", [
            "redirect" => "view-registration-vehicle",
        ]);
    } else {
        returnError('Failed to save vehicle data.');
    }
}


// Vehicle Information Update
if (isset($_POST['updateVehicleInformation'])) {
    $id = arr_val($_POST, 'id', '');
    if (!$id) {
        returnError('Invalid request: missing id.');
        exit;
    }

    // Collect POST values
    $reg_number = arr_val($_POST, 'reg_number', '');
    $make = arr_val($_POST, 'make', '');
    $model = arr_val($_POST, 'model', '');
    $firstUsedDate = arr_val($_POST, 'firstUsedDate', '');
    $fuelType = arr_val($_POST, 'fuelType', '');
    $primaryColour = arr_val($_POST, 'primaryColour', '');
    $registrationDate = arr_val($_POST, 'registrationDate', '');
    $manufactureDate = arr_val($_POST, 'manufactureDate', '');
    $engineSize = arr_val($_POST, 'engineSize', '');
    $hasOutstandingRecall = arr_val($_POST, 'hasOutstandingRecall', '');
    $expiryDate = arr_val($_POST, 'expiryDate', '');

    // Verify the record exists and belongs to the logged in company/agency
    $existing = $db->select_one("customer_car_history", "*", [
        "id" => $id,
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id']
    ]);
    if (!$existing) {
        returnError('Record not found or access denied.');
        exit;
    }

    // Check for duplicate registration number (exclude current record)
    if ($reg_number !== '') {
        $conflict = $db->select_one("customer_car_history", "*", [
            "reg_number" => $reg_number,
            "is_active" => 1
        ]);
        if ($conflict && $conflict['id'] != $id) {
            returnError("A record with this registration number already exists. Please check and try again.");
            exit;
        }
    }

    // Prepare update data
    $updateData = [
        "reg_number" => $reg_number,
        "make" => $make,
        "model" => $model,
        "firstUsedDate" => $firstUsedDate,
        "primaryColour" => $primaryColour,
        "registrationDate" => $registrationDate,
        "fuelType" => $fuelType,
        "manufactureDate" => $manufactureDate,
        "engineSize" => $engineSize,
        "hasOutstandingRecall" => $hasOutstandingRecall,
        "expiryDate" => $expiryDate
    ];

    $updated = $db->update("customer_car_history", $updateData, ["id" => $id]);

    if ($updated) {
        returnSuccess("Vehicle information updated successfully.", [
            "redirect" => "view-registration-vehicle",
        ]);
    } else {
        returnError("Failed to update vehicle information.");
    }
}
