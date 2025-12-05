<?php
define('_DIR_', '../');
require_once "db.php";

mkdir(_DIR_ . "/uploads");

// Check if action is already done
function _is($type)
{
    global $db;
    $data = $db->select_one("meta_data", "id", [
        "meta_key" => "tmp_scripts",
        "meta_value" => $type
    ]);
    if ($data) return false;
    $db->insert('meta_data', [
        'meta_key' => 'tmp_scripts',
        'meta_value' => $type
    ]);
    return true;
}

// Meta Data Table
$db->query("CREATE TABLE IF NOT EXISTS `meta_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `meta_key` varchar(250) NOT NULL,
    `meta_value` varchar(250) NOT NULL,
    `meta_json` text NOT NULL,
    `time` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB;");
// DB Tables
if (_is("install_db_tables")) {
    $db->query("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `fname` varchar(250) NOT NULL,
        `lname` varchar(250) NOT NULL,
        `name` varchar(250) NOT NULL,
        `email` varchar(250) NOT NULL,
        `image` varchar(250) NOT NULL,
        `password` varchar(250) NOT NULL,
        `is_admin` tinyint(1) NOT NULL DEFAULT 0,
        `verify_status` int(1) NOT NULL DEFAULT 0,
        `verify_token` varchar(250) NOT NULL,
        `password_forgot_token` varchar(250) NOT NULL,
        `token_expiry_date` timestamp NULL DEFAULT NULL,
        `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
        `uid` varchar(250) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB;");
}

if (_is("agencies_table")) {
    $db->query("CREATE TABLE `agencies` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `name` varchar(255) DEFAULT NULL,
 `email` varchar(250) NOT NULL,
 `contact` varchar(255) DEFAULT NULL,
 `address` varchar(255) DEFAULT NULL,
 `postcode` varchar(255) DEFAULT NULL,
 `lat` varchar(255) DEFAULT NULL,
 `lng` varchar(255) DEFAULT NULL,
 `agency_logo` varchar(255) DEFAULT NULL,
 `created` timestamp NOT NULL DEFAULT current_timestamp(),
 `is_active` tinyint(1) NOT NULL DEFAULT 1,
 `vat_percentage` varchar(250) NOT NULL DEFAULT '0',
 `city` varchar(250) NOT NULL,
 `discount_percentage` varchar(250) NOT NULL DEFAULT '0',
 `sms_api_key` varchar(250) NOT NULL,
 PRIMARY KEY (`id`)
) ");
}

if (_is("companies_table")) {
    $db->query("	CREATE TABLE `companies` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_name` varchar(255) NOT NULL,
 `company_logo` varchar(255) DEFAULT NULL,
 `company_email` varchar(255) DEFAULT NULL,
 `company_contact` varchar(255) DEFAULT NULL,
 `company_address` varchar(255) DEFAULT NULL,
 `company_postcode` varchar(255) DEFAULT NULL,
 `company_lat` varchar(255) DEFAULT NULL,
 `company_lng` varchar(255) DEFAULT NULL,
 `created` timestamp NOT NULL DEFAULT current_timestamp(),
 `is_active` tinyint(1) NOT NULL DEFAULT 1,
 `company_city` varchar(250) NOT NULL,
 PRIMARY KEY (`id`)
)");
}

if (_is("customers_table")) {
    $db->query("CREATE TABLE `customers` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `agency_id` int(11) NOT NULL,
 `title` varchar(250) NOT NULL,
 `gender` varchar(250) NOT NULL,
 `fname` varchar(250) NOT NULL,
 `lname` varchar(250) NOT NULL,
 `email` varchar(250) NOT NULL,
 `contact` varchar(250) NOT NULL,
 `address` varchar(250) NOT NULL,
 `postcode` varchar(250) NOT NULL,
 `city` varchar(250) NOT NULL,
 `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
 `is_active` varchar(250) DEFAULT '1',
 PRIMARY KEY (`id`)
)");
}

if (_is("customer_car_history_table")) {
    $db->query("CREATE TABLE `customer_car_history` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `agency_id` int(11) NOT NULL,
 `customer_id` int(11) NOT NULL,
 `reg_number` varchar(250) NOT NULL,
 `make` varchar(250) NOT NULL,
 `model` varchar(250) NOT NULL,
 `firstUsedDate` varchar(250) NOT NULL,
 `fuelType` varchar(250) NOT NULL,
 `primaryColour` varchar(250) NOT NULL,
 `registrationDate` varchar(250) NOT NULL,
 `manufactureDate` varchar(250) NOT NULL,
 `engineSize` varchar(250) NOT NULL,
 `hasOutstandingRecall` varchar(250) NOT NULL,
 `expiryDate` varchar(250) NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
 `is_active` int(11) NOT NULL DEFAULT 1,
 `is_manual` int(11) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`)
) ");
}

if (_is("customer_email_history_table")) {
    $db->query("CREATE TABLE `customer_email_history` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `agency_id` int(11) NOT NULL,
 `customer_id` int(11) NOT NULL,
 `pdf_file` varchar(250) NOT NULL,
 `invoice_id` int(11) NOT NULL,
 `invoice_type` varchar(250) NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
)");
}

if (_is("customer_notes_table")) {
    $db->query("CREATE TABLE `customer_notes` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `agency_id` int(11) NOT NULL,
 `customer_id` int(11) NOT NULL,
 `note` text NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
)");
}

