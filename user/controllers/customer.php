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
        i.notes,
        s.text AS service_name,
        s.amount AS service_amount
    FROM customer_car_history AS c
    LEFT JOIN invoices AS i ON c.id = i.mot_id
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
    $all_staff = $db->select("staffs", "*", [
        "company_id" => LOGGED_IN_USER['company_id'],
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "is_active" => 1
    ]);

    // Group invoices
    $grouped = [];
    foreach ($rows as $row) {
        $invoice_id = $row['invoice_id'];

        if (!isset($grouped[$invoice_id])) {
            $grouped[$invoice_id] = [
                "car" => [
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
            SELECT cs.id AS cs_id, st.title, st.fname, st.lname, st.email
            FROM customer_staff cs
            INNER JOIN staffs st ON cs.staff_id = st.id
            WHERE cs.customer_id = '$customer_id' AND cs.invoice_id = '$invoice_id' AND cs.is_active = 1
        ", ["select_query" => true]);

        $pdf_html = "";
        if (!empty($invoice['pdf_file']) && file_exists(_DIR_ . "/uploads/invoices/{$invoice['pdf_file']}")) {
            $pdf_html = "<a href='../uploads/invoices/{$invoice['pdf_file']}' target='_blank' class='btn btn-outline-primary btn-sm'><i class='fas fa-file-pdf mr-1'></i> View Invoice PDF</a>";
        } else {
            $pdf_html = "<span class='text-muted small'><i class='fas fa-exclamation-circle'></i> No PDF file available</span>";
        }

        echo "
        <div class='card mb-3 shadow-sm mt-5'>
            <div class='card-header bg-info d-flex justify-content-between align-items-center'>
                <div><strong>{$car['make']} {$car['model']}</strong> ({$car['reg_number']})</div>
                <div>
                    <a class='text-white add-staff' data-toggle='modal' data-target='#{$modal_id}' style='cursor:pointer;'>
                        <i class='fas fa-user-plus mr-1'></i> Add Staff
                    </a>
                    " . (
            $invoice['due_amount'] > 0 && $invoice['status'] != 'cancelled'
            ? "<a class='text-white update-payment ml-5' data-toggle='modal' data-target='#{$pay_modal_id}' style='cursor:pointer;'>
                        <i class='fas fa-pound-sign mr-1'></i> Update Payment
                    </a>"
            : "<span class='text-light ml-5 small'><i class='fas fa-check-circle'></i> Payment Cleared</span>"
        ) . "
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

                <div class='d-flex justify-content-between mb-3'>
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
                                <select name='staff_id' class='form-control' required>
                                    <option value=''>-- Select Staff Member --</option>";
        foreach ($all_staff as $staff) {
            echo "<option value='{$staff['id']}'>{$staff['title']} {$staff['fname']} {$staff['lname']} ({$staff['email']})</option>";
        }
        echo "
                                </select>
                            </div>

                            <input type='hidden' name='customer_id' value='{$customer_id}'>
                            <input type='hidden' name='invoice_id' value='{$invoice_id}'>
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
                                <label>Add Payment (" . _CURRENCY_SYMBOL . ")</label>
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

    $update = $db->update("customers", [
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
        "is_active" => 1
    ]);

    if ($update) {
        returnSuccess("Customer updated successfully");
    } else {
        returnError("Failed to update customer");
    }
    exit;
}
