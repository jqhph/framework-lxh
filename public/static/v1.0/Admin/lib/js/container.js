/**
 * Created by Jqh on 2017/6/28.
 */
window.Lxh = function (options) {

    /**
     * 容器构造器
     *
     * @param options
     * @constructor
     */
    function Container(options) {
        var self = this, config, cache, store, user, language, view, ui, env, tpl, util, urlMaker

        function init() {
            // 配置文件管理
            config = new Store(options.config || {})

            // 工具函数管理
            util = new Util()

            // 缓存管理
            cache = options.cache

            // 存储仓库
            store = new Store()

            // 登陆用户信息管理
            user = new Store(options.users || {})

            // 语言包管理
            language = new Language(self, cache, config)

            // ui组件
            ui = new UI()

            // 环境管理
            env = new Env()

            // 模板管理器
            tpl = new Tpl(self, cache, config)

            // 视图管理
            view = new View(self, tpl)


            urlMaker = new UrlMaker(self)
        }

        /**
         * 获取控制器名称
         *
         * @returns {null|*|Chart.Controller}
         */
        this.controllerName = function () {
            return options.controller
        }

        /**
         * 获取项目模块名称
         *
         * @returns {string|handlers.module}
         */
        this.moduleName = function () {
            return options.module
        }

        /**
         * 获取动作名称
         *
         * @returns {*}
         */
        this.actionName = function () {
            return options.action
        }

        /**
         * 获取请求参数
         *
         * @return {object}
         */
        this.requestParams = function () {
            return options.params
        }

        /**
         *
         * @returns {Store}
         */
        this.config = function () {
            return config
        }

        /**
         *
         * @returns {Cache}
         */
        this.cache = function () {
            return cache
        }

        /**
         *
         * @returns {Store}
         */
        this.store = function () {
            return store
        }

        /**
         *
         * @returns {Store}
         */
        this.user = function () {
            return user
        }

        /**
         *
         * @returns {Language}
         */
        this.language = function () {
            return language
        }

        /**
         *
         * @returns {View}
         */
        this.view = function () {
            return view
        }

        /**
         * 
         * @returns {UI}
         */
        this.ui = function () {
            return ui
        }

        /**
         *
         * @returns {Env}
         */
        this.env = function () {
            return env
        }

        /**
         *
         * @returns {Tpl}
         */
        this.tpl = function () {
            return tpl
        }

        this.util = function () {
            return util
        }

        /**
         * url管理
         *
         * @returns {*}
         */
        this.url = function () {
            return urlMaker
        }

        // 初始化
        init()
    }

    Container.prototype = {
        // 状态码
        statusCode: {
            success: 10001,
            failed: 10002,
            invalid_arguments: 10003,
        },

        // 设置状态码
        setStatusCode: function (o) {
            this.statusCode = o
        },

        /**
         * 缓存数据到容器
         *
         * @param name
         * @param val
         * @returns {Container}
         */
        set: function (name, val) {
            this.store().set(name, val)
            return this
        },

        /**
         * 获取容器缓存的数据
         *
         * @param name
         * @param {*}
         */
        get: function (name, $def) {
            return this.store().get(name, $def)
        },

        /**
         * 跳转
         *
         * @param routePath
         * @param timeout
         */
        redirect: function (routePath, timeout) {
            if (! timeout) {
                return window.location = routePath
            }
            setTimeout(function () {
                window.location = routePath
            }, timeout)
        },

        /**
         * 创建一个模型
         *
         * @param name    模型名称
         * @param module  项目模块名称
         * @returns {Model}
         */
        createModel: function (name, module) {
            name = name || this.controllerName()
            return new Model(name, module, this)
        },

        /**
         * 创建一个store对象
         *
         * @param data
         * @returns {Store}
         */
        createStore: function (data) {
            return new Store(data)
        },
        /**
         * 获取表单处理器
         *
         * @returns {Form}
         */
        form: function () {
            return this.get('form') || this.set('form', new Form()).get('form')
        },

        /**
         * 初始化表单验证对象
         * 用法参考：http://rickharrison.github.io/validate.js/
         *
         * @param options  表单验证配置数组
         * @param call     验证通过后执行的毁掉函数
         * @param selector form表单CSS选择器
         * @returns {FormValidator}
         */
        validator: function (options, call, selector) {
            return validator(options, call, selector)
        }
    }

    /*
      --------------------------------------------------------------------------------------
     |  往下为js组件
     |
     ---------------------------------------------------------------------------------------
    */

    /**
     * -------------------------------------------------------------------------------------
     * 工具函数集合
     *
     * @returns {{}}
     * @constructor
     */
    function Util() {
        return {
            /**
             * 比较两个对象是否一致
             *
             * @param x
             * @param y
             * @returns {boolean}
             */
            cmp: function (x, y) {
                // If both x and y are null or undefined and exactly the same
                if (x === y) {
                    return true;
                }

                // If they are not strictly equal, they both need to be Objects
                if (!( x instanceof Object ) || !( y instanceof Object )) {
                    return false;
                }

                //They must have the exact same prototype chain,the closest we can do is
                //test the constructor.
                if (x.constructor !== y.constructor) {
                    return false;
                }

                for (var p in x) {
                    //Inherited properties were tested using x.constructor === y.constructor
                    if (x.hasOwnProperty(p)) {
                        // Allows comparing x[ p ] and y[ p ] when set to undefined
                        if (!y.hasOwnProperty(p)) {
                            return false;
                        }

                        // If they have the same strict value or identity then they are equal
                        if (x[p] === y[p]) {
                            continue;
                        }

                        // Numbers, Strings, Functions, Booleans must be strictly equal
                        if (typeof( x[p] ) !== "object") {
                            return false;
                        }

                        // Objects and Arrays must be tested recursively
                        // if (!Object.equals(x[p], y[p])) {
                        //     return false;
                        // }
                        if (! this.cmp(x[p], y[p])) {
                            return false
                        }
                    }
                }

                for (p in y) {
                    // allows x[ p ] to be set to undefined
                    if (y.hasOwnProperty(p) && !x.hasOwnProperty(p)) {
                        return false;
                    }
                }
                return true;
            }
        }
    }
    // --------------------------------------Util END-----------------------------------------------


    /**
     * -------------------------------------------------------------------------------------
     * URL管理
     *
     * @returns {{}}
     * @constructor
     */
    function UrlMaker(container) {
        var store = {
            prefix: '/lxhadmin'
        }

        return {
            makeAction: function (a, c) {
                a = a || container.actionName()
                c = c || container.controllerName()

                return store.prefix + '/' + c + '/' + a
            },

            makeHome: function () {
                return store.prefix
            }
        }
    }
    // --------------------------------------UrlMaker END-----------------------------------------------


    /**
     * -------------------------------------------------------------------------------------
     * UI组件
     *
     * @returns {{loading: loading, notify: notify}}
     * @constructor
     */
    function UI() {
        return {
            /**
             * loading
             *
             * @param selector loading样式显示的位置CSS选择器
             * @param timeout  bool|int 当值为空时则显示loading样式；当值为false时则隐藏loading样式；当值为一个数值时则表示timeout毫秒后隐藏loading样式
             * @returns {*}
             */
            loading: function (selector, timeout) {
                selector = selector || '.loading'
                if (timeout === false) {
                    return close(selector)
                }
                return show(selector, timeout)
                function show(selector, timeout) {
                    var $portlet = $(selector).closest(selector);
                    // This is just a simulation, nothing is going to be reloaded
                    $portlet.append('<div class="panel-disabled"><div class="loader-1"></div></div>')
                    if (!timeout) return
                    setTimeout(function () {
                        close(selector)
                    }, timeout)
                }

                function close(selector) {
                    var $pd = $(selector).closest(selector).find('.panel-disabled');
                    $pd.fadeOut('fast', $pd.remove);
                }
            },

            /**
             * notify
             * 参考toastr
             *
             * @param options
             * @returns {*}
             */
            notify: function (options) {
                options = options || {}
                options.closeButton = options.closeButton || true
                options.positionClass = options.positionClass || 'toast-top-right'
                options.showMethod = options.showMethod || 'slideDown'
                //     closeButton: true,
                //     debug: false,
                //     newestOnTop: true,
                //     progressBar: false,
                //     positionClass: 'toast-top-right',// toast-top-full-width
                //     preventDuplicates: false,
                //     onclick: null,
                //     showDuration: 500,
                //     hideDuration: 500,
                //     showMethod: 'slideDown',
                //     hideMethod: 'fadeOut',
                //     //timeOut: 3000,
                toastr.options = options
                return toastr
            },

            // 弹窗
            modal: function (options, call) {
                options = options || {}
                options.closeButton = options.closeButton || true
                options.class = options.class || 'modal-container'

                options.title = trans(options.title) || ''
                options.content = options.content || ''
                options.saveButton = options.saveButton || true
                options.saveButtonClass = options.saveButtonClass || 'btn-primary'
                options.buttons = options.buttons || {}
                options.closeButtonLabel = trans(options.closeButtonLabel) || trans('Close')
                options.saveButtonLabel = trans(options.saveButtonLabel) || trans('Save')
                options.tpl = options.tpl || $('#modal-basic').text()

                var blade = new Blade(options.tpl, options)

                var $container = $('div.' + options.class)

                if ($container.length > 0) {
                    return $container.modal()
                }

                $('body').append(blade.fetch())

                $container = $('div.' + options.class)

                $container.find('button[data-action="modal-basic-close"]').click(close)

                var modal = $container.modal()

                if (call) {
                    $container.find('button[data-action="modal-basic-save"]').click(function () {
                        call(modal)
                    })
                }

                // 添加close事件
                modal.close = close

                return modal

                function close() {
                    $('.modal-backdrop').remove()
                    $container.remove()
                    $('body').removeClass('modal-open')
                }
            },
        }
    }
    // --------------------------------------UI END-----------------------------------------------

    /**
     * -------------------------------------------------------------------------------------
     * 视图管理
     *
     * @param container
     * @param tpls
     * @constructor
     */
    function View(container, tpl)
    {
        var store = {
            tpl: tpl
        }

        /**
         * 创建一个视图
         *
         */
        this.create = function (viewname, options) {

            return this
        }

        /**
         * 加载视图并初始化
         *
         * @param callback
         */
        this.then = function (callback) {

        }

        /**
         *
         * @param deps
         * @param callback
         */
        this.call = function (deps, callback) {
            seajs.use(deps, function () {

                var view = callback.call(arguments)
            })
        }
    }
    // --------------------------------------View END-----------------------------------------------

    /**
     * -------------------------------------------------------------------------------------
     * 环境管理
     */
    function Env() {
        return {
            val: 'dev',
            /**
             * 设置当前环境
             *
             * @param type
             */
            set: function (type) {
                this.val = type
            },

            /**
             * 是否是生产环境
             *
             * @returns {boolean}
             */
            isProd: function () {
                return this.val == 'prod'
            },

            /**
             * 是否是开发环境
             *
             * @returns {boolean}
             */
            isDev: function () {
                return this.val == 'dev'
            },

            /**
             * 是否是测试环境
             *
             * @returns {boolean}
             */
            isTest: function () {
                return this.val == 'test'
            }
        }
    }
    // --------------------------------------Env END-----------------------------------------------

    /**
     * -------------------------------------------------------------------------------------
     * 初始化表单验证对象
     * 用法参考：http://rickharrison.github.io/validate.js/
     *
     * @param options  表单验证配置数组
     * @param call     验证通过后执行的毁掉函数
     * @param selector form表单CSS选择器
     * @returns {FormValidator}
     */
    function validator(options, call, selector) {
        selector = selector || ('.' + $lxh.controllerName() + '-form')

        var self = this

        $(selector).submit(function () {
            return false;
        })

        var $form = document.querySelector(selector)
        var v = new FormValidator($form, options, function (errors, event) {
            if (errors.length < 1 && (event === 'submit' || event.type == 'submit')) {
                // 验证成功后回调
                typeof call != 'function' || call(event)
            }
        }, validate_call);

        // 注册自定义验证规则
        register_rules(v)

        add_events(options)

        // 给表单元素添加focus和keyup事件
        function add_events(options) {
            for (var key in options) {
                if (options.hasOwnProperty(key)) {
                    var field = options[key] || {},
                        element = $form[field.name]

                    if (element && element !== undefined) {
                        element.onfocus = element.onkeyup = function (e) {

                            v._validateForm(e)
                        }
                    }
                }
            }
        }

        // 显示错误信息
        function validate_call(field, errorObject) {
            var $e = $(field.element)
            // 移除表单错误
            remove_error($e, field.name)
            if (errorObject) {
                display_error_msg(field.name, $e, errorObject.message)
            }
        }

        /**
         * 显示表单错误
         *
         * @param name 表单name属性
         * @param e    表单dom元素
         * @param msg  错误信息
         */
        function display_error_msg (name, e, msg) {
            name = name.replace('[]', '')
            msg = trans(msg)
            e.addClass('parsley-error')
            e.parent().append('<ul class="parsley-errors-list filled validator-error-' + name + '" id="parsley-id-4"><li class="parsley-required">' + msg + '</li></ul>')
        }

        /**
         * 移除表单验证错误
         *
         * @param $e   表单dom元素
         * @param name 表单name属性
         */
        function remove_error($e, name) {
            name = name.replace('[]', '')
            $e.removeClass('parsley-error')
            $('.validator-error-' + name).remove()
        }

        /**
         * 注册自定义表单验证规则
         *
         * @param validator
         */
        function register_rules(validator) {
            // validator.registerCallback('length_between', function(value, param, field) {
            //     console.log(7890, value.length, param)
            //     return false
            // }).setMessage('length_between', 'This value length is invalid. It should be between 5 and 10 characters long.')

        }

        return v
    }
    // --------------------------------------Validator END-----------------------------------------------

    /**
     * -----------------------------------------------------------------------------------
     * 模板管理器
     *
     * @constructor
     */
    function Tpl(container, cache, config)
    {
        var store = {},
            expireTime = config.get('cache-expire'),
            cacheKey = 'tpls',
            useCache = config.get('use-cache'),
            defaultScope = container.controllerName()

        /**
         * 注入数据
         *
         * @param packages {object} <code>
         *     {"tplname":"<div>..."}
         *  </code>
         * @param save {bool} 是否缓存到localStore，默认false
         * @type {Language.fill}
         */
        var fill = this.fill = function (packages, save) {
            if (! packages) {
                // 如果没有数据，则从缓存中获取并注入
                packages = cache.get(cacheKey, {})
                save = false
            }
            for (var tplname in packages) {
                store[tplname] = packages[tplname]
            }
            if (save) {
                this.save(packages)
            }
        }

        /**
         * 缓存语言包
         *
         * @param packages
         */
        this.save = function (packages) {
            if (! useCache) {
                return
            }
            var cachePackage = {}, i
            cachePackage = cache.get(cacheKey, {})
            for (var tplname in packages) {
                cachePackage[tplname] = packages[tplname]
            }
            cache.set(cacheKey, cachePackage)
            cache.expire(cacheKey, expireTime)
        }

        /**
         * 获取模板，如果缓存中没有会从服务器中获取，如果缓存中有则直接从缓存中获取
         *
         * @param {array} names 模板名称数组
         * @param {function} call 获取成功后回调函数
         * @returns void
         */
        this.fetch = function (names, call) {
            names = typeof names == 'string' ? [names] : names
            if (useCache) {
                var packages = {}
                packages = cache.get(cacheKey)
                fill(packages)
            }

            // 取出缓存中没有的语言包模块
            var missingNames = []
            for (var i in names) {
                if (! store[names[i]]) {
                    missingNames.push(names[i])
                }
            }

            if (missingNames.length < 1) {
                // 缓存中有需要的语言包
                return call(store)
            }

            // 缓存中没有需要的语言包
            var model = container.createModel('Tpl')

            model.data({names: missingNames.join(',')})

            model.on('success', function (data) {
                // 注入并缓存
                fill(data.list, true)

                call(store)
            })
            model.touchAction('Get', 'POST')

        }

        this.all = function () {
            return store
        }

        /**
         * 获取模板内容
         *
         * @param name
         */
        this.get = function (name) {
            return store[name] || null
        }

        /**
         * 获取普通模块组件
         */
        this.module = function (name) {
            return store[defaultScope + '.' + name] || null
        }

        /**
         * 获取组件模板
         *
         * @param name
         * @returns {*}
         */
        this.component = function (name) {
            return store['component.' + name] || null
        }

        /**
         * 获取字段组件模板
         *
         * @param name
         */
        this.fields = function (name) {
            return store['component.fields.' + name] || null
        }
    }
    // --------------------------------------Tpl END-----------------------------------------------

    /**
     * -----------------------------------------------------------------------------------
     * 语言包管理
     *
     * @param container {Container}
     * @param cache     {Cache}
     * @param config    {Store}
     * @constructor
     */
    function Language(container, cache, config)
    {
        var store = {},
            cache = cache,
            lang = config.get('language'),
            defaultScope = container.controllerName(),
            self = this,
            cacheKeyPrefix = 'language_',
            cacheKey = cacheKeyPrefix + lang,
            useCache = config.get('use-cache'),
            expireTime = config.get('lang-package-expire')

        store[lang] = new Store()

        /**
         * 注入语言包数据
         *
         * @param packages {object} <code>
         *     {"en":{"Global": {...}, "Login": {...} ...} ...}
         *  </code>
         * @param save {bool} 是否缓存到localStore，默认false
         * @type {Language.fill}
         */
        var fill = this.fill = function (packages, save) {
            if (! packages) {
                // 如果没有数据，则从缓存中获取并注入
                packages = {}
                packages[lang] = cache.get(cacheKey)
                save = false
            }
            for (var language in packages) {
                if (! store[language]) {
                    store[language] = new Store()
                }
                for (var scope in packages[language]) {
                    store[language].set(scope, packages[language][scope])
                }
            }
            if (save) {
                this.save(packages)
            }
        }

        /**
         * 缓存语言包
         *
         * @param packages
         */
        this.save = function (packages) {
            if (! useCache) {
                return
            }
            var cachePackage = {}, i, key
            for (var lang in packages) {
                key = cacheKeyPrefix + lang
                cachePackage = cache.get(key, {})
                for (i in packages[lang]) {
                    cachePackage[i] = packages[lang][i]
                }
                cache.set(key, cachePackage)
                cache.expire(key, expireTime)
            }
        }

        /**
         * 获取语言包数据，此函数如果缓存中没有会从服务器中获取，如果缓存中有则直接从缓存中获取
         *
         * @param {array} scopes 语言包模块数组
         * @param {function} call 获取成功后回调函数
         * @returns void
         */
        this.fetch = function (scopes, call) {
            scopes = typeof scopes == 'string' ? [scopes] : scopes
            if (useCache) {
                var packages = {}
                packages[lang] = cache.get(cacheKey)
                fill(packages)
            }

            // 取出缓存中没有的语言包模块
            var missingScopes = []
            for (var i in scopes) {
                if (! store[lang].get(scopes[i])) {
                    missingScopes.push(scopes[i])
                }
            }

            if (missingScopes.length < 1) {
                // 缓存中有需要的语言包
                return call()
            }

            // 缓存中没有需要的语言包
            var model = container.createModel('Language')

            model.data({lang: lang, scopes: missingScopes.join(',')})

            model.on('success', function (data) {
                // 注入并缓存
                fill(data.list, true)

                call()
            })
            model.touchAction('Get', 'POST')

        }

        /**
         * 翻译，先从选中的模块语言包中查找，找不到则从全局语言包中查找
         *
         * @param label {string}    要翻译的label
         * @param category {string} 翻译的类型，默认“labels”
         * @param scope {scope}     语言包模块，默认为控制器名称
         * @type {Language.trans}
         */
        window.trans = this.trans = function (label, category, scope) {
            category = category || 'labels', scope = scope || defaultScope
            var res = store[lang].get(scope + '.' + category + '.' + label)
            return res || store[lang].get('Global.' + category + '.' + label, label)
        }

        /**
         * 翻译字段选项
         *
         * @param value {string|int} 选项值
         * @param label
         * @param scope
         * @type {Language.transOption}
         */
        window.trans_option = this.transOption = function (value, label, scope) {
            scope = scope || defaultScope
            var res = store[lang].get(scope + '.options.' + label + '.' + value)
            return res || store[lang].get('Global.options.' + label + '.' + value, value)
        }

        /**
         * 设置语言，默认语言为“en”
         *
         * @param type
         */
        this.type = function (type) {
            lang = type
            store[lang] || (store[lang] = new Store())
        }

        /**
         * 获取某一语言的语言包数据
         *
         * @param language
         * @returns {*}
         */
        this.all = function (language) {
            return store[language || lang].all()
        }

    }
    // --------------------------------------Language END-----------------------------------------------

    /**
     * -------------------------------------------------------------------------------------
     * Created by Jqh on 2017/6/27.
     */
    function Model(name, module, container) {
        var notify = container.ui().notify()

        var store = {
            /**
             * 表示当前模型是否在发起请求中
             *
             * @type {bool}
             */
            isRequsting: false,

            /**
             * 属性
             *
             * @type {object}
             */
            attrs: {},

            /**
             * 初始数据
             */
            initialData: {},

            /**
             * 项目模块名称
             *
             * @type {string}
             */
            module: 'Admin',

            /**
             * 模型名称
             *
             * @type {string}
             */
            name: '',

            api: '',

            /**
             * 请求超时时间，单位毫秒
             *
             * @type {int}
             */
            timeout: 10000,

            /**
             * 请求返回内容
             */
            responseContent: {

            },

            /**
             * 表单CSS选择器
             *
             * @type {string}
             */
            formSelector: '',

            /**
             * api前缀
             *
             * @type {string}
             */
            apiPrefix: '/api/',

            /**
             * 请求方法
             *
             * @var string
             */
            method: 'POST',

            /**
             * @type {Form}
             */
            formHandler: null,

            /**
             * 要发送到服务器的请求数据，如果此属性有值，则不会从表单获取数据
             *
             * @type {object|null}
             */
            data: null,

            /**
             * 回调函数
             */
            call: {
                /**
                 * 请求成功后触发
                 * 如果api返回json数据里存在“status”参数，则此函数会根据statusCode判断是否成功
                 * 如果api返回的json数据里不存在“status”参数，则会被判定为成功,此函数会被调用
                 *
                 * @param data
                 */
                success: function (data) {

                },

                /**
                 * 请求失败后触发
                 * 如果api返回json数据里存在“status”参数，则此函数会根据statusCode判断是否成功
                 * 如果api返回的json数据里不存在“status”参数，则会被判定为成功,此函数不会被调用
                 *
                 * @param data
                 */
                failed: function (data) {
                    if (typeof swal != 'undefined') swal.close() // 关闭提示窗
                    notify.remove()
                    notify.error(trans(data.msg, 'tip'))
                },

                /**
                 * ajax 错误回调函数
                 * 状态码不为200时触发
                 *
                 * @param req
                 * @param msg
                 * @param e
                 */
                error: function (req, msg, e) {
                    notify.remove()
                    notify.error(req.status + ' ' + trans(req.statusText) + ' ' + trans(req.responseText))
                    // store.call.error(req, msg, e)
                },

                /**
                 * 请求正常（状态码200），不论成功还是失败均会触发，且此事件不会和“success”或“error”事件冲突
                 */
                any: function () {

                }
            }
        }

        store.formHandler = container.form()
        store.name = name
        // 保存初始数据
        store.initialData = store.formHandler.get(get_form_selector())

        /**
         * 设置模型属性值
         *
         * @param k
         * @param v
         * @returns {Model}
         */
        this.set = function (k, v) {
            if (typeof k == 'object') {
                store.attrs = k
            } else {
                store.attrs[k] = v
            }
            return this
        }

        /**
         * 删除属性
         *
         * @param k
         * @returns {Model}
         */
        this.unset = function (k) {
            delete store.attrs[k]
            return this
        }

        // 设置请求超时时间，单位毫秒
        this.timeout = function (m) {
            store.timeout = m
            return this
        }

        // 重置属性
        this.reset = function () {
            store.attrs = {}
            return this
        }

        // 获取属性
        this.get = function(k, def) {
            return store.attrs[k] || def
        }

        // 获取所有属性
        this.all = function () {
            return store.attrs
        }

        /**
         * 设置请求发送给服务器的数据，如果设置了此值则不会从表单中获取数据
         *
         * @param data
         * @returns {Model}
         */
        this.data = function (data) {
            store.data = data
            return this
        }

        /**
         * 设置事件
         *
         * @param name 事件类型：success, failed, error, any
         * @param call 事件回调
         * @returns {Model}
         */
        this.on = function (name, call) {
            if (typeof call != 'function') throw new Error('Invalid arguments.')
            store.call[name] = call || store.call[name]
            return this
        }

        /**
         * 请求是否已结束，是返回true，否则返回false
         *
         * @returns {boolean}
         */
        this.requestEnded = function () {
            return (! store.isRequsting)
        }

        /**
         * 发起一个ajax请求
         *
         * @param api
         * @param method
         */
        this.request = function (api, method) {
            store.method = method || store.method
            store.api = api || store.api

            // 判断是否已经在发起请求中
            if (! this.requestEnded()) {
                return
            }

            // 标记请求开始
            store.isRequsting = true

            var data = util.getData()

            if (store.method.toLocaleUpperCase() == 'PUT') {
                data = JSON.stringify(data)
            }

            $.ajax({
                url: store.api,
                ifModified: false,
                type: store.method,
                cache: true,
                async: true,
                data: data,
                timeout: store.timeout,
                success: function(data) {
                    // 标记请求结束
                    store.isRequsting = false
                    if (typeof data != 'object' && data.indexOf('{') == 0) data = JSON.parse(data)
                    store.responseContent[store.method + store.api] = data
                    if (data.status) {
                        if (data.status == container.statusCode.success) {
                            store.call.success(data)
                        } else {
                            store.call.failed(data)
                        }
                    } else {
                        store.call.success(data)
                    }
                    store.call.any(data)
                },
                error: function (req, msg, e) {
                    // 标记请求结束
                    store.isRequsting = false
                    store.call.error(req, msg, e)
                }
            });
        }

        /**
         * 执行动作（发起一个ajax请求）
         *
         * @param action
         * @param method
         */
        this.touchAction = function (action, method) {
            return this.request(util.parseApi('action', {action: action}), method)
        }

        /**
         * 发起添加操作请求
         *
         */
        this.add = function () {
            return this.request(util.parseApi('add'), 'POST')
        }

        /**
         * 发起修改操作请求
         *
         */
        this.edit = function () {
            // 判断是否有修改过表单内容
            if (container.util().cmp(store.initialData, store.formHandler.get(get_form_selector())) === true) {
                notify.remove()
                return notify.info(trans('Nothing has been change.'))
            }

            return this.request(util.parseApi('edit'), 'PUT')
        }

        /**
         * 保存数据，根据是否存在id判断是新增操作还是修改操作
         */
        this.save = function () {
            var data = store.formHandler.get(get_form_selector())

            if (data.id) {
                return this.edit()
            }
            return this.add()
        }

        /**
         * 发起删除一行或多行数据请求
         *
         */
        this.delete = function () {
            return this.request(util.parseApi('delete'), 'DELETE')
        }

        /**
         * 获取列表数据
         *
         */
        this.fetchList = function () {
            return this.request(util.parseApi('list'), 'GET')
        }

        /**
         * 获取单行数据
         *
         */
        this.fetchRow = function () {
            return this.request(util.parseApi('detail'), 'GET')
        }

        /**
         * 获取表单数据
         */
        this.getFormData = function () {
            return store.formHandler.get(get_form_selector())
        }

        var self = this

        /**
         * 工具类
         *
         * @type {{getData: util.getData, parseApi: util.parseApi}}
         */
        var util = {
            /**
             * 获取要提交到服务器的数据
             *
             * @returns {Object|null}
             */
            getData: function () {
                var data = store.data
                if (data) {
                    store.data = null
                    return data
                }

                data = store.formHandler.get(get_form_selector())

                for (var i in data) {
                    self.set(i, data[i])
                }

                return self.all()
            },

            /**
             * 解析api
             *
             * @param type
             * @param options
             * @returns {string}
             */
            parseApi: function (type, options) {
                switch (type) {
                    case 'add':
                        return store.apiPrefix + store.name
                    case 'edit':
                        var id = self.get('id') || store.formHandler.get(get_form_selector()).id
                        return store.apiPrefix + store.name + '/view/' + id
                    case 'delete':
                        var id = self.get('id')
                        return store.apiPrefix + store.name + '/view/' + id
                    case 'list':
                        return store.apiPrefix + store.name + '/list'
                    case 'detail':
                        return store.apiPrefix + store.name + '/view/' + self.get('id')
                    case 'action':
                        return store.apiPrefix + store.name + '/' + options.action
                }
            },

        }

        /**
         * 获取表单选择器
         *
         * @returns {string}
         */
        function get_form_selector () {
            return store.formSelector || (store.formSelector = '.' + store.name + '-form')
        }
    }
    // --------------------------------------Model END-----------------------------------------------

    /**
     * -------------------------------------------------------------------------------------
     * 表单数据获取器
     *
     * @constructor
     */
    function Form() {
        var formEles = ['input', 'textarea', 'select']

        // 获取指定form中的所有的<input>对象
        function get_elements(selector) {
            var form = document.querySelector(selector)// || document.querySelector('form');
            if (! form) return []
            var elements = [], tagElements
            for (var i in formEles) {
                tagElements = form.getElementsByTagName(formEles[i]);
                for (var j = 0; j < tagElements.length; j++) {
                    elements.push(tagElements[j]);
                }
            }
            return elements;
        }

        //获取单个input中的【name,value】数组
        function input_selector(element) {
            if (element.checked) {
                return [element.name, element.value]
            }
            return [element.name, '']
        }

        function input(element) {
            switch (element.type.toLowerCase()) {
                case 'checkbox':
                case 'radio':
                    return input_selector(element);
                default:
                    return [element.name, element.value];
            }
            return false;
        }

        //组合URL
        function serialize_element(element) {
//            var method = element.tagName.toLowerCase();
            return input(element)
        }

        /**
         * 获取表单数据
         *
         * @param selector
         * @returns {{}}
         */
        this.get = function (selector) {
            var elements = get_elements(selector);
            var data = {};
            for (var i = 0; i < elements.length; i++) {
                var component = serialize_element(elements[i]);
                if (!component || typeof component[1] == 'undefined') continue
                if (component[0].indexOf('[') !== -1 && component[0].indexOf(']') !== -1 ) {
                    component[0] = component[0].replace('[]', '')
                    if (typeof data[component[0]] == 'undefined') {
                        data[component[0]] = []
                    }
                    data[component[0]].push(component[1])
                } else {
                    data[component[0]] = component[1]
                }
            }
            return data;
        }
    }
    // --------------------------------------Form END-----------------------------------------------

    /**
     * -------------------------------------------------------------------------------------
     * 数据管理
     *
     * @param data
     * @constructor
     */
    function Store(data)
    {
        data = data || {}

        /**
         * 获取所有已存入的数据
         *
         * @returns {{}}
         */
        this.all = function () {
            return data
        }

        /**
         * 把已存在数据转化成json返回
         *
         * @returns {json}
         */
        this.toJson = function () {
            return JSON.stringify(data)
        }

        /**
         * 获取数据，支持获取多维数值：“list.rows.name”
         *
         * @param $key
         * @param $default
         * @returns {*}
         */
        this.get = function ($key, $default) {
            if (! $key) {
                return data
            }
            $default = $default || null
            var $lastItem = data, keys = $key.split('.')
            for (var i = 0; i < keys.length; i ++) {
                if (typeof $lastItem[keys[i]] != 'undefined') {
                    $lastItem = $lastItem[keys[i]]
                } else {
                    return $default
                }
            }
            return $lastItem;
        }

        /**
         * 保存值
         *
         * @param key
         * @param val
         */
        this.set = function (key, val) {
            if (typeof key == 'object') {
                data = key
            } else {
                data[key] = val
            }
        }

        /**
         * 保存一个值到一个已存在对象
         *
         * @param key
         * @param name
         * @param val
         */
        this.add = function (key, name, val) {
            data[key] = data[key] || {}

            data[key][name] = val
        }

        /**
         * push一个值到一个已存在数组
         *
         * @param key
         * @param val
         */
        this.push = function (key, val) {
            data[key] = data[key] || []

            data[key].push(val)
        }

        /**
         * push一个值到一个已存在的第二维数组上
         *
         * @param key
         * @param name
         * @param val
         */
        this.pushSub = function (key, name, val) {
            data[key] = data[key] || {}

            data[key][name] = data[key][name] || []

            data[key][name].push(val)
        }
    }
    // --------------------------------------Store END-----------------------------------------------
    
    return new Container(options)
}
