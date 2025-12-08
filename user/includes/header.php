<?php
$company = $db->select_one("companies", "*", [
    "id" => LOGGED_IN_USER['company_id']
]);

$user_img = "";

$uploaded_img = _DIR_ . "uploads/" . LOGGED_IN_USER['image'];

// If no custom image OR file doesn't exist → fallback to default
if (
    LOGGED_IN_USER['image'] == "avatar.png" ||
    !file_exists($uploaded_img)
) {
    $user_img = _DIR_ . "images/logo_img.png";
} else {
    $user_img = $uploaded_img;
}

$query = "
    SELECT 
        COUNT(*) AS total_orders,

        -- Total income (exclude write_off amount)
        SUM(
            CASE 
                WHEN write_off = 1 THEN 0
                ELSE CAST(total_amount AS DECIMAL(10,2))
            END
        ) AS total_income,

        -- Total paid amount (pure customer ne jo pay kiya)
        SUM(
            CASE 
                WHEN write_off = 1 THEN 0
                ELSE CAST(paid_amount AS DECIMAL(10,2))
            END
        ) AS total_paid,

        -- Total due amount
        SUM(CAST(due_amount AS DECIMAL(10,2))) AS total_due

    FROM invoices
    WHERE company_id = " . LOGGED_IN_USER['company_id'] . "
    AND agency_id = " . LOGGED_IN_USER['agency_id'] . "
    AND status IN ('paid', 'partial')
";

$result = $db->query($query, ["select_query" => true]);

// Safe defaults
$total_orders = $total_income = $total_paid = $total_due = 0;

if (!empty($result)) {
    $total_orders = intval($result[0]['total_orders']);
    $total_income = floatval($result[0]['total_income']);
    $total_paid   = floatval($result[0]['total_paid']);
    $total_due    = floatval($result[0]['total_due']);
}
?>
<!-- Left Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-menu">
        <div class="branch-log">
            <img src="<?= $user_img ?>" alt="Branch Logo Img">
        </div>
        <div class="sidebar-option-menu">
            <?php if (LOGGED_IN_USER['type'] === "agency") { ?>
                <a href="dashboard" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
                <a href="invoice" class="menu-item">
                    <i class="fas fa-file-invoice"></i>
                    <span class="menu-text">Invoice</span>
                    <!-- <span class="menu-badge">15</span> -->
                </a>
                <a href="invoice-search" class="menu-item">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <span class="menu-text">Invoice Search</span>
                </a>
                <div class="has-submenu">
                    <a href="#" class="menu-item" id="customerMenu">
                        <i class="fas fa-user"></i>
                        <span class="menu-text">Customers</span>
                    </a>
                    <div class="submenu" id="customerMenu">
                        <a href="add-customer" class="submenu-item">Add Customer</a>
                        <a href="view-customer" class="submenu-item">View Customer</a>
                    </div>
                </div>
                <div class="has-submenu">
                    <a href="#" class="menu-item" id="registerationMenu">
                        <i class="fas fa-car"></i>
                        <span class="menu-text">Registration</span>
                    </a>
                    <div class="submenu" id="registerationMenu">
                        <a href="registration-vehicle" class="submenu-item">Registration Vehicle</a>
                        <a href="view-registration-vehicle" class="submenu-item">View Registration Vehicle</a>
                    </div>
                </div>
                <a href="financial-income-reports" class="menu-item">
                    <i class="fas fa-chart-line me-2"></i>
                    <span class="menu-text">Income Reports</span>
                </a>
                <a href="send-sms" class="menu-item">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                    <span class="menu-text">Send SMS</span>
                </a>
            <?php } ?>
            <?php if (LOGGED_IN_USER['type'] === "staff") { ?>
                <a href="assigned-vehicles" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span class="menu-text">Assigned Vehicles - (<?= isset($assigned_tasks) ? count($assigned_tasks) : "0" ?>)</span>
                </a>
                <a href="completed-tasks" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span class="menu-text">Completed Tasks</span>
                </a>
            <?php } ?>
        </div>
    </div>
