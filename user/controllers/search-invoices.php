<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');



if (isset($_GET['fetchFilteredInvoices'])) {

    $invoice_no     = isset($_POST['invoice_no']) ? trim($_POST['invoice_no']) : '';
    $customer_name  = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $reg_number     = isset($_POST['reg_number']) ? trim($_POST['reg_number']) : '';
    $status         = isset($_POST['status']) ? trim($_POST['status']) : '';
    $from_date      = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
    $to_date        = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
    $mobile_number  = isset($_POST['mobile_number']) ? trim($_POST['mobile_number']) : '';

    if (!empty($from_date)) {
        $from_date = DateTime::createFromFormat('d-m-Y', $from_date)->format('Y-m-d');
    }
    if (!empty($to_date)) {
        $to_date = DateTime::createFromFormat('d-m-Y', $to_date)->format('Y-m-d');
    }

    $company_id = LOGGED_IN_USER['company_id'];
    $agency_id  = LOGGED_IN_USER['agency_id'];

    // ✅ BASE QUERY (FIXED JOIN)
    $sql = "
        SELECT 
            i.id,
            i.invoice_no,
            DATE_FORMAT(i.invoice_date, '%d/%m/%Y') AS invoice_date,
            i.total_amount,
            i.paid_amount,
            i.due_amount,
            i.status,
            i.pdf_file,
            CONCAT(c.fname, ' ', c.lname) AS customer_name,
            ch.reg_number
        FROM invoices i
        INNER JOIN users c 
            ON i.customer_id = c.id 
            AND c.type = 'customer'

        LEFT JOIN customer_car_history ch 
            ON ch.id = (
                SELECT id 
                FROM customer_car_history 
                WHERE customer_id = i.customer_id
                ORDER BY id DESC
                LIMIT 1
            )

        WHERE i.company_id = '$company_id'
        AND i.agency_id = '$agency_id'
    ";

    // 🔍 FILTERS
    if (!empty($invoice_no)) {
        $sql .= " AND i.invoice_no LIKE '%" . addslashes($invoice_no) . "%'";
    }

    if (!empty($customer_name)) {
        $sql .= " AND (
            c.fname LIKE '%" . addslashes($customer_name) . "%' 
            OR c.lname LIKE '%" . addslashes($customer_name) . "%'
        )";
    }

    if (!empty($mobile_number)) {
        $sql .= " AND c.contact LIKE '%" . addslashes($mobile_number) . "%'";
    }

    if (!empty($reg_number)) {
        $sql .= " AND ch.reg_number LIKE '%" . addslashes($reg_number) . "%'";
    }

    if (!empty($status)) {
        $sql .= " AND i.status = '" . addslashes($status) . "'";
    }

    if (!empty($from_date)) {
        $sql .= " AND DATE(i.invoice_date) >= '" . addslashes($from_date) . "'";
    }

    if (!empty($to_date)) {
        $sql .= " AND DATE(i.invoice_date) <= '" . addslashes($to_date) . "'";
    }

    // 🔽 ORDER
    $sql .= " ORDER BY i.invoice_date DESC, i.id DESC";

    // 🚀 EXECUTE
    $invoices = $db->query($sql, ["select_query" => true]);

    // 📦 RESPONSE
    $data = [];
    foreach ($invoices as $row) {
        $data[] = [
            'id'            => $row['id'],
            'invoice_no'    => $row['invoice_no'],
            'invoice_date'  => DateTime::createFromFormat('d/m/Y', $row['invoice_date'])->format('d F Y'),
            'total_amount'  => $row['total_amount'],
            'paid_amount'   => $row['paid_amount'],
            'due_amount'    => $row['due_amount'],
            'status'        => $row['status'],
            'pdf_file'      => $row['pdf_file'],
            'customer_name' => $row['customer_name'],
            'reg_number'    => $row['reg_number'],
        ];
    }

    echo json_encode([
        'success'  => true,
        'invoices' => $data,
        'count'    => count($data)
    ]);
    exit;
}
