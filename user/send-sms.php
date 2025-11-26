<?php
require_once('includes/db.php');
$page_name = 'Send SMS';

$JS_FILES_ = [
    _DIR_ . "js/select2.min.js",
];
$CSS_FILES_ = [
    "send-sms.css",
    _DIR_ . "css/select2.min.css",
];

$get_customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : null;

$sql = "
SELECT 
    c.id AS customer_id,
    CONCAT(c.fname, ' ', c.lname) AS name,
    c.contact AS phone,
    CONCAT(h.make, ' ', h.model) AS vehicle,
    h.reg_number AS regNumber
FROM users c
INNER JOIN customer_car_history h 
    ON c.id = h.customer_id
WHERE c.is_active = '1' AND h.is_active = 1 AND c.type = 'customer'
  AND c.company_id = " . LOGGED_IN_USER['company_id'] . " 
  AND c.agency_id = " . LOGGED_IN_USER['agency_id'] . " 
ORDER BY c.fname, c.lname";
$customer = $db->query($sql, ["select_query" => true]);
if (empty($customer)) $customer = [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content sendSMS-container" id="mainContent">
        <div class="send-sms-container">
            <div class="header-card">
                <div class="garage-header">
                    <h1><i class="fas fa-tools me-2"></i> AutoCare Garage - Service SMS System</h1>
                    <p>Manage vehicle services and send SMS notifications to customers</p>
                </div>
            </div>
            <div class="main-content-">
                <div class="sms-card">
                    <div class="card-header">
                        <i class="fas fa-sms me-2"></i> Send Service SMS
                    </div>
                    <div class="card-body">
                        <form action="customer" method="POST" class="ajax_form reset" data-reset="reset">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-users"></i>Select Customers for Service SMS
                                        </label>
                                        <!-- multiple="multiple" -->
                                        <select class="form-select" name="customer_id" id="customerSelect" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-sticky-note"></i>Service SMS Templates
                                        </label>
                                        <select name="service_template" class="form-control">
                                            <option value="">--- Select Template ----</option>
                                            <option value="service_reminder">🔔 Service Reminder</option>
                                            <option value="service_completed">✅ Service Completed</option>
                                            <option value="service_delay">⏱ Service Delay</option>
                                            <option value="pickup_reminder">🚘 Pickup Reminder</option>
                                            <option value="regular_reminder">🚀 Regular Reminder</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-comment"></i>Service SMS Message
                                        </label>
                                        <textarea class="form-control" name="message" id="smsMessage" rows="4"
                                            placeholder="Type your service SMS message here..." required></textarea>
                                    </div>
                                </div>
                                <!-- Message Preview -->
                                <div class="col-md-12">
                                    <label class="form-label">
                                        <i class="fas fa-eye"></i>Message Preview
                                    </label>
                                    <div class="alert alert-info">
                                        <div id="messagePreview">Your service message will appear here...</div>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <input type="hidden" name="sendSMSToCustomers" value="true">
                                    <button type="submit" class="btn btn-send" id="sendBtn">
                                        <i class="fas fa-paper-plane me-2"></i>Send Service SMS
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once('./includes/js.php'); ?>
    <script>
        const _GET = <?= json_encode($_GET); ?>;
        // SMS Templates for garage
        const smsTemplates = <?= json_encode(sms_templates()) ?>;
        // Sample garage data
        const garageData = {
            customers: <?= json_encode($customer) ?>
        };
        // Initialize Select2 with garage customers
        function initializeSelect2() {
            const options = garageData.customers.map(customer =>
                `<option value="${customer.customer_id}" data-phone="${customer.phone}" ${_GET.customer_id == customer.customer_id ? "selected" : ""}>
            ${customer.name} - ${customer.vehicle} (${customer.regNumber})
        </option>`
            ).join('');

            $('#customerSelect').html(options).select2({
                placeholder: "Select customers for service SMS...",
                allowClear: true,
                width: '100%'
            });
        }
        initializeSelect2();

        // Fill SMS template into textarea when template is selected
        $('select[name="service_template"]').on('change', function() {
            const selectedTemplate = $(this).val();
            const templateText = smsTemplates[selectedTemplate] || "";

            $('#smsMessage').val(templateText).trigger('input');
        });

        function updatePreview() {
            let msg = $('#smsMessage').val();

            // Get first selected customer
            const firstCustomerId = $('#customerSelect').val()?.[0];

            if (firstCustomerId) {
                const customer = garageData.customers.find(c => c.customer_id == firstCustomerId);

                if (customer) {
                    msg = msg
                        .replaceAll("{name}", customer.name)
                        .replaceAll("{vehicle}", customer.vehicle)
                        .replaceAll("{regNumber}", customer.regNumber);
                }
            }

            // Update preview box
            $('#messagePreview').text(msg || "Your service message will appear here...");
        }

        $('#smsMessage').on('input', updatePreview);
        $('#customerSelect').on('change', updatePreview);
    </script>
</body>

</html>