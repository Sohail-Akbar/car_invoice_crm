<!-- Meta -->
<?php
$page_name .= " ";
require_once global_file('meta'); ?>

<!-- CSS -->
<?php
// Global CSS
require_once global_file('css');
// Admin CSS
assets_file(_DIR_ . 'css/user/styles.css', 'css');
assets_file(DIR . 'css/responsive.css', 'css');
// assets_file(DIR . 'css/styles.css', 'css');
?>

<?php assets_file($CSS_FILES_, 'css', 'css'); ?>