<?php
require_once('includes/db.php');
$page_name = 'Email Template';

$JS_FILES_ = [
    _DIR_ . "js/select2.min.js",
    _DIR_ . "js/tinymce/tinymce.min.js",
    _DIR_ . "js/jquery.dataTables.min.js",
];
$CSS_FILES_ = [
    _DIR_ . "css/select2.min.css",
    _DIR_ .  "css/jquery.dataTables.min.css",
];
$id = isset($_GET['id']) ? $_GET['id'] : null;

$email_template = [];
if ($id) {
    $email_template = $db->select_one("email_template", "id,email_title,email_body", [
        "agency_id" => LOGGED_IN_USER['agency_id'],
        "company_id" => LOGGED_IN_USER['company_id'],
        "id" => $id
    ], [
        "order_by" => "id desc"
    ]);
    $email_template['email_body'] = htmlspecialchars_decode($email_template['email_body']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('./includes/head.php'); ?>
    <style>
        .custom-heading::after {
            width: 45%;
        }

        .custom-heading::before {
            left: 45%;
        }

        .tox-statusbar {
            display: none !important;
        }
    </style>
</head>

<body>
    <?php require_once('./includes/header.php'); ?>
    <main class="main-content add-role-container" id="mainContent">
        <div class="card">
            <h3 class="heading mb-5 custom-heading">Define/Update Email Templates</h3>
            <form action="customer" method="POST" class="ajax_form reset" data-reset="reset">
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="form-group">
                            <span class="label">Email Template Head:</span>
                            <input type="text" name="email_title" class="form-control" placeholder="Email Title" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <small class="label">Available Short Codes</small>
                            <div class="customerShortCode">
                                <?php
                                $shortCodes = getCustomerShortCode();
                                $total = count($shortCodes);
                                $i = 1;

                                foreach ($shortCodes as $label => $code) {
                                    echo $label . ' = [' . $code . ']';

                                    // Add comma only if NOT last item
                                    if ($i < $total) {
                                        echo ', ';
                                    }

                                    $i++;
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="label">Detail</div>
                        <textarea class="customer-note form-control" id="customerNote" rows="6.8" name="email_body" placeholder="Start typing to leave a note..."></textarea>
                    </div>
                    <div class="col-12 mt-2">
                        <input type="hidden" name="saveEmailTemplates" value="<?= bc_code(); ?>">
                        <?php if ($id) { ?>
                            <input type="hidden" name="id" value="<?= $id ?>">
                        <?php } ?>
                        <button class="btn br-5" type="submit"><i class="fas fa-save"></i> Save</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive mt-5">
            <table class="table table-striped emailTemplatesTable">
                <thead style="background: var(--webMainColor);color: white;">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Body</th>
                        <!-- <th>Status</th> -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </main>
    <script>
        const EMAIL_TEMPLATE_DATA = <?= json_encode($email_template); ?>
    </script>
    <?php require_once('./includes/js.php'); ?>
    <script>
        $(document).ready(function() {
            tinymce.init({
                selector: '#customerNote',
                height: 300, // thoda bada height better view ke liye
                menubar: true, // menubar enable
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount',
                    'textcolor colorpicker'
                ],
                toolbar: 'undo redo | formatselect | ' +
                    'bold italic underline strikethrough | forecolor backcolor | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist outdent indent | removeformat | help',
                font_formats: 'Serif=serif; Sans-serif=sans-serif; Arial=arial,helvetica,sans-serif; Courier New=courier,courier new,monospace;',
                content_style: "body { font-family: 'Serif', sans-serif; line-height:0.5; }",
                setup: function(editor) {
                    const placeholderText = "Start typing to leave a note...";

                    function setPlaceholder() {
                        if (editor.getContent() === '') {
                            editor.setContent(`<p style="color:#888;">${placeholderText}</p>`);
                        }
                    }

                    editor.on('init', setPlaceholder);
                    editor.on('focus', function() {
                        if (editor.getContent().includes(placeholderText)) {
                            editor.setContent('');
                        }
                    });
                    editor.on('blur', setPlaceholder);
                }
            });
        });

        $(document).ready(function() {

            if ($(".emailTemplatesTable").length) {

                let emailTable = $('.emailTemplatesTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "controllers/customer?fetchEmailTemplates=true",
                        "type": "POST"
                    },
                    "pageLength": 10,
                    "lengthChange": true,
                    "columns": [

                        // Sr #
                        {
                            "data": "id",
                            "render": function(data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },

                        // Title + Created Date
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                return `
                            <strong>Title:</strong> ${row.email_title}<br>
                            <strong>Created:</strong> ${row.created_at}
                        `;
                            }
                        },
                        // Body
                        {
                            "data": "body",
                            "render": function(data, type, row) {
                                return row.email_body;
                            }
                        },
                        // Status
                        // {
                        //     "data": "is_active",
                        //     "render": function(data) {
                        //         let className = data == '1' ? 'bg-success' : 'bg-warning text-dark';
                        //         let statusText = data == '1' ? 'Active' : 'Inactive';
                        //         return `<span class="text-white p-1 bold small-font ${className}">${statusText}</span>`;
                        //     }
                        // },
                        // Action
                        {
                            "data": "actions",
                            "render": function(data, type, row) {
                                return `<i class="fas fa-trash text-danger tc-delete-btn" data-target="${row.id}" data-action="email_template"></i>
                                <a href="email-template?id=${row.id}"><i class="fas fa-edit  text-success cp "></i></a>`;
                            }
                        },
                    ],
                });

                // Custom Search Box Support
                $('.search-input').on('keyup', function() {
                    emailTable.search(this.value).draw();
                });

            }
        });

        $(document).ready(function() {
            if (GLOBAL_GET.id) {
                setTimeout(() => {
                    tinymce.get('customerNote').setContent(EMAIL_TEMPLATE_DATA.email_body);
                    $(`[name="email_title"]`).val(EMAIL_TEMPLATE_DATA.email_title);
                }, 1000);
            }
        });
    </script>
</body>

</html>