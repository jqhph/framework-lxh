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
    <style>.account-pages{position:absolute;height:100%;width:100%;background-size:cover}.wrapper-page{margin:5% auto;position:relative;width:420px}.text-center{text-align:center}.ex-page-content .text-error{color:#0072C6;text-shadow:rgba(0,114,198,.3) 5px 3px,rgba(0,114,198,.2) 3px 3px,rgba(0,114,198,.3) 6px 4px;font-size:88px;font-weight:700;line-height:150px}.text-uppercase{text-transform:uppercase}.h3,h3{font-size:24px}.h1,.h2,.h3,h1,h2,h3{margin-top:20px;margin-bottom:10px}.h1,.h2,.h3,.h4,.h5,.h6,h1,h2,h3,h4,h5,h6{font-family:inherit;font-weight:500;line-height:1.1;color:inherit}.text-muted{color:#777}p{margin:0 0 10px}</style>
</head>
<body>

    <div class="account-pages"></div>
    <div class="clearfix"></div>
    <div class="wrapper-page">
        <div class="ex-page-content text-center">
            <div class="text-error">500</div>
            <h3 class="text-uppercase font-600">Internal Server Error</h3>
            <p class="text-muted">
                Why not try refreshing your page? or you can contact <a href="" class="text-primary">support</a>
            </p>
            <br>
    <!--        <a class="btn btn-success waves-effect waves-light" href="index.html"> Return Home</a>-->
        </div>
    </div>
</body>
</html>
