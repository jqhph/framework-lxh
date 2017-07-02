<script>
    function LxhAction() {
        var $parsley = $('form').parsley({});

        var language = $lxh.language

        $lxh.createModel('Test').request('/test/Global.json', 'GET')

        $('.submit').click(function (e) {
            if (!$parsley.isValid()) {
                return
            }
            var notify = $lxh.ui.notify()
            notify.remove()
            notify.info(language.trans('loading'))

            var model = $lxh.createModel('User')

            // 设置成功回调函数
            model.on('success', function (data) {
                // success
                notify.remove()
                notify.success(language.trans('login success'))

                console.log('success: ', data)
            })
            // 发起登录请求
            model.touchAction('Register', 'POST')

        })
    }
</script>