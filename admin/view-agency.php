<?php
require_once('includes/db.php');
$page_name = 'Users';

$JS_FILES_ = [
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css"
];

$agency_data = "SELECT u.*, a.name as agency_name, a.contact as agency_contact, a.address as agency_address, a.agency_logo, a.email as agency_email
                        FROM users AS u
                        LEFT JOIN agencies AS a ON u.company_id = a.company_id
                        WHERE u.company_id = a.company_id AND u.agency_id = a.id AND u.user_id = '" . LOGGED_IN_USER_ID . "'
                        ORDER BY u.id DESC";
$agency_data = $db->query($agency_data, ["select_query" => true]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content view-agency-container" id="mainContent">
        <div class="card">
            <div class="col-md-12">
                <h3 class="heading mb-4 custom-heading text-clr">Agencies</h3>
            </div>
            <div class="custom-table-header pull-away mt-4">
                <div class="search-container">
                    <input type="text" class="search-input search-minimal form-control" placeholder="Type to search...">
                    <div class="search-icon">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="d-flex content-center">
                    <div class="btn-group dropleft content-center br-5">
                        <button type="button" class="btn dropdown-toggle table-filter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Entries
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 7H20M6.99994 12H16.9999M10.9999 17H12.9999" stroke="#454545" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">5</a>
                            <a class="dropdown-item" href="#">25</a>
                            <a class="dropdown-item" href="#">50</a>
                            <a class="dropdown-item" href="#">100</a>
                        </div>
                    </div>
                    <a href="add-agency" class="btn ml-3 add-customer-btn br-5">+ &nbsp;Add New Branch</a>
                </div>
            </div>
            <div class="table-responsive table-custom-design mt-4">
                <table class="table table-striped dataTable">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Branch Details</th>
                            <th>Branch Details</th>
                            <th>Branch Logo</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($agency_data) {
                        ?>
                            <?php
                            $count = 1;
                            foreach ($agency_data as $user) {
                                if ($user['id'] === LOGGED_IN_USER_ID)
                                    continue; ?>
                                <tr>
                                    <td><?php echo $count; ?></td>
                                    <td>
                                        <strong>Branch Name: </strong>
                                        <?= $user['agency_name'] ?><br>
                                        <strong>Agency Contact :</strong>
                                        <?= $user['agency_contact'] ?><br>
                                        <strong>Agency Address: </strong>
                                        <?= $user['agency_address'] ?>
                                    </td>
                                    <td>
                                        <strong>Full Name: </strong>
                                        <?= $user['title'] . "  " . $user['name'] ?><br>
                                        <strong>Email :</strong>
                                        <?= $user['email'] ?><br>
                                        <strong>Contact Number: </strong>
                                        <?= $user['contact'] ?>
                                    </td>
                                    <td class="content-center">
                                        <?php if (isset($user['agency_logo'])) { ?>
                                            <img src="<?= _DIR_ . "uploads/" . $user['agency_logo'] ?>" style="width:100px;">
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <span class="text-white p-1 bold small-font <?php
                                                                                    if ($user['verify_status'] != '1') echo 'bg-warning text-dark';
                                                                                    else echo 'bg-success'; ?>">
                                            <?php
                                            if ($user['verify_status'] == '1') echo 'Verified';
                                            else echo 'unverified';
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="align-center child-el-margin-x">
                                            <a class="no-btn-styles text-success cp" href="add-agency?id=<?= $user['id'] ?>"><i class="fas fa-edit"></i></a>
                                            <button class="no-btn-styles text-danger cp tc-delete-btn" title="Delete" data-target="<?= $user['id']; ?>" data-action="user"><i class="fas fa-trash-alt"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php $count++;
                            } ?>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php require_once('./includes/js.php'); ?>
    <script>
        $(document).ready(function() {
            // DataTable initialize karen aur variable me store karen
            var table = $('.dataTable').DataTable();

            // Custom search input
            $('.custom-table-header .search-input').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Agar dropdown se entries change karna chahen
            $('.custom-table-header .dropdown-item').on('click', function(e) {
                e.preventDefault();
                var val = parseInt($(this).text());
                table.page.len(val).draw();
            });
        });
    </script>
</body>

</html>