</aside>

<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-left">
        <button class="toggle-sidebar" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        <a href="dashboard" class="logo">
            <div class="user-info">
                <h4>Hi,<?= substr(LOGGED_IN_USER['fname'], 0, 20) ?></h4>
                <p>Let’s check your Garage today</p>
            </div>
        </a>
    </div>

    <div class="navbar-right">
        <!-- <div class="nav-icon">
            <i class="far fa-bell"></i>
            <span class="badge">5</span>
        </div>
        <div class="nav-icon">
            <i class="far fa-envelope"></i>
            <span class="badge">3</span>
        </div> -->
        <?php if (LOGGED_IN_USER['type'] === "agency") { ?>
            <div class="dropdown">
                <button class="dropdown-toggle menu-item no-arrow-icon" type="button" data-toggle="dropdown">
                    <h5 class="mb-0 cp text-dark"><i class="fas fa-cog"></i></h5>
                </button>
                <div class="dropdown-menu animated flipInY" style="min-width: 15rem;">
                    <a href="add-staff" class="dropdown-item">
                        <i class="fas fa-user pt-1"></i>
                        <span class="text">Add Staff</span>
                    </a>
                    <a href="view-staff" class="dropdown-item">
                        <i class="fas fa-user pt-1"></i>
                        <span class="text">View Staff</span>
                    </a>
                    <a href="add-role" class="dropdown-item">
                        <i class="fas fa-plus-circle pt-1"></i>
                        Add Role
                    </a>
                    <a href="add-services" class="dropdown-item">
                        <i class="fas fa-plus-circle pt-1"></i>
                        Add Services
                    </a>
                    <a href="settings" class="dropdown-item">
                        <i class="fas fa-cog pt-1"></i>
                        Branch Settings
                    </a>
                    <a href="email-template" class="dropdown-item">
                        <i class="fas fa-cog pt-1"></i>
                        Email Template
                    </a>
                    <a href="setting" class="dropdown-item">
                        <i class="fas fa-cog pt-1"></i>
                        <span class="text">Profile Setting</span>
                    </a>
                </div>
            </div>
        <?php } ?>
        <div class="user-profile" id="userProfile">
            <div class="user-avatar"><?= strtoupper(substr(LOGGED_IN_USER['fname'], 0, 1)) .  strtoupper(substr(LOGGED_IN_USER['lname'], 0, 1)) ?></div>
            <div class="user-name"><?= LOGGED_IN_USER['name'] ?></div>
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>
</nav>

<!-- Right Sidebar - User Info -->
<aside class="user-sidebar" id="userSidebar">
    <div class="user-header">
        <div class="user-avatar-large"><?= strtoupper(substr(LOGGED_IN_USER['fname'], 0, 1)) .  strtoupper(substr(LOGGED_IN_USER['lname'], 0, 1)) ?></div>
        <div class="user-name-large"><?= LOGGED_IN_USER['name'] ?></div>
        <div class="user-role"><?= ucfirst(LOGGED_IN_USER['type']) === "Agency" ? "Branch" : ucfirst(LOGGED_IN_USER['type']) ?></div>
    </div>
    <?php if (LOGGED_IN_USER['type'] === "agency") { ?>
        <div class="user-stats">
            <div class="stat-item">
                <div class="stat-value"><?= $total_orders ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= _CURRENCY_SYMBOL . $total_income ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= _CURRENCY_SYMBOL . $total_paid ?></div>
                <div class="stat-label">Total Paid</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= _CURRENCY_SYMBOL . $total_due ?></div>
                <div class="stat-label">Total Due</div>
            </div>
        </div>
        <div class="user-menu">
            <a href="setting" class="user-menu-item">
                <i class="fas fa-user"></i>
                <span>My Profile</span>
            </a>
        </div>
    <?php } ?>

    <a href="logout" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </a>
</aside>

<!-- Overlay for mobile -->
<div class="overlay" id="overlay"></div>