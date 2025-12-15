<?php
require_once('includes/db.php');
$page_name = 'Dashboard';

$JS_FILES_ = [];

$role_id = isset($_GET['id']) ? $_GET['id'] : null;

$roles_sql = "SELECT * FROM roles WHERE company_id = " . LOGGED_IN_USER['company_id'] . "  AND agency_id = " . LOGGED_IN_USER['agency_id'] . " ORDER BY id DESC";
$roles_data = $db->query($roles_sql, ["select_query" => true]);

$role_data = [];
if ($role_id) {
    $role_sql = "SELECT * FROM roles WHERE id = '$role_id' AND company_id = " . LOGGED_IN_USER['company_id'] . "  AND agency_id = " . LOGGED_IN_USER['agency_id'] . " LIMIT 1";
    $role_data = $db->query($role_sql, ["select_query" => true, "values" => [$role_id]]);
    if ($role_data) {
        $role_data = $role_data[0];
    } else {
        header("Location: add-role");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content add-role-container" id="mainContent">
        <div class="card">
            <h3 class="heading mb-3 custom-heading">Add New Role</h3>
            <form action="role" method="POST" class="ajax_form reset" data-reset="reset">
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="form-group">
                            <span class="label">Role:</span>
                            <input type="text" class="form-control" value="<?= arr_val($role_data, "text", "") ?>" name="text" placeholder="Add New Role" required data-length="[1,250]">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <input type="hidden" name="saveRole" value="<?= bc_code(); ?>">
                        <?php if ($role_id) { ?>
                            <input type="hidden" name="id" value="<?= $role_id; ?>">
                        <?php } ?>
                        <button class="btn" type="submit"><i class="fas fa-save"></i> Save</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive mt-5">
            <table class="table table-striped dataTable">
                <thead style="background: var(--webMainColor);color: white;">
                    <tr>
                        <th>#</th>
                        <th>Text</th>
                        <th class="d-none">Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$roles_data) {
                        echo '<tr><td colspan="4" class="text-center">No Roles Found</td></tr>';
                    } else {
                        $count = 1;
                        foreach ($roles_data as $role) { ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td><?= $role['text'] ?></td>
                                <td class="d-none">
                                    <span class="text-white p-1 bold small-font <?php
                                                                                if ($role['is_active'] != '1') echo 'bg-warning text-dark';
                                                                                else echo 'bg-success'; ?>">
                                        <?php
                                        if ($role['is_active'] == '1') echo 'Active';
                                        else echo 'Inactive';
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="align-center child-el-margin-x">
                                        <a class="no-btn-styles text-success cp" href="add-role?id=<?= $role['id'] ?>"><i class="fas fa-edit"></i></a>
                                    </div>
                                </td>
                            </tr>
                    <?php $count++;
                        }
                    } ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php require_once('./includes/js.php'); ?>
</body>

</html>