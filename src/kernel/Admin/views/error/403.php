<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo config('admin.title')?></title>
    <meta name="description" content="<?php echo config('admin.description');?>">
    <meta name="keywords" content="<?php echo config('admin.keywords');?>">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="<?php echo config('admin.favicon'); ?>">
</head>
<body style="background: #ebeff2">
<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">
    <div class="ex-page-content text-center">
        <div class="text-error" style="font-size:50px;">403</div>
        <h3 class="text-uppercase font-600"><?php echo empty($msg) ? 'Fobbiden' : $msg; ?></h3>
        <!--        <p class="text-muted"></p>-->
        <br>
        <!--        <a class="btn btn-success waves-effect waves-light" href="index.html"> Return Home</a>-->
    </div>
</div>
<?php
echo admin_css('css/bootstrap.min');
//echo admin_css('css/pages.min');
?>
</body>
</html>