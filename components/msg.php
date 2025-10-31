<?php
if (!defined('DIR')) define('DIR', './');
if (!isset($type)) {
    $type = "error";
    if (!isset($msg))
        $msg = "Something Went Wrong! Pls try again";
}
$image = strtolower($type) . '.png';
// Image Dir
$image_dir = DIR;
if (isset($msg_image_dir)) $image_dir = $msg_image_dir;

$page_name = $msg;
$site_name = SITE_NAME;

$showFullHtml = isset($fullHtml) ? $fullHtml : true;

$size = isset($size) ? $size : 'medium';
$size1 = 'col-md-6 offset-md-3';
if ($size === "medium")
    $size1 = 'col-md-6 offset-md-3';
if ($size === "large")
    $size1 = 'col-md-12';

$exitProgram = isset($exit) ? $exit : false;
?>
<?php if ($showFullHtml) { ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            <?php echo $page_name . ' - ' . $site_name; ?>
        </title>
        <link rel="stylesheet" href="<?= DIR ?>css/bootstrap.min.css">
        <link rel="stylesheet" href="<?= DIR ?>css/custom.css">
        <link rel="stylesheet" href="<?= DIR ?>css/styles.css">
    </head>

    <body class="content-center">
    <?php } ?>
    <div class="container-fluid">
        <div class="row">
            <div class="<?= $size1 ?>">
                <div class="card p-3">
                    <div class="card-body text-center">
                        <div class="justify-center">
                            <div class="col-lg-3 col-md-6">
                                <img src="<?= $image_dir ?>images/<?= $image ?>" alt="image" class="img-fluid" />
                            </div>
                        </div>
                        <p class="mt-3">
                            <?= $msg ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showFullHtml) { ?>
    </body>

    </html>
<?php
        if ($exitProgram)
            die();
    }
?>