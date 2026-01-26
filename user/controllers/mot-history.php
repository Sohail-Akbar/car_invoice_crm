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


// Fetch all Customer vehicle
if (isset($_POST['getCustomersVehicleData'])) {
    $customer_id = arr_val($_POST, 'customer_id', 0);
    $reg = arr_val($_POST, 'reg', '');

    // Vehicle
    $condition = [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id"  => LOGGED_IN_USER['agency_id'],
    ];
    if ($customer_id) {
        $condition['customer_id'] = $customer_id;
    }
    if ($reg) {
        $condition['reg_number'] = $reg;
    }


    $customer_vehicles = $db->select("customer_car_history", "*", $condition, [
        "order_by" => "id desc"
    ]);

    $customer_data = [];
    if ($customer_vehicles) {
        foreach ($customer_vehicles as $vehicle) {
            $customer = $db->select_one("users", "fname,lname,id,contact,email", [
                "company_id" => LOGGED_IN_USER['company_id'],
                "agency_id"  => LOGGED_IN_USER['agency_id'],
                "is_active"  => 1,
                "type"       => "customer",
                "id" => $vehicle['customer_id']
            ], [
                "order_by" => "id desc"
            ]);
            array_push($customer_data, $customer);
        }
    }
    if ($customer_vehicles) {
        returnSuccess([
            "customers" => $customer_data,
            "customer_vehicles" => $customer_vehicles
        ], [
            "search_by" => true
        ]);
    } else {
        returnError("No Vehicle Record found.");
    }
}



