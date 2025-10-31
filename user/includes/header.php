    <div class="sidebar">
        <div class="user-info text-center">
            <div class="user-image-container">
                <img src="<?= _DIR_ ?>images/users/<?= LOGGED_IN_USER['image'] ?>" alt="user image" class="user-img">
                <label class="overlay"><i class="fas fa-camera"></i>
                    <input type="file" class="user-img-file d-none" accept="image/*">
                </label>
            </div>
            <button class="save-img btn bg_pink"><i class="fas fa-save"></i> Save Image</button>
            <div>
                <p class="user-name"><?= LOGGED_IN_USER['name']; ?></p>
                <p class="headline">Member Since <?= monthDate(LOGGED_IN_USER['date_added']); ?></p>
            </div>
        </div>
        <ul class="nav">
            <li class="nav-item">
                <a href="dashboard" class="nav-link">
                    <i class="fas fa-th-large"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="add-staff" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span class="text">Add Staff</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="view-staff" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span class="text">View Staff</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="add-customer" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span class="text">Add Customer</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="view-customer" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span class="text">View Customer</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="registration-car" class="nav-link">
                    <i class="far fa-registered"></i>
                    <span class="text">Registration Car</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="view-registration-car" class="nav-link">
                    <i class="far fa-registered"></i>
                    <span class="text">View Registration Car</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="invoice" class="nav-link">
                    <i class="far fa-registered"></i>
                    <span class="text">Invoice</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="setting" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span class="text">Profile Setting</span>
                </a>
            </li>
        </ul>
    </div>
    <nav class="navbar">
        <a class="logo page-name" href="dashboard">
            User Dashboard
        </a>
        <div class="menu">
            <div class="dropdown">
                <button class="dropdown-toggle menu-item no-arrow-icon" type="button" data-toggle="dropdown">
                    <h5 class="mb-0 mr-3 cp text-dark"><i class="fas fa-cog"></i></h5>
                </button>
                <div class="dropdown-menu" style="min-width: 18rem;">
                    <a href="add-role" class="dropdown-item">
                        <i class="fas fa-plus-circle pt-1"></i>
                        Add Role
                    </a>
                    <a href="add-services" class="dropdown-item">
                        <i class="fas fa-plus-circle pt-1"></i>
                        Add Services
                    </a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropdown-toggle menu-item no-arrow-icon" type="button" data-toggle="dropdown">
                    <img src="../images/users/<?= LOGGED_IN_USER['image']; ?>" alt="user-img" class="user-img">
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