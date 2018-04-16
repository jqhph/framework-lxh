<?php $language = translator();?>
<div id="account-pages" class="account-pages"></div>
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
                        <input class="form-control" name="username" type="text" placeholder="<?php echo trans('Username')?>">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" name="password" type="password" placeholder="<?php echo trans('Password')?>">
                    </div>
                </div>

                <div class="form-group m-t-10 captcha" style="display:none">
                    <style>.captcha .parsley-required{padding-left:59%}</style>
                    <div class="col-xs-12">
                        <img style="float:left;height:36px;width:59%;cursor:pointer">
                        <input style="width:40%" class="form-control" name="captcha" type="text"
                               placeholder="<?php echo trans('Captcha')?>" value="55555">
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

                <input  name="_token" type="hidden" value="<?php echo csrf_token()?>">
            </form>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 text-center">
            <p class="text-muted"><?php echo $language->translate('unaccount')?> <a href="/register" class="text-primary m-l-5"><b>
                        <?php echo $language->translate('sign up');?></b></a></p>
        </div>
    </div>

</div>
<script>
    $('#account-pages').css('height', $(window).height());
require_js(['@lxh/js/particles.min', '@lxh/js/validate.min', '@lxh/js/login/index']);
__then__(function () {
    particlesJS("account-pages", {
        particles: {
            number: {value: 40, density: {enable: !0, value_area: 388}},
            color: {value: "#399c9c"},
            shape: {type: "circle", stroke: {width: 0, color: "#000000"}, polygon: {nb_sides: 5}},
            opacity: {value: .3, random: !1, anim: {enable: !1, speed: 1, opacity_min: .2, sync: !1}},
            size: {value: 20, random: !0, anim: {enable: !1, speed: 50, size_min: .1, sync: !1}},
            line_linked: {enable:0, distance: 250, color: "#40afaf", opacity: .3, width: 1},
            move: {
                enable: !0,
                speed: 3,
                direction: "none",
                random: !0,
                straight: !1,
                out_mode: "out",
                bounce: !0,
                attract: {enable: !1, rotateX: 600, rotateY: 1200}
            }
        },
        interactivity: {
            detect_on: "canvas",
            events: {onhover: {enable: !0, mode: "grab"}, onclick: {enable: !0, mode: "push"}, resize: !0},
            modes: {grab: {distance: 140, line_linked: {opacity: 1}}}
        },
        retina_detect: !0
    });
    <?php if ($requiredCaptcha) {?>
    show_captcha();
    <?php } ?>
});

</script>