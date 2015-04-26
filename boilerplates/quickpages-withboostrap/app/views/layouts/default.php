<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <title><?php echo $this->title; ?></title>
<?php
    echo $this->assets('auto', [
        'css/bootstrap.min.css',
        'css/site.css',
    ]);
?>
</head>
<body>

    <div class="container">
        <?php $this->layoutContent(); ?>
    </div>

<?php
    echo $this->assets('auto', [
        'js/jquery-2.1.3.min.js',
        'js/bootstrap.min.js',
    ]);
?>
</body>
</html>