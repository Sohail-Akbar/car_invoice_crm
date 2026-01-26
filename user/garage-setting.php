<?php
require_once('includes/db.php');
$page_name = 'Setting';

$JS_FILES_ = [];


$agency_id  = LOGGED_IN_USER['agency_id'];
$company_id = LOGGED_IN_USER['company_id'];

$rows = $db->select("garage_working_hours", "*", [
    "agency_id"  => $agency_id,
    "company_id" => $company_id
]);

$garageSchedule = [];

/*
    Result ko is format me convert karenge:
    [
        'Monday' => ['open' => '09:00', 'close' => '18:00'],
        'Tuesday' => ['open' => '10:00', 'close' => '17:00'],
    ]
*/
foreach ($rows as $r) {
    $garageSchedule[$r['day']] = [
        'open'  => $r['open_time'],
        'close' => $r['close_time']
    ];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content profile-setting-container" id="mainContent">
        <div class="card">
            <h3 class="heading custom-heading mb-5">Garage Setting</h3>
            <form action="garage-setting" method="POST" class="ajax_form">
                <?php
                $days = [
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                    'Sunday'
                ];

                foreach ($days as $day):

                    $checked = isset($garageSchedule[$day]);
                    $open    = $checked ? $garageSchedule[$day]['open'] : '';
                    $close   = $checked ? $garageSchedule[$day]['close'] : '';
                ?>
                    <div class="row align-items-center mb-2">

                        <!-- Day Checkbox -->
                        <div class="col-md-3">
                            <input type="checkbox" class="tc-checkbox day-checkbox" data-label="<?= $day ?>" name="days[]" value="<?= $day ?>" <?= $checked ? 'checked' : '' ?>>
                        </div>

                        <!-- Open Time -->
                        <div class="col-md-3">
                            <input type="time"
                                name="open_time[<?= $day ?>]"
                                class="form-control time-field"
                                value="<?= $open ?>"
                                <?= $checked ? '' : 'disabled' ?>>
                        </div>

                        <!-- Close Time -->
                        <div class="col-md-3">
                            <input type="time"
                                name="close_time[<?= $day ?>]"
                                class="form-control time-field"
                                value="<?= $close ?>"
                                <?= $checked ? '' : 'disabled' ?>>
                        </div>

                        <div class="col-md-3 text-muted">
                            Open / Close Time
                        </div>

                    </div>
                <?php endforeach; ?>


                <div class="mt-5">
                    <input type="hidden" name="saveGarageSetting" value="<?= bc_code(); ?>">
                    <button class="btn" type="submit"><i class="fas fa-save"></i> Save Timetable</button>
                </div>
            </form>
        </div>
    </main>
    <?php require_once('./includes/js.php'); ?>
    <script>
        $(document).on('change', '.day-checkbox', function() {

            let row = $(this).closest('.row');

            if ($(this).is(':checked')) {
                row.find('.time-field').prop('disabled', false);
            } else {
                row.find('.time-field').prop('disabled', true).val('');
            }
        });
    </script>
</body>

</html>