// Fetch Registeration Car Data
if (isset($_POST['fetchRegistrationCar'])) {
    // search by vehicle id
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

        $firstUsedDate = DateTime::createFromFormat('d-m-Y', $vehicle_data['firstUsedDate'])->format('Y-m-d');
        $registrationDate = DateTime::createFromFormat('d-m-Y', $vehicle_data['registrationDate'])->format('Y-m-d');
        $expiryDate = DateTime::createFromFormat('d-m-Y', $vehicle_data['expiryDate'])->format('Y-m-d');
        $manufactureDate = DateTime::createFromFormat('d-m-Y', $vehicle_data['manufactureDate'])->format('Y-m-d');

        $data = [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "customer_id" => $customer_id,
            "reg_number" => $vehicle_data['reg_number'],
            "make" =>  $vehicle_data['make'] ?? '',
            "model" =>  $vehicle_data['model'] ?? '',
            "firstUsedDate" =>  $firstUsedDate ?? '',
            "primaryColour" =>  $vehicle_data['primaryColour'] ?? '',
            "registrationDate" =>  $registrationDate ?? '',
            "fuelType" =>  $vehicle_data['fuelType'] ?? '',
            "manufactureDate" =>  $manufactureDate ?? '',
            "engineSize" =>  $vehicle_data['engineSize'] ?? '',
            "hasOutstandingRecall" =>  $vehicle_data['hasOutstandingRecall'] ?? '',
            "expiryDate" =>  $expiryDate ?? '',
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
                "redirect" => "invoice?customer_id=$customer_id&vehicle_id=$save",
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

        $customer = $db->select_one("users", "*", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id'],
            "id" => $existingRecord['customer_id'],
            "type" => "customer",
            "is_active" => 1
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

        $firstUsedDate = !empty($vehicleInfo['firstUsedDate'])
            ? (new DateTime($vehicleInfo['firstUsedDate']))->format('d-m-Y')
            : null;
        $registrationDate = !empty($vehicleInfo['registrationDate'])
            ? (new DateTime($vehicleInfo['registrationDate']))->format('d-m-Y')
            : null;
        $manufactureDate = !empty($vehicleInfo['manufactureDate'])
            ? (new DateTime($vehicleInfo['manufactureDate']))->format('d-m-Y')
            : null;
        $expiryDate = !empty($vehicleInfo['motTests'][0]['expiryDate'])
            ? (new DateTime($vehicleInfo['motTests'][0]['expiryDate']))->format('d-m-Y')
            : null;


        // Extract main vehicle details
        $mainDetails = [
            'reg_number' => $vehicleInfo['registration'] ?? '',
            'make' => $vehicleInfo['make'] ?? '',
            'model' => $vehicleInfo['model'] ?? '',
            'firstUsedDate' => $firstUsedDate ?? '',
            'fuelType' => $vehicleInfo['fuelType'] ?? '',
            'primaryColour' => $vehicleInfo['primaryColour'] ?? '',
            'registrationDate' => $registrationDate ?? '',
            'manufactureDate' => $manufactureDate ?? '',
            'engineSize' => $vehicleInfo['engineSize'] ?? '',
            'hasOutstandingRecall' => $vehicleInfo['hasOutstandingRecall'] ?? '',
            "expiryDate" => $expiryDate ?? ''
        ];

        // save vehicle with customer id
        if ($customerId) {

            // $firstUsedDate = DateTime::createFromFormat('d-m-Y', $mainDetails['firstUsedDate'])->format('Y-m-d');
            $firstUsedDate = null;
            if (!empty($mainDetails['firstUsedDate'])) {
                $dt = DateTime::createFromFormat('d-m-Y', $mainDetails['firstUsedDate']);
                $firstUsedDate = $dt ? $dt->format('Y-m-d') : null;
            }
            $registrationDate = null;
            if (!empty($mainDetails['registrationDate'])) {
                $dt = DateTime::createFromFormat('d-m-Y', $mainDetails['registrationDate']);
                $registrationDate = $dt ? $dt->format('Y-m-d') : null;
            }

            $expiryDate = null;
            if (!empty($mainDetails['expiryDate'])) {
                $dt = DateTime::createFromFormat('d-m-Y', $mainDetails['expiryDate']);
                $expiryDate = $dt ? $dt->format('Y-m-d') : null;
            }

            $manufactureDate = null;
            if (!empty($mainDetails['manufactureDate'])) {
                $dt = DateTime::createFromFormat('d-m-Y', $mainDetails['manufactureDate']);
                $manufactureDate = $dt ? $dt->format('Y-m-d') : null;
            }

            $data = [
                "company_id" => LOGGED_IN_USER['company_id'],
                "agency_id" => LOGGED_IN_USER['agency_id'],
                "customer_id" => $customerId,
                "reg_number" =>  $mainDetails['reg_number'],
                "make" =>  $mainDetails['make'],
                "model" =>  $mainDetails['model'],
                "firstUsedDate" =>  $firstUsedDate,
                "primaryColour" =>  $mainDetails['primaryColour'],
                "registrationDate" =>  $registrationDate,
                "fuelType" =>  $mainDetails['fuelType'],
                "manufactureDate" =>  $manufactureDate,
                "engineSize" =>  $mainDetails['engineSize'],
                "hasOutstandingRecall" =>  $mainDetails['hasOutstandingRecall'],
                "ExpiryDate" =>  $expiryDate
            ];

            $save = $db->insert("customer_car_history", $data);


            if ($save) {
                returnSuccess("Vehicle data saved successfully.", [
                    "redirect" => "invoice?customer_id=$customerId&vehicle_id=$save",
                    "vehicle_data" => [
                        "reg_number" =>  $mainDetails['reg_number'],
                        "make" =>  $mainDetails['make'],
                        "model" =>  $mainDetails['model'],
                        "vehicle_id" => $save
                    ]
                ]);
            } else {
                returnError('Failed to save vehicle data.');
            }
        }
        // fetch vehicle details with save and update only fetch with reg no
        else {
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

    // 🔹 Date conversions for DB
    $firstUsedDate    = dbDateFormat(arr_val($_POST, 'firstUsedDate', ''));
    $registrationDate = dbDateFormat(arr_val($_POST, 'registrationDate', ''));
    $manufactureDate  = dbDateFormat(arr_val($_POST, 'manufactureDate', ''));
    $expiryDate       = dbDateFormat(arr_val($_POST, 'expiryDate', ''));

    $fuelType = arr_val($_POST, 'fuelType', '');
    $primaryColour = arr_val($_POST, 'primaryColour', '');
    $engineSize = arr_val($_POST, 'engineSize', '');
    $hasOutstandingRecall = arr_val($_POST, 'hasOutstandingRecall', '');

    // 🔹 Vehicle reg validation
    $existingRecordReg = $db->select_one("customer_car_history", "id", [
        "reg_number" => $reg_number,
        "is_active"  => 1
    ]);

    if ($existingRecordReg) {
        returnError("A record with this registration number already exists.");
        exit;
    }

    // 🔹 Final data for DB
    $data = [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id"  => LOGGED_IN_USER['agency_id'],
        "customer_id" => $customerId,
        "reg_number" => $reg_number,
        "make" => $make,
        "model" => $model,
        "firstUsedDate" => $firstUsedDate,        // YYYY-MM-DD
        "primaryColour" => $primaryColour,
        "registrationDate" => $registrationDate,  // YYYY-MM-DD
        "fuelType" => $fuelType,
        "manufactureDate" => $manufactureDate,    // YYYY-MM-DD
        "engineSize" => $engineSize,
        "hasOutstandingRecall" => $hasOutstandingRecall,
        "expiryDate" => $expiryDate,               // YYYY-MM-DD
        "is_manual" => 1
    ];

    $save = $db->insert("customer_car_history", $data);

    if ($save) {
        returnSuccess("Vehicle data saved successfully.", [
            "redirect" => "view-registration-vehicle",
            "vehicle_data" => [
                "reg_number" => $reg_number,
                "make" => $make,
                "model" => $model,
                "vehicle_id" => $save
            ]
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



if (isset($_GET['fetchVehicleData'])) {

    $draw = intval($_POST['draw']);
    $start = intval($_POST['start']);
    $length = intval($_POST['length']);
    $searchValue = $_POST['search']['value'];

    $company_id = LOGGED_IN_USER['company_id'];
    $agency_id = LOGGED_IN_USER['agency_id'];

    // Base query
    $sql = "SELECT * FROM customer_car_history WHERE company_id = '$company_id' AND agency_id = '$agency_id' ";

    // Search filter
    if (!empty($searchValue)) {
        $sql .= " AND (reg_number LIKE '%$searchValue%' 
                    OR make LIKE '%$searchValue%' 
                    OR model LIKE '%$searchValue%' 
                    OR engineSize LIKE '%$searchValue%' 
                    OR expiryDate LIKE '%$searchValue%')";
    }

    // Total records (without filter)
    $totalQuery = $db->query("SELECT COUNT(*) as total FROM customer_car_history WHERE company_id = '$company_id' AND agency_id = '$agency_id' ", ["select_query" => true]);
    $totalRecords = $totalQuery[0]['total'];

    // Filtered records (with search)
    $filteredQuery = $db->query(
        "SELECT COUNT(*) as total FROM customer_car_history WHERE company_id = '$company_id' AND agency_id = '$agency_id' " .
            (!empty($searchValue)
                ? " AND (reg_number LIKE '%$searchValue%' 
                    OR make LIKE '%$searchValue%' 
                    OR model LIKE '%$searchValue%' 
                    OR engineSize LIKE '%$searchValue%' 
                    OR expiryDate LIKE '%$searchValue%')"
                : ""),
        ["select_query" => true]
    );
    $filteredRecords = $filteredQuery[0]['total'];

    // Add order & limit
    $sql .= " ORDER BY id DESC LIMIT $start, $length";
    $mot_history = $db->query($sql, ["select_query" => true]);

    $data = [];
    $count = $start + 1;

    foreach ($mot_history as $row) {
        // Fetch customer details
        $customer = $db->select_one("users", "*", [
            "id" => $row['customer_id'],
            "company_id" => $company_id,
            "agency_id" => $agency_id,
            "type" => "customer",
            "is_active" => 1
        ]);

        $email_html = "";
        if (!empty($customer['email'])) $email_html = "<strong>Email:</strong> {$customer['email']}<br>";

        $customerHTML = "<a href='customer-profile?id={$row['customer_id']}'>
                        <strong>Name:</strong> {$customer['title']} {$customer['fname']} {$customer['lname']}<br>
                         {$email_html}
                         <strong>Contact:</strong> {$customer['contact']}
                         </a>";
        $expiryDate_ = displayDateFormat($row['expiryDate']);
        $detailsHTML = "<a href='customer-profile?id={$row['customer_id']}'>
                        <strong>Make:</strong> {$row['make']}<br>
                        <strong>Model:</strong> {$row['model']}<br>
                        <strong>Engine Size:</strong> {$row['engineSize']}<br>
                        <strong>Expiry Date:</strong> {$expiryDate_}
                        </a>";

        $statusClass = ($row['is_active'] == '1') ? 'bg-success' : 'bg-warning text-dark';
        $statusText = ($row['is_active'] == '1') ? 'Active' : 'Inactive';
        $statusHTML = "<span class='text-white p-1 bold small-font $statusClass'>$statusText</span>";

        $data[] = [
            "index" => $count++,
            "customer_details" => $customerHTML,
            "reg_number" => "<a href='customer-profile?id={$row['customer_id']}'>{$row['reg_number']}</a>",
            "details" => $detailsHTML,
            "status" => $statusHTML,
            "id" => $row['id']
        ];
    }

    $response = [
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data
    ];

    echo json_encode($response);
    exit;
}
