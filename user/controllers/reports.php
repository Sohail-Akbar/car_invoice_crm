<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');

class IncomeReport
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Get detailed income report
    public function getIncomeReport($data)
    {
        $company_id = LOGGED_IN_USER["company_id"];
        $agency_id = LOGGED_IN_USER['agency_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $vehicle_reg = $data['vehicle_reg'] ?? '';
        $customer_name = $data['customer_name'] ?? '';
        $report_type = $data['report_type'] ?? 'all';

        // Prepare WHERE conditions
        $where = "i.company_id = $company_id AND i.agency_id = $agency_id AND i.invoice_date BETWEEN '$start_date' AND '$end_date'";

        if ($vehicle_reg != '') {
            $where .= " AND cch.reg_number LIKE '%$vehicle_reg%'";
        }
        if ($customer_name != '') {
            $where .= " AND CONCAT(c.fname,' ',c.lname) LIKE '%$customer_name%'";
        }

        // Add report type filters
        switch ($report_type) {
            case 'paid':
                $where .= " AND i.status IN ('paid','partial')";
                break;
            case 'unpaid':
                $where .= " AND i.status = 'unpaid'";
                break;
            case 'write_off':
                $where .= " AND i.write_off = 1";
                break;
        }

        // Query: join only one active car per customer
        $sql = "
            SELECT 
                i.id,
                i.invoice_no,
                i.invoice_date,
                i.due_date,
                i.status,
                CONCAT(c.fname,' ',c.lname) AS customer_name,
                cch.reg_number AS vehicle_reg,
                cch.make,
                cch.model,
                i.subtotal,
                i.tax_amount,
                i.discount,
                i.total_amount,
                i.paid_amount,
                i.due_amount,
                i.write_off,
                i.notes,
                i.pdf_file,
                i.created_at
            FROM invoices i
            LEFT JOIN users c ON i.customer_id = c.id
            LEFT JOIN (
                SELECT * FROM customer_car_history WHERE is_active = 1 GROUP BY customer_id
            ) cch ON i.customer_id = cch.customer_id
            WHERE $where
            ORDER BY i.invoice_date DESC
        ";
        $result = $this->conn->query($sql, ["select_query" => true]);

        return [
            "success" => true,
            "data" => $result,
            "total_records" => count($result)
        ];
    }

    // Get summary totals for income report
    public function getReportSummary($data)
    {
        $company_id = LOGGED_IN_USER["company_id"];
        $agency_id = LOGGED_IN_USER['agency_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $vehicle_reg = $data['vehicle_reg'] ?? '';
        $customer_name = $data['customer_name'] ?? '';
        $report_type = $data['report_type'] ?? 'all';

        $where = "i.company_id = $company_id AND i.agency_id = $agency_id AND i.invoice_date BETWEEN '$start_date' AND '$end_date'";

        if ($vehicle_reg != '') {
            $where .= " AND cch.reg_number LIKE '%$vehicle_reg%'";
        }
        if ($customer_name != '') {
            $where .= " AND CONCAT(c.fname,' ',c.lname) LIKE '%$customer_name%'";
        }

        switch ($report_type) {
            case 'paid':
                $where .= " AND i.status IN ('paid','partial')";
                break;
            case 'unpaid':
                $where .= " AND i.status = 'unpaid'";
                break;
            case 'write_off':
                $where .= " AND i.write_off = 1";
                break;
        }

        $sql = "
            SELECT 
                i.status,
                COUNT(i.id) AS total_invoices,
                SUM(i.subtotal) AS total_subtotal,
                SUM(i.tax_amount) AS total_tax,
                SUM(i.discount) AS total_discount,
                SUM(i.total_amount) AS total_amount,
                SUM(i.paid_amount) AS total_paid,
                SUM(i.due_amount) AS total_due,
                i.write_off
            FROM invoices i
            LEFT JOIN customers c ON i.customer_id = c.id
            LEFT JOIN (
                SELECT * FROM customer_car_history WHERE is_active = 1 GROUP BY customer_id
            ) cch ON i.customer_id = cch.customer_id
            WHERE $where
            GROUP BY i.status
        ";

        $result = $this->conn->query($sql, ["select_query" => true]);

        return [
            "success" => true,
            "summary" => $result
        ];
    }
}

// Usage
$report = new IncomeReport($db);
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $data['action'] ?? '';

    if ($action == 'get_report') {
        echo json_encode($report->getIncomeReport($data));
    } elseif ($action == 'get_summary') {
        echo json_encode($report->getReportSummary($data));
    } else {
        echo json_encode(["success" => false, "message" => "Invalid action"]);
    }
}
