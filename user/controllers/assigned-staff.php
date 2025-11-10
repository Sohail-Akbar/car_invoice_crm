
<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');

// Handle staff assignment
// if (isset($_POST['assign_staff'])) {

//     $staff_id = intval($_POST['staff_id']);
//     $customer_id = intval($_POST['customer_id']);
//     $invoice_id = intval($_POST['invoice_id']);

//     $callback = isset($_POST['callback']) ? true : false;

//     // Check if staff is already assigned
//     $existing = $db->select_one("customer_staff", "*", [
//         "customer_id" => $customer_id,
//         "staff_id" => $staff_id,
//         "is_active" => 1,
//         "invoice_id" => $invoice_id
//     ]);

//     if (!$existing) {
//         $save = $db->insert("customer_staff", [
//             "company_id" => LOGGED_IN_USER['company_id'],
//             "agency_id" => LOGGED_IN_USER['agency_id'],
//             "customer_id" => $customer_id,
//             "staff_id" => $staff_id,
//             "assigned_by" => LOGGED_IN_USER['id'],
//             "is_active" => 1,
//             "invoice_id" => $invoice_id
//         ]);

//         if ($save) {
//             if ($callback) {
//                 returnSuccess("Staff assigned successfully!");
//             } else {
//                 returnSuccess("Staff assigned successfully!", [
//                     "redirect" => "customer-profile?id=" . $customer_id
//                 ]);
//             }
//         } else {
//             returnError("Failed to assign staff.");
//         }
//     } else {
//         returnError("This staff member is already assigned to this customer.");
//     }
// }

if (isset($_POST['assign_staff'])) {

    $staff_ids = $_POST['staff_id']; // array of selected staff
    $customer_id = intval($_POST['customer_id']);
    $invoice_id = intval($_POST['invoice_id']);

    $callback = isset($_POST['callback']) ? true : false;

    if (is_array($staff_ids) && count($staff_ids) > 0) {

        $assignedCount = 0;
        $alreadyAssigned = 0;

        foreach ($staff_ids as $sid) {
            $staff_id = intval($sid);

            // 🔍 Check if this customer is already assigned to this staff for this invoice
            $existing = $db->select_one("customer_staff", "*", [
                "customer_id" => $customer_id,
                "staff_id" => $staff_id,
                "invoice_id" => $invoice_id,
                "is_active" => 1
            ]);

            if ($existing) {
                $alreadyAssigned++;
                continue;
            }

            // ✅ Insert new assignment
            $save = $db->insert("customer_staff", [
                "company_id" => LOGGED_IN_USER['company_id'],
                "agency_id" => LOGGED_IN_USER['agency_id'],
                "customer_id" => $customer_id,
                "staff_id" => $staff_id,
                "assigned_by" => LOGGED_IN_USER['id'],
                "is_active" => 1,
                "invoice_id" => $invoice_id
            ]);

            if ($save) {
                $assignedCount++;
            }
        }

        // 🟢 Response
        if ($assignedCount > 0) {
            $msg = "$assignedCount staff member(s) assigned successfully.";
            if ($alreadyAssigned > 0) {
                $msg .= " ($alreadyAssigned already assigned)";
            }

            if ($callback) {
                returnSuccess($msg);
            } else {
                returnSuccess($msg, [
                    "redirect" => "customer-profile?id=" . $customer_id,
                    "invoice_id" => $_POST['vehicle_id']
                ]);
            }
        } else {
            returnError("All selected staff are already assigned to this customer for this invoice.");
        }
    } else {
        returnError("Please select at least one staff member.");
    }
}


// Handle staff removal
if (isset($_POST['remove_staff'])) {
    $assignment_id = intval($_POST['assignment_id']);

    $update = $db->update("customer_staff", [
        "is_active" => 0
    ], [
        "id" => $assignment_id,
        "company_id" => LOGGED_IN_USER['company_id']
    ]);

    if ($update) {
        $success_message = "Staff removed successfully!";
        returnSuccess("Staff removed successfully!", [
            "redirect" => ""
        ]);
    } else {
        returnError("Failed to remove staff.");
    }
}
