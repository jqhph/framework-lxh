<!DOCTYPE html>
<html>
<?php $language = language();?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <!-- App Favicon -->
    <link rel="shortcut icon" href="<?php echo load_img('favicon.ico'); ?>">

    <!-- App title -->
    <title><?php echo $language->translateWithGolobal('web-title');?></title>

    <!-- App CSS -->
    <?php
        echo load_js('util');

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
        <a href="index.html" class="logo"><span><?php echo $language->translate('title');?></span></a>
        <h5 class="text-muted m-t-0 font-600"><?php echo $language->translateWithGolobal('project-desc')?></h5>
    </div>
    <div class="m-t-40 card-box">
        <div class="text-center">
            <h4 class="text-uppercase font-bold m-b-0"><?php echo trans_with_global('register');?></h4>
        </div>
        <div class="panel-body">
            <form class="form-horizontal m-t-20 User-form" action onsubmit="return false" >


                <div class="form-group ">
                    <div class="col-xs-12">
                        <input name="username"  class="form-control form-username" type="text"  placeholder="<?php echo trans('Username')?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control password" name="password" type="password"  placeholder="<?php echo trans('Password')?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" type="password" name="repassword"  placeholder="<?php echo trans('Confirm Password')?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <div class="checkbox checkbox-custom">
                            <input id="checkbox-signup" name="terms" type="checkbox" checked="checked">
                            <label for="checkbox-signup"><?php echo trans('iAccept')?>
                                <a href="#">
                                    <?php echo trans_with_global('terms')?>
                                    <?php echo trans_with_global('and')?>
                                    <?php echo trans_with_global('conditions')?>
                                </a></label>
                        </div>
                    </div>
                </div>

                <div class="form-group text-center m-t-40">
                    <div class="col-xs-12">
                        <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light submit"  type="submit" name="submit">
                            <?php echo ucfirst(trans_with_global('register'));?>
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
    <!-- end card-box -->

    <div class="row">
        <div class="col-sm-12 text-center">
            <p class="text-muted"><?php echo trans('haveAccount'); ?><a href="page-login.html" class="text-primary m-l-5"><b>
                        <?php echo trans_with_global('sign in');?></b></a></p>
        </div>
    </div>

</div>
<!-- end wrapper page -->
<script>add_js(parse_view_name('Login', 'Register'))</script>
<?php echo render_view('public.app-js');?>

</body>
</html>