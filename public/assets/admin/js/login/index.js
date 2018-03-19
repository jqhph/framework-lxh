__then__(function (plugIns) {
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
        form = '.login-form',
        v = $lxh.validator(rules, submit, form);

    var model = $lxh.createModel('Admin', form);
    var notify = $lxh.ui().notify();

    function submit(e) {
        if (! model.requestEnded()) {
            return notify.warning(trans('Logging in, please wait a moment'))
        }

        notify.remove();
        notify.info(trans('loading'));

        // 设置成功回调函数
        model.on('success', function (data) {
            // success
            notify.remove();
            notify.success(trans('login success'));

            // 500豪秒后跳转到首页
            $lxh.redirect(data.target || '/admin', 500)
        });
        
        model.on('failed', function (data) {
            switch (data.status) {
                // 失败次数过多，显示验证码
                case 10047:
                    var $capt =  $('.captcha'), $img = $capt.find('img'), src = '/admin/captcha';

                    $capt.show();
                    $img.attr('src', src);
                    $img.off('click');
                    $img.click(function () {
                        $img.attr('src', src + '?_=' + new Date().getTime());
                    });

                    rules.push({name: 'captcha', rules: 'required'});
                    v = $lxh.validator(rules, submit, form);
            }
        });
        
        // 发起登录请求
        model.request('/admin/api/login', 'POST')

    }
})