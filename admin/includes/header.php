    <div class="sidebar">
        <div class="user-info text-center">
            <div class="user-image-container">
                <img src="../images/users/<?= LOGGED_IN_USER['image']; ?>" alt="user-img" class="user-img">
                <label class="overlay"><i class="fas fa-camera"></i>
                    <input type="file" class="user-img-file d-none" accept="image/*">
                </label>
            </div>
            <button class="save-img btn bg_pink"><i class="fas fa-save"></i> Save Image</button>
            <div>
                <p class="user-name" style="text-transform: capitalize;"><?= LOGGED_IN_USER['name']; ?></p>
            </div>
        </div>
        <ul class="nav">
            <li class="nav-item">
                <a href="dashboard" class="nav-link">
                    <i class="fas fa-th-large"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <?php if (LOGGED_IN_USER["type"] === "main_admin") { ?>
                <li class="nav-item">
                    <a href="add-company" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span class="text">Add Company</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="view-company" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span class="text">View Company</span>
                    </a>
                </li>
            <?php } ?>
            <?php if (LOGGED_IN_USER["type"] === "admin") { ?>
                <li class="nav-item">
                    <a href="add-agency" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span class="text">Add Agency</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="view-agency" class="nav-link">
                        <i class="fas fa-building"></i>
                        <span class="text">View Agency</span>
                    </a>
                </li>
            <?php } ?>
            <li class="nav-item">
                <a href="setting" class="nav-link" target="_blank">
                    <i class="fas fa-cog"></i>
                    <span class="text">Profile Setting
                        <i class="fas fa-external-link-alt ml-1" style="font-size: 13px"></i>
                    </span>
                </a>
            </li>
        </ul>
    </div>
    <nav class="navbar">
        <a class="logo page-name" href="dashboard">
            Admin Dashboard
        </a>
        <div class="menu">
            <div class="dropdown">
                <button class="dropdown-toggle menu-item no-arrow-icon" type="button" data-toggle="dropdown">
                    <img src="../images/users/<?= LOGGED_IN_USER['image']; ?>" alt="user-img" class="user-img">
                </button>
                <div class="dropdown-menu">
                    <a href="<?= _DIR_ ?>logout" class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>