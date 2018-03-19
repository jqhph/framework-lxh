<?php $language = translator();?>
<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">
    <div class="text-center">
        <a href="/" class="logo"><span><?php echo $language->translate('title');?></span></span></a>
        <h5 class="text-muted m-t-0 font-600"><?php echo $language->translateWithGolobal('project-desc')?></h5>
    </div>
    <div class="m-t-40 card-box portlet">
        <div class="text-center" style="display: none">
            <h4 class="text-uppercase font-bold m-b-0"><?php echo $language->translate('sign in')?></h4>
        </div>
        <div class="panel-body">
            <form class="form-horizontal m-t-20 login-form" onsubmit="return false">

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

                <!-- /admin/captcha -->
                <div class="form-group m-t-10 captcha" style="">
                    <div class="col-xs-12">
                        <img style="float:left;height:36px;width:59%" src="/admin/captcha">
                        <input style="width:40%" class="form-control" name="captcha" type="text" data-parsley-length="[4, 20]"
                               placeholder="<?php echo trans('Captcha')?>">
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

                <div class="form-group text-center m-t-30 log-btn">
                    <div class="col-xs-12">
                        <button class="  btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">
                            <?php echo $language->translate('log in');?>
                        </button>
                    </div>
                </div>

                <div class="form-group m-t-30 m-b-0">
                    <div class="col-sm-12">
                        <a href="/page-recoverpw" class="text-muted"><i class="fa fa-lock m-r-5"></i> <?php echo $language->translate('forgot')?></a>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <!-- end card-box-->

    <div class="row">
        <div class="col-sm-12 text-center">
            <p class="text-muted"><?php echo $language->translate('unaccount')?> <a href="/register" class="text-primary m-l-5"><b>
                        <?php echo $language->translate('sign up');?></b></a></p>
        </div>
    </div>

</div>
<script>
    require_js(['@lxh/js/validate.min', '@lxh/js/login/index']);
</script>