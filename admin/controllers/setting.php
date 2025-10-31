<?php
define('DIR', '../');
require_once('../includes/db.php');

if (isset($_POST['change_password'])) {
    $user_id = LOGGED_IN_USER_ID;

    if (!$user_id) {
        returnError("User not logged in");
        exit;
    }

    $current_password = arr_val($_POST, "current_password", "");
    $new_password = arr_val($_POST, "new_password", "");
    $confirm_password = arr_val($_POST, "confirm_password", "");

    if ($new_password !== $confirm_password) {
        returnError("New password and confirm password do not match");
        exit;
    }

    // Get current password from DB
    $user = $db->select_one("users", ["password"], ["id" => $user_id]);
    if (!$user) {
        returnError("User not found");
        exit;
    }


    // Ensure stored password is present
    if (empty($user['password'])) {
        returnError("Stored password missing. Cannot verify.");
        exit;
    }


    // Verify current password
    $isValid = password_verify($current_password, $user['password']);
    if (!$isValid) {
        returnError("Current password is incorrect");
    exit;
    }

    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);


    $update = $db->update("users", ["password" => $hashed_password], ["id" => $user_id]);

    if ($update) {
        returnSuccess("Password changed successfully", [
            "redirect" => ""
        ]);
    } else {
        returnError("Failed to update password");
    }
    exit;
}


// Update Personal Info
if (isset($_POST['update_personal_information'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    if (strlen($fname) > 0 && strlen($lname) > 0) {
        $name = $fname . ' ' . $lname;
        $update = $db->update('users', [
            'fname' => $fname,
            'lname' => $lname,
            'name' => $name,
        ], ['id' => LOGGED_IN_USER['id']]);
        if ($update) {
            returnSuccess('Information Updated Successfully', [
                "redirect" => ""
            ]);
        }
    }
}
