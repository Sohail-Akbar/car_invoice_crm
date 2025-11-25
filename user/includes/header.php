<?php
$company = $db->select_one("companies", "*", [
    "id" => LOGGED_IN_USER['company_id']
]);

$user_img = "";
if (LOGGED_IN_USER['image'] == "avatar.png") {
    $user_img =  _DIR_ . "images/users/" . LOGGED_IN_USER['image'];
} else {
    $user_img = _DIR_ . "uploads/" . LOGGED_IN_USER['image'];
}
?>

<div class="sidebar">
    <div class="user-info text-center">
        <div class="user-image-container">
            <img src="<?= $user_img ?>" alt="user image" class="user-img">
            <label class="overlay"><i class="fas fa-camera"></i>
                <input type="file" class="user-img-file d-none" accept="image/*">
            </label>
        </div>
        <button class="save-img btn bg_pink"><i class="fas fa-save"></i> Save Image</button>
        <div>
            <p class="user-name"><?= LOGGED_IN_USER['name']; ?></p>
            <!-- <p class="headline">Member Since <?= monthDate(LOGGED_IN_USER['date_added']); ?></p> -->
        </div>
    </div>
    <ul class="nav">
        <?php if (LOGGED_IN_USER['type'] === "agency") { ?>
            <li class="nav-item">
                <a href="dashboard" class="nav-link">
                    <i class="fas fa-th-large"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item with-sub-menu">
                <a href="#" class="nav-link">
                    <span>
                        <i class="fas fa-user"></i>
                        <span class="text">Customers</span>
                    </span>
                    <i class="fas fa-angle-down"></i>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="add-customer" class="nav-link">
                            <span class="text">Add Customer</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="view-customer" class="nav-link">
                            <span class="text">View Customer</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item with-sub-menu">
                <a href="#" class="nav-link">
                    <span>
                        <i class="fas fa-car"></i>
                        <span class="text">Registration</span>
                    </span>
                    <i class="fas fa-angle-down"></i>
                </a>
                <ul class="sub-menu">
                    <li class="nav-item">
                        <a href="registration-vehicle" class="nav-link">
                            <span class="text">Registration Vehicle</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="view-registration-vehicle" class="nav-link">
                            <span class="text">View Registration Vehicle</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="invoice" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <span class="text">Invoice</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="invoice-search" class="nav-link">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    <span class="text">Invoice Search</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="financial-income-reports" class="nav-link">
                    <i class="fas fa-chart-line me-2"></i>
                    <span class="text">Financial Income Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="send-sms" class="nav-link">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                    <span class="text">Send SMS To Customer</span>
                </a>
            </li>
        <?php } ?>
        <?php if (LOGGED_IN_USER['type'] === "staff") { ?>
            <li class="nav-item">
                <a href="assigned-vehicles" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <span class="text">Assigned Vehicles - (<?= isset($assigned_tasks) ? count($assigned_tasks) : "0" ?>)</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="completed-tasks" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <span class="text">Completed Tasks</span>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
<nav class="navbar">
    <a class="logo page-name" href="dashboard">
        <?= ucfirst(LOGGED_IN_USER['type']) === "Agency" ? "Branch" : ucfirst(LOGGED_IN_USER['type']) ?> Dashboard
    </a>
    <h4><b><?= $company['company_name'] ?></b></h4>
    <div class="menu">
        <div class="dropdown">
            <button class="dropdown-toggle menu-item no-arrow-icon" type="button" data-toggle="dropdown">
                <h5 class="mb-0 mr-3 cp text-dark"><i class="fas fa-cog"></i></h5>
            </button>
            <div class="dropdown-menu" style="min-width: 18rem;">
                <?php if (LOGGED_IN_USER['type'] === "agency") { ?>
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
                <?php } ?>
                <a href="setting" class="dropdown-item">
                    <i class="fas fa-cog pt-1"></i>
                    <span class="text">Profile Setting</span>
                </a>
            </div>
        </div>
        <div class="dropdown">
            <button class="dropdown-toggle menu-item no-arrow-icon" type="button" data-toggle="dropdown">
                <img src="<?= $user_img ?> ?>" alt="user-img" class="user-img">
            </button>
            <div class="dropdown-menu">
                <?php if (IS_ADMIN) { ?>
                    <a href="<?= _DIR_ ?>admin/login" class="dropdown-item"><i class="fas fa-user-cog"></i> Admin Dashboard</a>
                <?php } ?>
                <a href="logout" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>