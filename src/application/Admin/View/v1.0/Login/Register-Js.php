<script>
    function lxh_action() {
        var language = $lxh.language

        var v = $lxh.formValidator([
            {
                name: 'username',
                rules: 'length_between[4-20]',
            },
            {
                name: 'password',
                rules: 'length_between[4-30]'
            },
            {
                name: 'repassword',
                rules: 'matches[password]'
            },
            {
                name: 'terms',
                rules: 'required'
            },

        ], submit)

        var notify = $lxh.ui.notify()
        var model = $lxh.createModel('User')

        function submit(e) {
            if (! model.requestEnded()) {
                return
            }

            notify.remove()
            notify.info(language.trans('loading'))

            // 设置成功回调函数
            model.on('success', function (data) {
                // success
                notify.remove()
                notify.success(language.trans('Successful registration.'))

                console.log('success: ', data)
            })
            // 发起登录请求
            model.touchAction('Register', 'POST')

        }
    }
</script>