<?php
require_once('includes/db.php');
$page_name = 'View All Staff';

$JS_FILES_ = [
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ .  "css/jquery.dataTables.min.css"
];

$staffs_data = $db->select("users", "*", [
    "company_id" => LOGGED_IN_USER['company_id'],
    "agency_id" => LOGGED_IN_USER['agency_id'],
    "type" => "staff"
], ["order_by" => "id desc"]);
if (!$staffs_data) $staffs_data = [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content view-staff-container" id="mainContent">
        <div class="card">
            <h3 class="heading mb-3 custom-heading">Staffs</h3>
            <div class="custom-table-header pull-away mt-5">
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
                    <a href="add-staff" class="btn ml-3 add-customer-btn br-5">+ &nbsp;Add New Staff</a>
                </div>
            </div>
            <div class="table-responsive table-custom-design mt-3">
                <table class="table table-striped dataTable">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Staff Details</th>
                            <th>Address</th>
                            <th class="d-none">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 1;
                        foreach ($staffs_data as $staff) { ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td>
                                    <strong>Name: </strong>
                                    <?= $staff['title'] . " " . $staff['fname'] . " " . $staff['lname'] ?><br>
                                    <strong>Email :</strong>
                                    <?= $staff['email'] ?><br>
                                    <strong>Contact :</strong>
                                    <?= $staff['contact'] ?><br>
                                </td>
                                <td>
                                    <?= $staff['address'] . ", " . $staff['postcode'] . ", " . $staff['city'] ?>
                                </td>
                                <td class="d-none">
                                    <span class="text-white p-1 bold small-font <?php
                                                                                if ($staff['is_active'] != '1') echo 'bg-warning text-dark';
                                                                                else echo 'bg-success'; ?>">
                                        <?php
                                        if ($staff['is_active'] == '1') echo 'Active';
                                        else echo 'Inactive';
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="align-center child-el-margin-x">
                                        <a class="no-btn-styles text-success cp" href="add-staff?id=<?= $staff['id'] ?>"><i class="fas fa-edit"></i></a>
                                        <button class="no-btn-styles text-danger cp tc-delete-btn" title="Delete" data-target="<?= $staff['id']; ?>" data-action="staff"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php $count++;
                        } ?>
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