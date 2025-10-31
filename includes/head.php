<!-- Meta -->
<?php
require_once global_file('meta'); ?>

<!-- CSS -->
<?php
// Global CSS
require_once global_file('css');
// Admin CSS
assets_file(DIR . 'css/styles.css', 'css');
?>

<?php assets_file($CSS_FILES_, 'css', 'css'); ?>