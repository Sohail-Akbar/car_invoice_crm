<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');

// Add Staff
if (isset($_POST['createStaff'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : "";
    if (strlen($password) !== 6 && !$id) returnError("Password must be exactly 6 digits!");

    // ✅ Collect form data
    $data = [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id"  => LOGGED_IN_USER['agency_id'],
        "user_id"    => LOGGED_IN_USER_ID,
        "role_id"    => arr_val($_POST, 'role_id', ""),
        "title"      => arr_val($_POST, 'title', ""),
        "gender"     => arr_val($_POST, 'gender', ""),
        "fname"      => arr_val($_POST, 'fname', ""),
        "lname"      => arr_val($_POST, 'lname', ""),
        "name" => arr_val($_POST, 'fname', "") . " " . arr_val($_POST, 'lname', ""),
        "email"      => arr_val($_POST, 'email', ""),
        "contact"    => arr_val($_POST, 'contact', ""),
        "address"    => arr_val($_POST, 'address', ""),
        "postcode"   => arr_val($_POST, 'postcode', ""),
        "city"       => arr_val($_POST, 'city', ""),
        "type"       => "staff",
        "verify_status" => 1,
        "image"      => "avatar.png"
    ];
    if (!empty($password)) {
        $data["password"] =  password_hash($password, PASSWORD_BCRYPT);
    }

    // ✅ Prevent duplicate email (for new records)
    if (!$id && !empty($data['email'])) {
        $exists = $db->select_one('users', 'id', [
            'email' => $data['email'],
        ]);

        if ($exists) {
            returnError("Email already exists. Please use another one.");
        }
    }

    // ✅ Insert or Update
    if ($id) {
        $save = $db->update("users", $data, [
            "id" => $id,
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id" => LOGGED_IN_USER['agency_id']
        ]);
        $message = "Staff updated successfully";
    } else {
        // Generate a unique token
        $token = bin2hex(random_bytes(16)); // 32-character token
        $data['verify_token'] = $token;

        $save = $db->insert("users", $data);

        if ($save) {
            // Send email to staff to set password
            // $_tc_email->send([
            //     'template' => 'set_password', // create this email template
            //     'to' => $data['email'],
            //     'to_name' => $data['fname'] . " " . $data['lname'],
            //     'subject' => 'Set Your Account Password',
            //     'vars' => [
            //         'name' => $data['fname'],
            //         'set_password_link' => SITE_URL . "/set-password.php?token=" . $token
            //     ]
            // ]);
            $fullName = $data['name'];
            $email = $data['email'];
            $password = $password; // saved password (hashed nahi bhejna!)

            // Email body bana lo
            $emailBody = "
    <h3>Welcome to " . SITE_NAME . "</h3>
    <p>Dear <strong>$fullName</strong>,</p>
    <p>Your account has been created successfully.</p>

    <h4>Login Details</h4>
    <p><strong>Email:</strong> $email</p>
    <p><strong>Password:</strong> $password</p>
    
    <br><br>
    <p>If you have any questions, feel free to contact us.</p>
";

            // Email data create
            $emailData = [
                'to' => "sohailakbar3324@gmail.com",
                // 'to' => $email,
                'to_name' => $fullName,
                'subject' => 'Your Staff Login Details - ' . SITE_NAME,
                'body' => $emailBody
            ];

            // Send email
            $mailStatus = $_tc_email->sendEmailTo($emailData);
        }

        $message = "Staff added successfully";
    }

    // ✅ Response
    if ($save) {
        $staffs = $db->select("users", "*", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id"  => LOGGED_IN_USER['agency_id'],
            "is_active" => 1,
            "type" => "staff"
        ]);

        returnSuccess($message, [
            "redirect" => "view-staff",
            "staff" => $staffs
        ]);
    } else {
        returnError("Error saving staff. Please try again.");
    }
}


// Mark Task Done
if (isset($_POST['markTaskDone'])) {
    $cs_id = intval($_POST['cs_id']);

    if ($cs_id) {
        // Update the customer_staff record as completed
        $update = $db->update("customer_staff", [
            "is_active" => 0, // mark as done
            "completed_at" => date("Y-m-d H:i:s")
        ], [
            "id" => $cs_id
        ]);

        if ($update) {
            returnSuccess("Task marked as done", [
                "redirect" => ""
            ]);
        } else {
            returnError("Failed to update task");
        }
    } else {
        returnError("Invalid request");
    }
}




if (isset($_GET['fetchCompletedTasks'])) {

    $draw   = intval($_POST['draw']);
    $start  = intval($_POST['start']);
    $length = intval($_POST['length']);
    $searchValue = $_POST['search']['value'] ?? '';

    $staff_id   = (int) LOGGED_IN_USER_ID;
    $company_id = (int) LOGGED_IN_USER['company_id'];
    $agency_id  = (int) LOGGED_IN_USER['agency_id'];

    // =======================
    // MAIN DATA QUERY
    // =======================
    $sql = "
        SELECT 
            cs.id,
            cs.completed_at,
            cs.is_active,
            u.fname,
            u.lname,
            i.invoice_no,
            i.id AS invoice_id,
            MIN(cch.make) AS make,
            MIN(cch.model) AS model,
            MIN(cch.reg_number) AS reg_number
        FROM customer_staff cs
        INNER JOIN users u ON cs.customer_id = u.id
        INNER JOIN invoices i ON cs.invoice_id = i.id
        INNER JOIN customer_car_history cch 
            ON cch.customer_id = u.id 
            AND cch.is_active = 1
        WHERE cs.staff_id = $staff_id
          AND cs.company_id = $company_id
          AND cs.agency_id = $agency_id
          AND cs.is_active = 0
          AND cs.completed_at IS NOT NULL
    ";

    // 🔍 Search filter
    if (!empty($searchValue)) {
        $searchValue = addslashes($searchValue);
        $sql .= "
            AND (
                u.fname LIKE '%$searchValue%' 
                OR u.lname LIKE '%$searchValue%' 
                OR i.invoice_no LIKE '%$searchValue%' 
                OR cch.reg_number LIKE '%$searchValue%'
            )
        ";
    }

    // 🔐 Prevent duplicate tasks
    $sql .= " GROUP BY cs.invoice_id ";

    // =======================
    // TOTAL RECORDS (FIXED)
    // =======================
    $countSql = "
        SELECT COUNT(DISTINCT cs.invoice_id) AS total
        FROM customer_staff cs
        WHERE cs.staff_id = $staff_id
          AND cs.company_id = $company_id
          AND cs.agency_id = $agency_id
          AND cs.is_active = 0
          AND cs.completed_at IS NOT NULL
    ";

    $totalQuery   = $db->query($countSql, ["select_query" => true]);
    $totalRecords = $totalQuery[0]['total'] ?? 0;

    // =======================
    // PAGINATION
    // =======================
    $sql .= " ORDER BY cs.completed_at DESC LIMIT $start, $length";
    $tasks = $db->query($sql, ["select_query" => true]);

    // =======================
    // RESPONSE FORMAT
    // =======================
    $data = [];
    foreach ($tasks as $task) {
        $data[] = [
            "id" => $task['id'],
            "customer_name" => $task['fname'] . ' ' . $task['lname'],
            "vehicle" => $task['make'] . ' ' . $task['model'] . ' (' . $task['reg_number'] . ')',
            "invoice_no" => $task['invoice_no'],
            "status" => 'Completed',
            "completed_at" => date("d M Y H:i", strtotime($task['completed_at'])),
            "invoice_id" => $task['invoice_id']
        ];
    }

    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $data
    ]);
    exit;
}
