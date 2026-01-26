<?php
define('DIR', '../');
require_once(DIR . 'includes/db.php');


if (isset($_POST['saveGarageSetting'])) {

    $agency_id = LOGGED_IN_USER['agency_id'];
    $company_id = LOGGED_IN_USER['company_id'];

    // Delete old timetable
    $db->delete("garage_working_hours", [
        "agency_id" => $agency_id,
        "company_id" => $company_id
    ]);

    if (!empty($_POST['days'])) {
        foreach ($_POST['days'] as $day) {

            // Get open and close times, or default full day
            $open  = isset($_POST['open_time'][$day]) && !empty($_POST['open_time'][$day])
                ? $_POST['open_time'][$day]
                : "00:00"; // default open

            $close = isset($_POST['close_time'][$day]) && !empty($_POST['close_time'][$day])
                ? $_POST['close_time'][$day]
                : "24:00"; // default close

            // Ensure proper HH:MM:SS format
            $open  = date("H:i:s", strtotime($open));
            $close = date("H:i:s", strtotime($close));

            // Handle cross-midnight scenario
            if (strtotime($close) <= strtotime($open)) {
                // Example: open 23:00, close 09:00 → we will treat as next day close
                $close = "24:00:00"; // optional, or handle specially in calendar
            }

            // Insert into DB
            $db->insert("garage_working_hours", [
                "company_id" => $company_id,
                "agency_id" => $agency_id,
                "day" => $day,
                "open_time" => $open,
                "close_time" => $close
            ]);
        }
    }

    returnSuccess("Save Setting Successfully", ["redirect" => ""]);
}



if (isset($_POST['fetchGarageTimetable'])) {

    $agency_id  = LOGGED_IN_USER['agency_id'];
    $company_id = LOGGED_IN_USER['company_id'];

    $rows = $db->select("garage_working_hours", "*", [
        "agency_id"  => $agency_id,
        "company_id" => $company_id
    ]);

    $businessHours = [];

    foreach ($rows as $r) {

        // FullCalendar day numbers
        // 0 = Sunday, 1 = Monday ...
        $map = [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6
        ];

        $businessHours[] = [
            'daysOfWeek' => [$map[$r['day']]],
            'startTime' => substr($r['open_time'], 0, 5),
            'endTime'   => substr($r['close_time'], 0, 5)
        ];
    }

    echo json_encode([
        'status' => 'success',
        'businessHours' => $businessHours
    ]);
    exit;
}
