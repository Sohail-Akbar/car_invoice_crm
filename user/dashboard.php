<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [];
if (LOGGED_IN_USER['type'] === "customer") redirectTo("customer-profile?id=" . LOGGED_IN_USER_ID);
if (LOGGED_IN_USER['type'] === "staff") redirectTo("assigned-vehicles");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6fa;
            margin: 0;
            padding: 40px;
        }

        .tabs-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }

        .tab-card {
            background: white;
            width: 220px;
            padding: 30px 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: 0.3s;
            border: 2px solid transparent;
        }

        .tab-card:hover {
            transform: translateY(-5px);
            border-color: #3498db;
            box-shadow: 0 6px 14px rgba(52, 152, 219, 0.2);
        }

        .tab-card i {
            font-size: 40px;
            color: #3498db;
            margin-bottom: 10px;
        }

        .tab-card h4 {
            margin: 10px 0 0;
            color: #333;
        }
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <div class="all-content">
        <?php if (LOGGED_IN_USER['type'] === "agency") { ?>
            <h2 style="text-align:center; margin-bottom:10px; color:#333;">Welcome to Your Dashboard</h2>
            <p style="text-align:center; color:#666; margin-bottom:40px;">
                Manage your invoices, payments, and clients easily from here.
            </p>

            <div class="tabs-container">
                <div class="tab-card" onclick="location.href='invoice'">
                    <i class="fas fa-plus-circle"></i>
                    <h4>Create Invoice</h4>
                </div>

                <div class="tab-card" onclick="location.href='invoice-search'">
                    <i class="fas fa-file-invoice"></i>
                    <h4>View Invoices</h4>
                </div>

                <div class="tab-card" onclick="location.href='view-customer'">
                    <i class="fas fa-search"></i>
                    <h4>Search Clients</h4>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php require_once('./includes/js.php'); ?>
</body>

</html>