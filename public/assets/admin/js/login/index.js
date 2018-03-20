(function () {
    var rules = [
        {
            name: 'username',
            rules: 'required|length_between[4-20]',
        },
        {
            name: 'password',
            rules: 'required|length_between[4-30]'
        }
    ],
        form = '.login-form', v, showcaptcha = false, model, ntf, $img;

    __then__(function (plugIns) {
        v = $lxh.validator(rules, submit, form);
        model = $lxh.createModel('Admin', form);
        ntf = $lxh.ui().notify();
    });

    function submit(e) {
        if (! model.requestEnded()) {
            return ntf.warning(trans('Logging in, please wait a moment'))
        }

        ntf.remove();
        ntf.info(trans('loading'));

        // 设置成功回调函数
        model.on('success', function (data) {
            // success
            ntf.remove();
            ntf.success(trans('login success'));

            // 500豪秒后跳转到首页
            $lxh.redirect(data.target || '/admin', 500)
        });

        model.on('failed', function (data) {
            ntf.remove();
            switch (data.status) {
                // 失败次数过多，显示验证码
                case 10047:
                    show_captcha();
                    break;
                case 10049:
                    $img.click();
                default:
                    ntf.error(trans(data.msg));

            }
        });

        // 发起登录请求
        model.request('/admin/api/login', 'POST')

    }

    function show_captcha() {
        if (!showcaptcha) {
            showcaptcha = true;
            var $capt =  $('.captcha'), src = '/admin/captcha';
            $img = $capt.find('img');
            $capt.find('input').val('');

            $capt.show();
            $img.attr('src', src);
            $img.off('click');
            $img.click(function () {
                $img.attr('src', src + '?_=' + new Date().getTime());
            });

            rules.push({name: 'captcha', rules: 'required'});
            v = $lxh.validator(rules, submit, form);
        }
    }

    window.show_captcha = show_captcha;
})(window);