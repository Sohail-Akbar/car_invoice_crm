<?php
assets_file([
    'font-awesome.min.css',
    'bootstrap.min.css',
    'custom.css',
    _DIR_ . "css/header.css"
], 'css', _DIR_ . "css");
?>
<?php $CSS_FILES_ = isset($CSS_FILES_) ? $CSS_FILES_ : []; ?>