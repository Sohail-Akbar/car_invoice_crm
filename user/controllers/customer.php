<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');

if (isset($_POST['fetchCarInfo'])) {
    $car_id = $_POST['car_id'];
    $customer_id = $_POST['customer_id'];

    $query_sql = "
    SELECT 
        c.id AS car_id,
        c.make,
        c.model,
        c.reg_number,
        i.id AS invoice_id,
        i.invoice_no,
        i.invoice_date,
        i.status,
        i.total_amount,
        i.paid_amount,
        i.due_amount,
        i.pdf_file,
        i.proforma,
        i.notes,
        s.text AS service_name,
        s.amount AS service_amount
    FROM customer_car_history AS c
    LEFT JOIN invoices AS i ON c.id = i.mot_id AND proforma = 0
    LEFT JOIN invoice_items AS it ON i.id = it.invoice_id
    LEFT JOIN services AS s ON FIND_IN_SET(s.id, it.services_id)
    WHERE c.customer_id = '$customer_id' AND c.id = '$car_id'
    ORDER BY i.invoice_date DESC
    ";

    $rows = $db->query($query_sql, ["select_query" => true]);

    if (empty($rows)) {
        echo "<p class='text-muted'>No service history found for this car.</p>";
        exit;
    }

    // Get all available staff for assignment
    $all_staff = $db->select("users", "*", [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "type" => "staff",
    ]);

    // Group invoices
    $grouped = [];
    foreach ($rows as $row) {
        $invoice_id = $row['invoice_id'];

        if (!isset($grouped[$invoice_id])) {
            $grouped[$invoice_id] = [
                "car" => [
                    "id" => $row['car_id'],
                    "make" => $row["make"],
                    "model" => $row["model"],
                    "reg_number" => $row["reg_number"]
                ],
                "invoice" => [
                    "invoice_no" => $row["invoice_no"],
                    "invoice_date" => $row["invoice_date"],
                    "status" => $row["status"],
                    "total_amount" => $row["total_amount"],
                    "paid_amount" => $row["paid_amount"],
                    "due_amount" => $row["due_amount"],
                    "pdf_file" => $row["pdf_file"],
                    "notes" => $row["notes"]
                ],
                "services" => []
            ];
        }

        $grouped[$invoice_id]["services"][] = [
            "name" => $row["service_name"],
            "amount" => $row["service_amount"]
        ];
    }
    // Display cards
    foreach ($grouped as $invoice_id => $data) {
        $car = $data["car"];
        $invoice = $data["invoice"];
        $modal_id = "addStaffModal_" . $invoice_id;
        $pay_modal_id = "paymentModal_" . $invoice_id;

        // Fetch assigned staff for this invoice
        $assigned_staff = $db->query("
            SELECT cs.id AS cs_id, cs.is_active, st.title, st.fname, st.lname, st.email,st.type
            FROM customer_staff cs
            INNER JOIN users st ON cs.staff_id = st.id AND st.type = 'staff'
            WHERE cs.customer_id = '$customer_id' AND cs.invoice_id = '$invoice_id' AND cs.is_active = 1
        ", ["select_query" => true]);
        $assigned_staff_ids = [];


        $pdf_html = "";
        if (!empty($invoice['pdf_file']) && file_exists(_DIR_ . "/uploads/invoices/{$invoice['pdf_file']}")) {
            $pdf_html = "<a href='../uploads/invoices/{$invoice['pdf_file']}' target='_blank' class='btn btn-outline-primary btn-sm view-invoice-btn'><i class='fas fa-file-pdf mr-1'></i> View Invoice PDF</a>";
        } else {
            $pdf_html = "<span class='text-muted small'><i class='fas fa-exclamation-circle'></i> No PDF file available</span>";
        }

        $add_staff_btn_html = "";
        if (LOGGED_IN_USER['type'] === "agency") {
            $add_staff_btn_html = "<a class='text-white add-staff' data-toggle='modal' data-target='#{$modal_id}' style='cursor:pointer;'>
                        <i class='fas fa-user-plus mr-1'></i> Add Staff
                    </a>";
        }

        $update_payment_btn_html = "";

        if (LOGGED_IN_USER['type'] === "agency") {
            if ($invoice['due_amount'] > 0 && $invoice['status'] !== 'cancelled') {
                $update_payment_btn_html = <<<HTML
<a class="text-white update-payment ml-5" data-toggle="modal" data-target="#{$pay_modal_id}" style="cursor:pointer;">
    <i class="fas fa-pound-sign mr-1"></i> Update Payment
</a>
HTML;
            } else if (isset($invoice['proforma']) && $invoice['proforma'] == 0) {
                $update_payment_btn_html = <<<HTML
<span class="text-light ml-5 small">
    <i class="fas fa-check-circle"></i> Payment Cleared
</span>
HTML;
            }
        }


        echo "
        <div class='card mb-3 shadow-sm mt-5'>
            <div class='card-header bg-info d-flex justify-content-between align-items-center'>
                <div><strong>{$car['make']} {$car['model']}</strong> ({$car['reg_number']})</div>
                <div class='action-btns'>
                    {$add_staff_btn_html}
                    {$update_payment_btn_html}
                </div>
            </div>

            <div class='card-body'>
                <div class='pull-away'>
                    <div>
                        <h6>
                            Invoice <strong>#{$invoice['invoice_no']}</strong> 
                            <span class='badge'>{$invoice['status']}</span>
                        </h6>
                    </div>
                    {$pdf_html}
                </div>
                <p class='small text-muted mb-2'>Date: {$invoice['invoice_date']}</p>

                <table class='table table-sm table-bordered mb-3'>
                    <thead class='table-secondary'>
                        <tr><th>Service</th><th>Amount (" . _CURRENCY_SYMBOL . ")</th></tr>
                    </thead>
                    <tbody>";
        foreach ($data["services"] as $srv) {
            echo "<tr><td>{$srv['name']}</td><td>{$srv['amount']}</td></tr>";
        }
        echo "
                    </tbody>
                </table>

                <div class='d-flex justify-content-between mb-3 payment-details'>
                    <span><strong>Total:</strong> " . _CURRENCY_SYMBOL . "{$invoice['total_amount']}</span>
                    <span><strong>Paid:</strong> " . _CURRENCY_SYMBOL . "{$invoice['paid_amount']}</span>
                    <span><strong>Due:</strong> " . _CURRENCY_SYMBOL . "{$invoice['due_amount']}</span>
                </div>";

        // 🔹 Show Invoice Note
        if (!empty($invoice['notes'])) {
            echo "
    <div class='invoice-note border rounded p-2 bg-light mb-3'>
        <strong><i class='fas fa-sticky-note mr-1 text-info'></i> Note:</strong>
        <p class='mb-0 text-muted'>{$invoice['notes']}</p>
    </div>";
        }



        // 🔹 Show assigned staff
        echo "<div class='assigned-staff-box mb-3'>";
        if (!empty($assigned_staff)) {
            echo "<h6><i class='fas fa-users mr-1'></i> Assigned Staff:</h6><ul class='list-inline'>";
            foreach ($assigned_staff as $st) {
                array_push($assigned_staff_ids, $st['cs_id']);
                echo "
                <li class='list-inline-item border rounded px-2 py-1 bg-light mb-1'>
                    {$st['title']} {$st['fname']} {$st['lname']}
                    <span class='text-muted small'>({$st['email']})</span>
                    <a href='#' class='text-danger ml-2 tc-delete-btn' data-target='{$st['cs_id']}' data-action='customer_staff' data-parent='.list-inline-item' title='Remove'>
                        <i class='fas fa-times-circle'></i>
                    </a>
                </li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='text-muted small mb-0'>No staff assigned yet.</p>";
        }
        echo "</div>";

        echo "</div></div>";

        // 🔹 Add Staff Modal
        echo "
        <div class='modal fade' id='{$modal_id}' tabindex='-1' role='dialog' aria-hidden='true'>
            <div class='modal-dialog modal-lg' role='document'>
                <div class='modal-content'>
                    <div class='modal-header bg-info text-white'>
                        <h5 class='modal-title'><i class=\"fas fa-user-plus mr-2\"></i> Assign Staff for {$car['make']} ({$car['reg_number']})</h5>
                        <button type='button' class='close text-white' data-dismiss='modal' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>

                    <div class='modal-body bg-white'>
                        <form action='assigned-staff' method='POST' class='ajax_form' data-callback='assignedStaffCB'>
                            <div class='form-group'>
                                <label class='font-weight-bold'>Select Staff Member</label>
                                <select name='staff_id[]' class='form-control select2' multiple='multiple' required>";
        foreach ($all_staff as $staff) {
            echo "<option  value='{$staff['id']}'>{$staff['title']} {$staff['fname']} {$staff['lname']} ({$staff['email']})</option>";
        }
        echo "
                                </select>
                            </div>

                            <input type='hidden' name='customer_id' value='{$customer_id}'>
                            <input type='hidden' name='invoice_id' value='{$invoice_id}'>
                            <input type='hidden' name='vehicle_id' value='{$car['id']}'>
                            <input type='hidden' name='assign_staff' value='true'>

                            <div class='text-right'>
                                <button type='submit' class='btn btn-primary'>
                                    <i class='fas fa-check-circle mr-1'></i> Assign Staff
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>";
        // 🔹 Payment Update Modal
        echo "
        <div class='modal fade' id='{$pay_modal_id}' tabindex='-1' role='dialog' aria-hidden='true'>
            <div class='modal-dialog modal-md' role='document'>
                <div class='modal-content'>
                    <div class='modal-header bg-info text-white'>
                        <h5 class='modal-title'><i class=\"fas fa-pound-sign mr-2\"></i> Update Payment</h5>
                        <button type='button' class='close text-white' data-dismiss='modal' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>

                    <div class='modal-body bg-white'>
                         <form action='invoice' method='POST' class='ajax_form'>
                            <div class='form-group'>
                                <label>Total Amount (" . _CURRENCY_SYMBOL . ")</label>
                                <input type='number' name='total_amount' class='form-control' value='{$invoice['total_amount']}' readonly>
                            </div>

                            <div class='form-group'>
                                <label>Already Paid (" . _CURRENCY_SYMBOL . ")</label>
                                <input type='number' name='paid_amount_old' class='form-control' value='{$invoice['paid_amount']}' readonly>
                            </div>

                            <div class='form-group'>
                                <label>Status</label>
                                <select name='status' class='form-control' required>
                                    <option value='unpaid' " . ($invoice['status'] == 'unpaid' ? 'selected' : '') . ">Unpaid</option>
                                    <option value='partial' " . ($invoice['status'] == 'partial' ? 'selected' : '') . ">Partial</option>
                                    <option value='paid' " . ($invoice['status'] == 'paid' ? 'selected' : '') . ">Paid</option>
                                </select>
                            </div>

                            <div class='form-group'>
                                <label>Add Payment (" . _CURRENCY_SYMBOL . ")</label> (Due: {$invoice['due_amount']})
                                <input type='number' name='new_payment' class='form-control' placeholder='Enter payment amount' min='0' required>
                            </div>

                            <input type='hidden' name='invoice_id' value='{$invoice_id}'>
                            <input type='hidden' name='customer_id' value='{$customer_id}'>
                            <input type='hidden' name='updateInvoicePayment' value='true'>
                            <div class='text-right'>
                                <button type='submit' class='btn btn-info'>
                                    <i class='fas fa-check-circle mr-1'></i> Update Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>";
    }
}


// Update Customer
if (isset($_POST['updateCustomerInfo'])) {
    $title = $_POST['title'];
    $gender = $_POST['gender'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $customer_id = $_POST['customer_id'];

    $update = $db->update("users", [
        "title"    => $title,
        "gender"   => $gender,
        "fname"    => $fname,
        "lname"    => $lname,
        "email"    => $email,
        "contact"  => $contact,
        "address"  => $address,
        "postcode" => $postcode,
        "city"     => $city
    ], [
        "id" => $customer_id,
        "is_active" => 1,
        "type" => "customer"
    ]);

    if ($update) {
        returnSuccess("Customer updated successfully");
    } else {
        returnError("Failed to update customer");
    }
    exit;
}

if (isset($_POST['addCustomerNotes'])) {

    $customer_id = intval($_POST['customer_id']);
    $note        = trim($_POST['note']);

    // Basic validation
    if (empty($note)) {
        returnError("Please enter a note before saving.");
    }

    // Insert note into DB
    $save = $db->insert("customer_notes", [
        "company_id"  => LOGGED_IN_USER['company_id'],
        "agency_id"   => LOGGED_IN_USER['agency_id'],
        "customer_id" => $customer_id,
        "note"        => $note,
    ]);

    if ($save) {
        returnSuccess("Customer note added successfully!", [
            "redirect" => "customer-profile?id=" . $customer_id
        ]);
    } else {
        returnError("Failed to save customer note. Please try again.");
    }
}



if (isset($_POST['fetchCustomerNotes'])) {
    $customer_id = intval($_POST['customer_id']);
    $offset      = intval($_POST['offset']);
    $limit       = 10;

    $from_date = !empty($_POST['from_date']) ? $_POST['from_date'] : null;
    $to_date   = !empty($_POST['to_date']) ? $_POST['to_date'] : null;

    $where = [
        "customer_id" => $customer_id,
        "company_id"  => LOGGED_IN_USER['company_id'],
        "agency_id"   => LOGGED_IN_USER['agency_id'],
    ];

    $sql = "SELECT * FROM customer_notes 
            WHERE customer_id = {$customer_id}
            AND company_id = {$where['company_id']}
            AND agency_id = {$where['agency_id']}";

    if ($from_date && $to_date) {
        $sql .= " AND DATE(created_at) BETWEEN '{$from_date}' AND '{$to_date}'";
    }

    $sql .= " ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";

    $notes = $db->query($sql, ['select_query' => true]);

    if ($notes) {
        echo json_encode([
            'status' => 'success',
            'notes'  => $notes
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'notes'  => []
        ]);
    }
    exit;
}



// Fetch Invoice Data
if (isset($_GET['fetchInvoiceData'])) {

    $columns = ['invoice_no', 'invoice_date', 'due_date', 'total_amount', 'paid_amount', 'due_amount', 'status', 'pdf_file'];

    $draw = intval($_POST['draw']);
    $start = intval($_POST['start']);
    $length = intval($_POST['length']);
    $searchValue = $_POST['search']['value'];
    $customer_id = intval($_POST['customer_id']); // 👈 receive customer_id

    // ✅ Base query with customer condition
    $sql = "SELECT * FROM invoices WHERE customer_id = '$customer_id' AND proforma = 0 ";

    if (!empty($searchValue)) {
        $sql .= " AND (invoice_no LIKE '%$searchValue%' OR status LIKE '%$searchValue%') ";
    }

    // ✅ Total records (without search)
    $totalQuery = $db->query("SELECT COUNT(*) as total FROM invoices WHERE customer_id = '$customer_id' AND proforma = 0 ", ["select_query" => true]);
    $totalRecords = $totalQuery[0]['total'];

    // ✅ Filtered records
    $filteredQuery = $db->query(
        "SELECT COUNT(*) as total FROM invoices WHERE customer_id = '$customer_id' AND proforma = 0 " .
            (!empty($searchValue) ? " AND (invoice_no LIKE '%$searchValue%' OR status LIKE '%$searchValue%')" : ""),
        ["select_query" => true]
    );
    $filteredRecords = $filteredQuery[0]['total'];

    // ✅ Pagination
    $sql .= " ORDER BY id DESC LIMIT $start, $length";
    $invoiceData = $db->query($sql, ["select_query" => true]);

    $data = [];
    foreach ($invoiceData as $row) {
        $data[] = $row;
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

// Fetch Proforma Invoice Data
if (isset($_GET['fetchPorformaInvoiceData'])) {

    $columns = ['invoice_no', 'invoice_date', 'due_date', 'total_amount', 'paid_amount', 'due_amount', 'status', 'pdf_file'];

    $draw = intval($_POST['draw']);
    $start = intval($_POST['start']);
    $length = intval($_POST['length']);
    $searchValue = $_POST['search']['value'];
    $customer_id = intval($_POST['customer_id']); // 👈 receive customer_id

    // ✅ Base query with customer condition
    $sql = "SELECT * FROM invoices WHERE customer_id = '$customer_id' AND proforma = 1";

    if (!empty($searchValue)) {
        $sql .= " AND (invoice_no LIKE '%$searchValue%' OR status LIKE '%$searchValue%') ";
    }

    // ✅ Total records (without search)
    $totalQuery = $db->query("SELECT COUNT(*) as total FROM invoices WHERE customer_id = '$customer_id' AND proforma = 1 ", ["select_query" => true]);
    $totalRecords = $totalQuery[0]['total'];

    // ✅ Filtered records
    $filteredQuery = $db->query(
        "SELECT COUNT(*) as total FROM invoices WHERE customer_id = '$customer_id' AND proforma = 1 " .
            (!empty($searchValue) ? " AND (invoice_no LIKE '%$searchValue%' OR status LIKE '%$searchValue%')" : ""),
        ["select_query" => true]
    );
    $filteredRecords = $filteredQuery[0]['total'];

    // ✅ Pagination
    $sql .= " ORDER BY id DESC LIMIT $start, $length";
    $invoiceData = $db->query($sql, ["select_query" => true]);

    $data = [];
    foreach ($invoiceData as $row) {
        $data[] = $row;
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




// Fetch Customers Data
if (isset($_GET['fetchCustomers'])) {

    $columns = ['id', 'fname', 'lname', 'email', 'contact', 'address', 'postcode', 'city', 'is_active'];

    $draw = intval($_POST['draw']);
    $start = intval($_POST['start']);
    $length = intval($_POST['length']);
    $searchValue = $_POST['search']['value'];

    $company_id = LOGGED_IN_USER['company_id'];
    $agency_id = LOGGED_IN_USER['agency_id'];

    // Base query
    $sql = "SELECT * FROM users WHERE company_id = '$company_id' AND agency_id = '$agency_id' AND type = 'customer'";

    if (!empty($searchValue)) {
        $sql .= " AND (fname LIKE '%$searchValue%' 
                    OR lname LIKE '%$searchValue%'
                    OR email LIKE '%$searchValue%'
                    OR contact LIKE '%$searchValue%'
                    OR city LIKE '%$searchValue%')";
    }

    // Total records
    $totalQuery = $db->query("SELECT COUNT(*) as total FROM users WHERE company_id = '$company_id' AND agency_id = '$agency_id' AND type = 'customer' ", ["select_query" => true]);
    $totalRecords = $totalQuery[0]['total'];

    // Filtered records
    $filteredQuery = $db->query(
        "SELECT COUNT(*) as total FROM users WHERE company_id = '$company_id' AND agency_id = '$agency_id' AND type = 'customer' " .
            (!empty($searchValue) ? " AND (fname LIKE '%$searchValue%' 
            OR lname LIKE '%$searchValue%' 
            OR email LIKE '%$searchValue%' 
            OR contact LIKE '%$searchValue%' 
            OR city LIKE '%$searchValue%')" : ""),
        ["select_query" => true]
    );
    $filteredRecords = $filteredQuery[0]['total'];

    // Add LIMIT for pagination
    $sql .= " ORDER BY id DESC LIMIT $start, $length";
    $customersData = $db->query($sql, ["select_query" => true]);

    $data = [];
    foreach ($customersData as $row) {
        $data[] = [
            "id" => $row['id'],
            "title" => $row['title'],
            "fname" => $row['fname'],
            "lname" => $row['lname'],
            "email" => $row['email'],
            "contact" => $row['contact'],
            "address" => $row['address'],
            "postcode" => $row['postcode'],
            "city" => $row['city'],
            "is_active" => $row['is_active'],
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



// Add Customer
if (isset($_POST['createCustomer'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    // ✅ Collect form data
    $data = [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id"  => LOGGED_IN_USER['agency_id'],
        "user_id"    => LOGGED_IN_USER_ID,
        "title"      => arr_val($_POST, 'title'),
        "gender"     => arr_val($_POST, 'gender'),
        "fname"      => arr_val($_POST, 'fname'),
        "lname"      => arr_val($_POST, 'lname'),
        "email"      => arr_val($_POST, 'email'),
        "contact"    => arr_val($_POST, 'contact'),
        "address"    => arr_val($_POST, 'address'),
        "postcode"   => arr_val($_POST, 'postcode'),
        "city"       => arr_val($_POST, 'city'),
        "type" => "customer",
        "verify_status" => 1,
        "image" => "avatar.png"
    ];

    // ✅ Prevent duplicate email (for new records)
    if (!$id && !empty($data['email'])) {
        $exists = $db->select_one('users', 'id', [
            'email' => arr_val($_POST, 'email'),
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id"  => LOGGED_IN_USER['agency_id']
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
        $message = "Customer updated successfully";
    } else {
        // Generate a unique token
        $token = bin2hex(random_bytes(16)); // 32-character token
        $data['verify_token'] = $token;

        $save = $db->insert("users", $data);

        if ($save) {
            // Send email to customer to set password
            $_tc_email->send([
                'template' => 'set_password', // create this email template
                'to' => $data['email'],
                'to_name' => $data['fname'] . " " . $data['lname'],
                'subject' => 'Set Your Account Password',
                'vars' => [
                    'name' => $data['fname'],
                    'set_password_link' => SITE_URL . "/set-password.php?token=" . $token
                ]
            ]);
        }
        $message = "Customer added successfully";
    }

    // ✅ Response
    if ($save) {
        $customers = $db->select("users", "*", [
            "company_id" => LOGGED_IN_USER['company_id'],
            "agency_id"  => LOGGED_IN_USER['agency_id'],
            "is_active" => 1,
            "type" => "customer"
        ]);

        $token = bin2hex(random_bytes(16)); // 32-character token

        returnSuccess($message, [
            "redirect" => "view-customer",
            "customer" => $customers
        ]);
    } else {
        returnError("Error saving customer. Please try again.");
    }
}