if (_is("customer_staff_table")) {
    $db->query("CREATE TABLE `customer_staff` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `agency_id` int(11) NOT NULL,
 `customer_id` int(11) NOT NULL,
 `invoice_id` int(11) NOT NULL,
 `staff_id` int(11) NOT NULL,
 `assigned_by` int(11) NOT NULL,
 `assignment_date` timestamp NOT NULL DEFAULT current_timestamp(),
 `is_active` int(11) NOT NULL DEFAULT 1,
 `completed_at` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`)
)");
}

if (_is("invoices")) {
    $db->query("	CREATE TABLE `invoices` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `agency_id` int(11) NOT NULL,
 `customer_id` int(11) NOT NULL,
 `mot_id` int(11) NOT NULL,
 `invoice_no` varchar(250) NOT NULL,
 `invoice_date` date NOT NULL,
 `due_date` date NOT NULL,
 `status` enum('unpaid','paid','partial','cancelled') NOT NULL,
 `subtotal` varchar(250) NOT NULL,
 `tax_rate` varchar(250) NOT NULL,
 `tax_amount` varchar(250) NOT NULL,
 `discount` varchar(250) NOT NULL,
 `total_amount` varchar(250) NOT NULL,
 `paid_amount` varchar(250) NOT NULL,
 `due_amount` varchar(250) NOT NULL,
 `notes` text NOT NULL,
 `pdf_file` varchar(250) NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
 `proforma` int(11) NOT NULL DEFAULT 0,
 `discount_percentage` int(250) NOT NULL,
 `write_off` int(11) NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`)
)");
}

if (_is("invoice_items_table")) {
    $db->query("CREATE TABLE `invoice_items` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `invoice_id` int(11) NOT NULL,
 `services_id` varchar(250) NOT NULL,
 `quantity` int(11) NOT NULL DEFAULT 1,
 `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
)");
}

if (_is("meta_data_table")) {
    $db->query("CREATE TABLE `meta_data` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `meta_key` varchar(250) NOT NULL,
 `meta_value` varchar(250) NOT NULL,
 `meta_json` text NOT NULL,
 `time` timestamp NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
)");
}

if (_is("roles_table")) {
    $db->query("CREATE TABLE `roles` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `agency_id` int(11) DEFAULT NULL,
 `text` varchar(255) NOT NULL,
 `created` timestamp NOT NULL DEFAULT current_timestamp(),
 `is_active` tinyint(4) NOT NULL DEFAULT 1,
 PRIMARY KEY (`id`)
)");
}

if (_is("services_table")) {
    $db->query("CREATE TABLE `services` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `agency_id` int(11) DEFAULT NULL,
 `text` varchar(255) NOT NULL,
 `amount` varchar(250) NOT NULL,
 `created` timestamp NOT NULL DEFAULT current_timestamp(),
 `is_active` tinyint(4) NOT NULL DEFAULT 1,
 PRIMARY KEY (`id`)
) ");
}

if (_is("sms_logs_table")) {
    $db->query("CREATE TABLE `sms_logs` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `agency_id` int(11) NOT NULL,
 `customer_id` int(11) NOT NULL,
 `reg_number` varchar(250) NOT NULL,
 `template_id` varchar(250) NOT NULL,
 `message` text NOT NULL,
 `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
 `status` varchar(250) NOT NULL DEFAULT 'pending',
 PRIMARY KEY (`id`)
)");
}

if (_is("staffs_table")) {
    $db->query("CREATE TABLE `staffs` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) NOT NULL,
 `agency_id` int(11) NOT NULL,
 `role_id` int(11) NOT NULL,
 `title` varchar(250) NOT NULL,
 `gender` varchar(250) NOT NULL,
 `fname` varchar(250) NOT NULL,
 `lname` varchar(250) NOT NULL,
 `email` varchar(250) NOT NULL,
 `contact` varchar(250) NOT NULL,
 `address` varchar(250) NOT NULL,
 `postcode` varchar(250) NOT NULL,
 `city` varchar(250) NOT NULL,
 `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
 `is_active` varchar(250) DEFAULT '1',
 PRIMARY KEY (`id`)
)");
}

if (_is("users_table")) {
    $db->query("CREATE TABLE `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `company_id` int(11) DEFAULT NULL,
 `agency_id` int(11) DEFAULT NULL,
 `user_id` int(11) DEFAULT NULL,
 `role_id` int(11) NOT NULL,
 `fname` varchar(250) NOT NULL,
 `lname` varchar(250) NOT NULL,
 `name` varchar(250) NOT NULL,
 `gender` varchar(250) NOT NULL,
 `title` varchar(250) NOT NULL,
 `email` varchar(250) NOT NULL,
 `type` enum('main_admin','admin','agency','customer','staff') NOT NULL,
 `address` varchar(250) NOT NULL,
 `contact` varchar(250) NOT NULL,
 `city` varchar(250) NOT NULL,
 `lat` varchar(250) NOT NULL,
 `lng` varchar(250) NOT NULL,
 `postcode` varchar(250) NOT NULL,
 `image` varchar(250) NOT NULL DEFAULT 'avatar.png',
 `password` varchar(250) NOT NULL,
 `is_admin` tinyint(1) NOT NULL DEFAULT 0,
 `verify_status` int(1) NOT NULL DEFAULT 0,
 `verify_token` varchar(250) NOT NULL,
 `password_forgot_token` varchar(250) NOT NULL,
 `token_expiry_date` timestamp NULL DEFAULT NULL,
 `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
 `uid` varchar(250) NOT NULL,
 `is_active` int(11) DEFAULT 1,
 `twofa_code` varchar(10) DEFAULT NULL,
 `twofa_expire` datetime DEFAULT NULL,
 `remember_token` varchar(255) DEFAULT NULL,
 PRIMARY KEY (`id`)
)");
}
