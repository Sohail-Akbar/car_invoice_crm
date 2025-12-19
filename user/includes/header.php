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
?>
<!-- Left Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-menu">
        <div class="branch-log">
            <img src="<?= _DIR_ . "/images/PR-Auto-Centre-Logo-dark.png" ?>" alt="Branch Logo Img">
        </div>
        <div class="sidebar-option-menu">
            <?php if (LOGGED_IN_USER['type'] === "agency") { ?>
                <a href="dashboard" class="menu-item">
                    <div>
                        <i class="fas fa-home"></i>
                        <span class="menu-text">Dashboard</span>
                    </div>
                </a>
                <a href="invoice" class="menu-item">
                    <div>
                        <i class="fas fa-file-invoice"></i>
                        <span class="menu-text">Quick Invoice</span>
                    </div>
                </a>
                <a href="invoice-search" class="menu-item">
                    <div>
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <span class="menu-text">Search Invoice</span>
                    </div>
                </a>
                <div class="has-submenu">
                    <a href="#" class="menu-item" id="customerMenu">
                        <div>
                            <i class="fas fa-user"></i>
                            <span class="menu-text">Customer Record</span>
                        </div>
                    </a>
                    <div class="submenu" id="customerMenu">
                        <a href="add-customer" class="submenu-item">Add</a>
                        <a href="registration-vehicle" class="submenu-item">Search</a>
                        <a href="view-customer" class="submenu-item">View All Customer</a>
                        <a href="view-registration-vehicle" class="submenu-item">View All Vehicles</a>
                    </div>
                </div>
                <!-- <div class="has-submenu d-none">
                    <a href="#" class="menu-item" id="registerationMenu">
                        <div>
                            <i class="fas fa-car"></i>
                            <span class="menu-text">Registration</span>
                        </div>
                    </a>
                    <div class="submenu" id="registerationMenu">
                        <a href="add-vehicle" class="submenu-item">Add Vehicle</a>
                        <a href="view-registration-vehicle" class="submenu-item">View Vehicle</a>
                        <a href="registration-vehicle" class="submenu-item">Search</a>
                        <div class="has-submenu d-none">
                            <a href="#" class="menu-item" id="customerMenu">
                                <div>
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    <span class="menu-text">Search</span>
                                </div>
                            </a>
                            <div class="submenu" id="customerMenu">
                                <a href="registration-vehicle?search_by=name" class="submenu-item">By Customer</a>
                                <a href="registration-vehicle?search_by=reg_no" class="submenu-item">By Vehicle</a>
                                <a href="registration-vehicle?search_by=phone" class="submenu-item">By Telephone</a>
                            </div>
                        </div>
                    </div>
                </div> -->
                <a href="financial-income-reports" class="menu-item">
                    <div>
                        <i class="fas fa-chart-line me-2"></i>
                        <span class="menu-text">Financial Records</span>
                    </div>
                </a>
                <a href="send-sms" class="menu-item d-none">
                    <div>
                        <i class="fa fa-comment" aria-hidden="true"></i>
                        <span class="menu-text">Send SMS</span>
                    </div>
                </a>
            <?php } ?>
            <?php if (LOGGED_IN_USER['type'] === "staff") { ?>
                <a href="assigned-vehicles" class="menu-item">
                    <div>
                        <i class="fas fa-users"></i>
                        <span class="menu-text">Assigned Vehicles - (<?= isset($assigned_tasks) ? count($assigned_tasks) : "0" ?>)</span>
                    </div>
                </a>
                <a href="completed-tasks" class="menu-item">
                    <div>
                        <i class="fas fa-users"></i>
                        <span class="menu-text">Completed Tasks</span>
                    </div>
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
                        <svg width="21" height="19" viewBox="0 0 21 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 4C4 1.79 5.79 0 8 0C10.21 0 12 1.79 12 4C12 6.21 10.21 8 8 8C5.79 8 4 6.21 4 4ZM7.14 15.75L6.85 15L7.14 14.25C7.84 12.5 9.08 11.14 10.61 10.22C9.79 10.08 8.92 10 8 10C3.58 10 0 11.79 0 14V16H7.27C7.23 15.91 7.18 15.83 7.14 15.75ZM15 14C14.44 14 14 14.44 14 15C14 15.56 14.44 16 15 16C15.56 16 16 15.56 16 15C16 14.44 15.56 14 15 14ZM21 15C20.06 17.34 17.73 19 15 19C12.27 19 9.94 17.34 9 15C9.94 12.66 12.27 11 15 11C17.73 11 20.06 12.66 21 15ZM17.5 15C17.5 14.337 17.2366 13.7011 16.7678 13.2322C16.2989 12.7634 15.663 12.5 15 12.5C14.337 12.5 13.7011 12.7634 13.2322 13.2322C12.7634 13.7011 12.5 14.337 12.5 15C12.5 15.663 12.7634 16.2989 13.2322 16.7678C13.7011 17.2366 14.337 17.5 15 17.5C15.663 17.5 16.2989 17.2366 16.7678 16.7678C17.2366 16.2989 17.5 15.663 17.5 15Z" fill="black" />
                        </svg>
                        <span class="text">Add Staff</span>
                    </a>
                    <a href="view-staff" class="dropdown-item">
                        <svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 21.5H24M13.5 19V13C13.5 13 11 11.5 7 11.5C3 11.5 0.5 13 0.5 13V19M17.5 0.5V3.5H14.5V6.5H17.5V9.5H20.5V6.5H23.5V3.5H20.5V0.5H17.5ZM6.744 9.5C6.744 9.5 3.999 7.752 3.999 5.566C3.999 3.875 5.343 2.504 7.001 2.504C8.659 2.504 9.996 3.875 9.996 5.566C9.996 7.75 7.26 9.5 7.26 9.5H6.744Z" stroke="black" />
                        </svg>
                        <span class="text">View Staff</span>
                    </a>
                    <a href="add-role" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 2.5C0 1.83696 0.263392 1.20107 0.732233 0.732233C1.20107 0.263392 1.83696 0 2.5 0H11.5C12.163 0 12.7989 0.263392 13.2678 0.732233C13.7366 1.20107 14 1.83696 14 2.5V6.6C13.5272 6.3585 13.022 6.18672 12.5 6.09V2.5C12.5 2.23478 12.3946 1.98043 12.2071 1.79289C12.0196 1.60536 11.7652 1.5 11.5 1.5H2.5C2.23478 1.5 1.98043 1.60536 1.79289 1.79289C1.60536 1.98043 1.5 2.23478 1.5 2.5V11.5C1.5 11.7652 1.60536 12.0196 1.79289 12.2071C1.98043 12.3946 2.23478 12.5 2.5 12.5H6.09C6.18672 13.022 6.3585 13.5272 6.6 14H2.5C1.83696 14 1.20107 13.7366 0.732233 13.2678C0.263392 12.7989 0 12.163 0 11.5V2.5ZM16 11.5C16 12.6935 15.5259 13.8381 14.682 14.682C13.8381 15.5259 12.6935 16 11.5 16C10.3065 16 9.16193 15.5259 8.31802 14.682C7.47411 13.8381 7 12.6935 7 11.5C7 10.3065 7.47411 9.16193 8.31802 8.31802C9.16193 7.47411 10.3065 7 11.5 7C12.6935 7 13.8381 7.47411 14.682 8.31802C15.5259 9.16193 16 10.3065 16 11.5ZM12 9.5C12 9.36739 11.9473 9.24021 11.8536 9.14645C11.7598 9.05268 11.6326 9 11.5 9C11.3674 9 11.2402 9.05268 11.1464 9.14645C11.0527 9.24021 11 9.36739 11 9.5V11H9.5C9.36739 11 9.24021 11.0527 9.14645 11.1464C9.05268 11.2402 9 11.3674 9 11.5C9 11.6326 9.05268 11.7598 9.14645 11.8536C9.24021 11.9473 9.36739 12 9.5 12H11V13.5C11 13.6326 11.0527 13.7598 11.1464 13.8536C11.2402 13.9473 11.3674 14 11.5 14C11.6326 14 11.7598 13.9473 11.8536 13.8536C11.9473 13.7598 12 13.6326 12 13.5V12H13.5C13.6326 12 13.7598 11.9473 13.8536 11.8536C13.9473 11.7598 14 11.6326 14 11.5C14 11.3674 13.9473 11.2402 13.8536 11.1464C13.7598 11.0527 13.6326 11 13.5 11H12V9.5Z" fill="black" />
                        </svg>
                        Add Role
                    </a>
                    <a href="add-services" class="dropdown-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 9C6.79565 9 7.55871 8.68393 8.12132 8.12132C8.68393 7.55871 9 6.79565 9 6M6 9C5.20435 9 4.44129 8.68393 3.87868 8.12132C3.31607 7.55871 3 6.79565 3 6M6 9V12M9 6C9 5.20435 8.68393 4.44129 8.12132 3.87868C7.55871 3.31607 6.79565 3 6 3M9 6H12M6 3C5.20435 3 4.44129 3.31607 3.87868 3.87868C3.31607 4.44129 3 5.20435 3 6M6 3V0M3 6H0M2 2L4 4M8 8L10 10M10 2L8 4M4 8L2 10M18 12C18.7956 12 19.5587 11.6839 20.1213 11.1213C20.6839 10.5587 21 9.79565 21 9M18 12C17.2044 12 16.4413 11.6839 15.8787 11.1213C15.3161 10.5587 15 9.79565 15 9M18 12V15M21 9C21 8.20435 20.6839 7.44129 20.1213 6.87868C19.5587 6.31607 18.7956 6 18 6M21 9H24M18 6C17.2044 6 16.4413 6.31607 15.8787 6.87868C15.3161 7.44129 15 8.20435 15 9M18 6V3M15 9H12M14 5L16 7M20 11L22 13M22 5L20 7M16 11L14 13M9 21C9.79565 21 10.5587 20.6839 11.1213 20.1213C11.6839 19.5587 12 18.7956 12 18M9 21C8.20435 21 7.44129 20.6839 6.87868 20.1213C6.31607 19.5587 6 18.7956 6 18M9 21V24M12 18C12 17.2044 11.6839 16.4413 11.1213 15.8787C10.5587 15.3161 9.79565 15 9 15M12 18H15M9 15C8.20435 15 7.44129 15.3161 6.87868 15.8787C6.31607 16.4413 6 17.2044 6 18M9 15V12M6 18H3M5 14L7 16M11 20L13 22M13 14L11 16M7 20L5 22" stroke="black" stroke-width="2" />
                        </svg>
                        Add Services
                    </a>
                    <a href="settings" class="dropdown-item">
                        <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.22 6.5C7.07833 6.18511 7.00342 5.84428 7 5.499C7 4.12 8.121 2.999 9.5 2.999C10.879 2.999 12 4.12 12 5.499C12 5.857 11.917 6.193 11.781 6.501C11.4764 6.36211 11.1597 6.25131 10.835 6.17C10.937 5.967 11.001 5.742 11.001 5.499C11.001 4.672 10.328 3.999 9.501 3.999C8.674 3.999 8.001 4.672 8.001 5.499C8.001 5.741 8.064 5.967 8.167 6.17C7.84225 6.25131 7.5256 6.36211 7.221 6.501L7.22 6.5ZM4.001 11.516C4.00449 12.5317 4.29123 13.5263 4.829 14.388C4.64922 14.8605 4.3304 15.2675 3.9146 15.5551C3.4988 15.8427 3.00557 15.9975 2.5 15.999C1.83696 15.999 1.20107 15.7356 0.732233 15.2668C0.263392 14.7979 0 14.162 0 13.499C0 12.291 0.86 11.282 2 11.05V4.949C1.43549 4.83377 0.928099 4.52709 0.563663 4.08084C0.199227 3.63459 0.000112869 3.07615 0 2.5C0 1.121 1.121 0 2.5 0C3.879 0 5 1.121 5 2.5C5 3.708 4.14 4.717 3 4.949V9.512C3.43053 9.18185 3.95746 9.002 4.5 9H4.607C4.437 9.33267 4.30133 9.68267 4.2 10.05C3.93693 10.1027 3.69298 10.2255 3.49391 10.4054C3.29484 10.5853 3.14804 10.8156 3.069 11.072C3.414 11.153 3.726 11.312 4 11.518L4.001 11.516ZM2.5 3.999C3.327 3.999 4 3.326 4 2.499C4 1.672 3.327 0.999 2.5 0.999C1.673 0.999 1 1.672 1 2.499C1 3.326 1.673 3.999 2.5 3.999ZM4 13.499C4 12.672 3.327 11.999 2.5 11.999C1.673 11.999 1 12.672 1 13.499C1 14.326 1.673 14.999 2.5 14.999C3.327 14.999 4 14.326 4 13.499ZM14 11.499C14 12.6925 13.5259 13.8371 12.682 14.681C11.8381 15.5249 10.6935 15.999 9.5 15.999C8.30653 15.999 7.16193 15.5249 6.31802 14.681C5.47411 13.8371 5 12.6925 5 11.499C5 10.3055 5.47411 9.16093 6.31802 8.31702C7.16193 7.47311 8.30653 6.999 9.5 6.999C10.6935 6.999 11.8381 7.47311 12.682 8.31702C13.5259 9.16093 14 10.3055 14 11.499ZM12.5 11.499C12.5 11.3664 12.4473 11.2392 12.3536 11.1454C12.2598 11.0517 12.1326 10.999 12 10.999H10V8.999C10 8.86639 9.94732 8.73921 9.85355 8.64545C9.75979 8.55168 9.63261 8.499 9.5 8.499C9.36739 8.499 9.24021 8.55168 9.14645 8.64545C9.05268 8.73921 9 8.86639 9 8.999V10.999H7C6.86739 10.999 6.74021 11.0517 6.64645 11.1454C6.55268 11.2392 6.5 11.3664 6.5 11.499C6.5 11.6316 6.55268 11.7588 6.64645 11.8526C6.74021 11.9463 6.86739 11.999 7 11.999H9V13.888C9 14.0206 9.05268 14.1478 9.14645 14.2416C9.24021 14.3353 9.36739 14.388 9.5 14.388C9.63261 14.388 9.75979 14.3353 9.85355 14.2416C9.94732 14.1478 10 14.0206 10 13.888V11.999H12C12.1326 11.999 12.2598 11.9463 12.3536 11.8526C12.4473 11.7588 12.5 11.6316 12.5 11.499Z" fill="black" />
                        </svg>
                        VAT%
                    </a>
                    <a href="discount" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 2.5C0 1.83696 0.263392 1.20107 0.732233 0.732233C1.20107 0.263392 1.83696 0 2.5 0H11.5C12.163 0 12.7989 0.263392 13.2678 0.732233C13.7366 1.20107 14 1.83696 14 2.5V6.6C13.5272 6.3585 13.022 6.18672 12.5 6.09V2.5C12.5 2.23478 12.3946 1.98043 12.2071 1.79289C12.0196 1.60536 11.7652 1.5 11.5 1.5H2.5C2.23478 1.5 1.98043 1.60536 1.79289 1.79289C1.60536 1.98043 1.5 2.23478 1.5 2.5V11.5C1.5 11.7652 1.60536 12.0196 1.79289 12.2071C1.98043 12.3946 2.23478 12.5 2.5 12.5H6.09C6.18672 13.022 6.3585 13.5272 6.6 14H2.5C1.83696 14 1.20107 13.7366 0.732233 13.2678C0.263392 12.7989 0 12.163 0 11.5V2.5ZM16 11.5C16 12.6935 15.5259 13.8381 14.682 14.682C13.8381 15.5259 12.6935 16 11.5 16C10.3065 16 9.16193 15.5259 8.31802 14.682C7.47411 13.8381 7 12.6935 7 11.5C7 10.3065 7.47411 9.16193 8.31802 8.31802C9.16193 7.47411 10.3065 7 11.5 7C12.6935 7 13.8381 7.47411 14.682 8.31802C15.5259 9.16193 16 10.3065 16 11.5ZM12 9.5C12 9.36739 11.9473 9.24021 11.8536 9.14645C11.7598 9.05268 11.6326 9 11.5 9C11.3674 9 11.2402 9.05268 11.1464 9.14645C11.0527 9.24021 11 9.36739 11 9.5V11H9.5C9.36739 11 9.24021 11.0527 9.14645 11.1464C9.05268 11.2402 9 11.3674 9 11.5C9 11.6326 9.05268 11.7598 9.14645 11.8536C9.24021 11.9473 9.36739 12 9.5 12H11V13.5C11 13.6326 11.0527 13.7598 11.1464 13.8536C11.2402 13.9473 11.3674 14 11.5 14C11.6326 14 11.7598 13.9473 11.8536 13.8536C11.9473 13.7598 12 13.6326 12 13.5V12H13.5C13.6326 12 13.7598 11.9473 13.8536 11.8536C13.9473 11.7598 14 11.6326 14 11.5C14 11.3674 13.9473 11.2402 13.8536 11.1464C13.7598 11.0527 13.6326 11 13.5 11H12V9.5Z" fill="black" />
                        </svg>
                        Discount
                    </a>
                    <a href="email-template" class="dropdown-item">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.625 0C0.728 0 1.49506e-08 0.728 1.49506e-08 1.625V4.3C-0.000111225 6.2729 0.620537 8.19583 1.77403 9.7964C2.92752 11.397 4.55537 12.594 6.427 13.218C6.55545 13.2613 6.69455 13.2613 6.823 13.218C8.69463 12.594 10.3225 11.397 11.476 9.7964C12.6295 8.19583 13.2501 6.2729 13.25 4.3V1.625C13.25 0.728 12.523 0 11.625 0H1.625ZM1.25 1.625C1.25 1.418 1.418 1.25 1.625 1.25H11.625C11.832 1.25 12 1.418 12 1.625V4.3C12.0004 6.04019 11.4432 7.73472 10.41 9.135C9.38935 8.16383 8.03386 7.62307 6.625 7.625C5.158 7.625 3.825 8.2 2.839 9.135C1.80616 7.7346 1.24927 6.04008 1.25 4.3V1.625ZM6.625 3.125C6.26033 3.125 5.91059 3.26987 5.65273 3.52773C5.39487 3.78559 5.25 4.13533 5.25 4.5C5.25 4.86467 5.39487 5.21441 5.65273 5.47227C5.91059 5.73013 6.26033 5.875 6.625 5.875C6.98967 5.875 7.33941 5.73013 7.59727 5.47227C7.85513 5.21441 8 4.86467 8 4.5C8 4.13533 7.85513 3.78559 7.59727 3.52773C7.33941 3.26987 6.98967 3.125 6.625 3.125ZM4 4.5C4 3.80381 4.27656 3.13613 4.76884 2.64384C5.26113 2.15156 5.92881 1.875 6.625 1.875C7.32119 1.875 7.98887 2.15156 8.48116 2.64384C8.97344 3.13613 9.25 3.80381 9.25 4.5C9.25 5.19619 8.97344 5.86387 8.48116 6.35616C7.98887 6.84844 7.32119 7.125 6.625 7.125C5.92881 7.125 5.26113 6.84844 4.76884 6.35616C4.27656 5.86387 4 5.19619 4 4.5Z" fill="black" />
                        </svg>
                        Email Template
                    </a>
                    <a href="setting" class="dropdown-item">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.625 0C0.728 0 1.49506e-08 0.728 1.49506e-08 1.625V4.3C-0.000111225 6.2729 0.620537 8.19583 1.77403 9.7964C2.92752 11.397 4.55537 12.594 6.427 13.218C6.55545 13.2613 6.69455 13.2613 6.823 13.218C8.69463 12.594 10.3225 11.397 11.476 9.7964C12.6295 8.19583 13.2501 6.2729 13.25 4.3V1.625C13.25 0.728 12.523 0 11.625 0H1.625ZM1.25 1.625C1.25 1.418 1.418 1.25 1.625 1.25H11.625C11.832 1.25 12 1.418 12 1.625V4.3C12.0004 6.04019 11.4432 7.73472 10.41 9.135C9.38935 8.16383 8.03386 7.62307 6.625 7.625C5.158 7.625 3.825 8.2 2.839 9.135C1.80616 7.7346 1.24927 6.04008 1.25 4.3V1.625ZM6.625 3.125C6.26033 3.125 5.91059 3.26987 5.65273 3.52773C5.39487 3.78559 5.25 4.13533 5.25 4.5C5.25 4.86467 5.39487 5.21441 5.65273 5.47227C5.91059 5.73013 6.26033 5.875 6.625 5.875C6.98967 5.875 7.33941 5.73013 7.59727 5.47227C7.85513 5.21441 8 4.86467 8 4.5C8 4.13533 7.85513 3.78559 7.59727 3.52773C7.33941 3.26987 6.98967 3.125 6.625 3.125ZM4 4.5C4 3.80381 4.27656 3.13613 4.76884 2.64384C5.26113 2.15156 5.92881 1.875 6.625 1.875C7.32119 1.875 7.98887 2.15156 8.48116 2.64384C8.97344 3.13613 9.25 3.80381 9.25 4.5C9.25 5.19619 8.97344 5.86387 8.48116 6.35616C7.98887 6.84844 7.32119 7.125 6.625 7.125C5.92881 7.125 5.26113 6.84844 4.76884 6.35616C4.27656 5.86387 4 5.19619 4 4.5Z" fill="black" />
                        </svg>
                        <span class="text">Profile Setting</span>
                    </a>
                </div>
            </div>
        <?php } ?>
        <div class="user-profile" id="userProfile">
            <div class="dropdown">
                <button class="dropdown-toggle menu-item no-arrow-icon content-center" type="button" data-toggle="dropdown">
                    <div class="user-avatar"><?= strtoupper(substr(LOGGED_IN_USER['fname'], 0, 1)) .  strtoupper(substr(LOGGED_IN_USER['lname'], 0, 1)) ?></div>
                    <div class="user-name d-none"><?= LOGGED_IN_USER['name'] ?></div>
                    <i class="fas fa-chevron-down d-none"></i>
                </button>
                <div class="dropdown-menu animated flipInY" style="min-width: 15rem;">
                    <a href="logout" class="logout-btn dropdown-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Right Sidebar - User Info -->
<aside class="user-sidebar d-none" id="userSidebar">
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
                <div class="stat-value"><?= _CURRENCY_SYMBOL . ($total_income - $total_due) ?></div>
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