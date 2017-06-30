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
//    load_css('toastr.min', 'plugins/toastr');
        // <!-- App CSS -->
//        load_css('bootstrap.min');
//        load_css('core');
//        load_css('components');
//        load_css('icons');
//        load_css('responsive');
//        load_css('pages');
//        load_css('menu');

//        load_js('modernizr.min');
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
                        <input class="form-control" name="username" type="text" data-parsley-length="[4, 20]" required="" placeholder="Username">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" name="password" type="password"  data-parsley-length="[5, 30]"  required="" placeholder="Password">
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
                        <button class=" submit btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">
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
            <p class="text-muted"><?php echo $language->translate('unaccount')?> <a href="page-register.html" class="text-primary m-l-5"><b>
                        <?php echo $language->translateWithGolobal('sign up');?></b></a></p>
        </div>
    </div>

</div>
<!-- end wrapper page -->

<?php
load_js('jquery.min');
load_js('sea');

//load_js('parsley.min', 'plugins/parsleyjs/dist');
//load_js('container');
//load_js('toastr.min', 'plugins/toastr');
////load_js('bootstrap.min');
//load_js('jquery.core');
?>

<script>
    seajs.config({
        // 设置路径，方便跨目录调用
        paths: {
            's': '/static/v1.0',
        },
        // 设置别名，方便调用
        alias: {
            'jquery': 's/js/jquery.min',
            'parsley': 's/plugins/parsleyjs/dist/parsley.min',
            'container': 's/js/container',
            'toastr': 's/plugins/toastr/toastr.min',
            'core': 's/js/jquery.core',
        }

    });
    seajs.use(['s/plugins/toastr/toastr.min.css',
        's/css/bootstrap.min.css',
        's/css/core.css',
        's/css/components.css',
        's/css/icons.css',
        's/css/responsive.css',
        's/css/pages.css',])
    
    var options = ['parsley', 'toastr', 'container', 'core']
    seajs.use(options, function (parsley, toastr) {
        var $parsley = $('form').parsley({});

        Lxh.createModel('Test').request('/test/Global.json', 'GET')

        $('.submit').click(function (e) {
            if (!$parsley.isValid()) {
                return
            }
            var notify = Lxh.ui.notify()
            notify.remove()
            notify.info('loading')

            var model = Lxh.createModel('User')
            // 设置成功回调函数
            model.on('success', function (data) {
                // success
                notify.remove()
                notify.success('登录成功，即将跳转到首页！')

                console.log('success: ', data)
            })
            // 发起登录请求
            model.touchAction('Login', 'POST')

        })

    })

</script>

</body>
</html>