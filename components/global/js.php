<?php
// Protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    ? 'https://'
    : 'http://';


// Remove fragment (#) if somehow exists
$fullUrl = strtok($protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '#');

?>

<script>
    const GLOBAL_GET = <?= json_encode($_GET) ?>;
    const MAIN_PATH = <?= json_encode($fullUrl) ?>;
    const _CURRENCY_SYMBOL = '<?= _CURRENCY_SYMBOL ?>';
    const _SITE_URL = '<?= SITE_URL ?>';
    const __CURRENCY_SYMBOL = '<?= _CURRENCY_SYMBOL ?>';
</script>
<?php assets_file([
    "jquery.min.js",
    "popper.min.js",
    "bootstrap.min.js",
    "sweetalert.min.js",
    "functions.js",
    "tc.jquery.fn.js",
    "tc.jquery.js",
    "tc.forms.js",
    "tc.fn.js",
    // "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
], 'js', _DIR_ . "js");


assets_file($JS_FILES_, 'js', 'js');
