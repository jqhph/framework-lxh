<!DOCTYPE html>
<html>
<head>
    <?php

    $language = language();
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $language->translateWithGolobal('web-description');?>">
    <meta name="author" content="Coderthemes">

    <!-- App Favicon -->
    <link rel="shortcut icon" href="<?php load_img('favicon.ico'); ?>">

    <!-- App title -->
    <title><?php echo $language->translateWithGolobal('web-title');?></title>

    <?php
        load_css('bootstrap.min');

        load_js('jquery.min');

        echo fetch_view('app-js', 'Public', ['loadCurrentJs' => true]);
    ?>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

</head>
<body>

<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">
    <div class="text-center">
        <a href="/" class="logo"><span><?php echo $language->translate('title');?></span></span></a>
        <h5 class="text-muted m-t-0 font-600"><?php echo $language->translateWithGolobal('project-desc')?></h5>
    </div>
    <div class="m-t-40 card-box portlet">
        <div class="text-center">
            <h4 class="text-uppercase font-bold m-b-0"><?php echo $language->translate('sign in')?></h4>
        </div>
        <div class="panel-body">
            <form class="form-horizontal m-t-20 User-form" onsubmit="return false">

                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" name="username" type="text" data-parsley-length="[4, 20]"
                               placeholder="<?php echo trans('Username')?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" name="password" type="password"  data-parsley-length="[4, 30]"
                               placeholder="<?php echo trans('Password')?>">
                    </div>
                </div>

                <div class="form-group ">
                    <div class="col-xs-12">
                        <div class="checkbox checkbox-custom">
                            <input id="checkbox-signup" name="remember" type="checkbox">
                            <label for="checkbox-signup">
                                <?php echo $language->translate('remember'); ?>

                            </label>
                        </div>

                    </div>
                </div>

                <div class="form-group text-center m-t-30">
                    <div class="col-xs-12">
                        <button class="  btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">
                            <?php echo $language->translate('log in');?>
                        </button>
                    </div>
                </div>

                <div class="form-group m-t-30 m-b-0">
                    <div class="col-sm-12">
                        <a href="page-recoverpw.html" class="text-muted"><i class="fa fa-lock m-r-5"></i> <?php echo $language->translate('forgot')?></a>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <!-- end card-box-->

    <div class="row">
        <div class="col-sm-12 text-center">
            <p class="text-muted"><?php echo $language->translate('unaccount')?> <a href="/Register" class="text-primary m-l-5"><b>
                        <?php echo $language->translateWithGolobal('sign up');?></b></a></p>
        </div>
    </div>

</div>
<!-- end wrapper page -->

<?php


//load_js('parsley.min', 'plugins/parsleyjs/dist');
//load_js('container');
//load_js('toastr.min', 'plugins/toastr');
////load_js('bootstrap.min');
//load_js('jquery.core');
?>



</body>
</html>