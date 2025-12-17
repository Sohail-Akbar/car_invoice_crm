   <?php
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
               <img src="<?= $user_img ?>" alt="Branch Logo Img">
           </div>
           <div class="sidebar-option-menu">
               <a href="dashboard" class="menu-item">
                   <div>
                       <i class="fas fa-home"></i>
                       <span class="menu-text">Dashboard</span>
                   </div>
               </a>
               <?php if (LOGGED_IN_USER["type"] === "main_admin") { ?>
                   <div class="has-submenu">
                       <a href="#" class="menu-item" id="customerMenu">
                           <div>
                               <i class="fas fa-user"></i>
                               <span class="menu-text">Companies</span>
                           </div>
                       </a>
                       <div class="submenu" id="customerMenu">
                           <a href="add-company" class="submenu-item">Add</a>
                           <a href="view-company" class="submenu-item">View</a>
                       </div>
                   </div>
               <?php } ?>
               <?php if (LOGGED_IN_USER["type"] === "admin") { ?>
                   <div class="has-submenu">
                       <a href="#" class="menu-item" id="customerMenu">
                           <div>
                               <i class="fas fa-user"></i>
                               <span class="menu-text">Branches</span>
                           </div>
                       </a>
                       <div class="submenu" id="customerMenu">
                           <a href="add-agency" class="submenu-item">Add Branch</a>
                           <a href="view-agency" class="submenu-item">View Branch</a>
                       </div>
                   </div>
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
           <?php if (LOGGED_IN_USER['type'] === "agency") { ?>
               <div class="dropdown">
                   <button class="dropdown-toggle menu-item no-arrow-icon" type="button" data-toggle="dropdown">
                       <h5 class="mb-0 cp text-dark"><i class="fas fa-cog"></i></h5>
                   </button>
                   <div class="dropdown-menu" style="min-width: 18rem;">
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
                       <a href="setting" class="dropdown-item">
                           <i class="fas fa-cog pt-1"></i>
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
                       <a href="<?= _DIR_ . "logout" ?>" class="logout-btn dropdown-item">
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
           <div class="user-role"><?= ucfirst(LOGGED_IN_USER['type']) === "Agency" ? "Branch" : ucwords(str_replace('_', ' ', LOGGED_IN_USER['type'])) ?></div>
       </div>

       <div class="user-menu">
           <a href="setting" class="user-menu-item">
               <i class="fas fa-user"></i>
               <span>My Profile</span>
           </a>
       </div>

       <a href="<?= _DIR_ . "logout" ?>" class="logout-btn">
           <i class="fas fa-sign-out-alt"></i>
           <span>Logout</span>
       </a>
   </aside>

   <!-- Overlay for mobile -->
   <div class="overlay" id="overlay"></div>