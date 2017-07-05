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
        var actions = []

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

        // 配置文件管理
        this.config = new Store(options.config || {})

        // 缓存管理
        this.cache = options.cache

        // 存储仓库
        this.store = new Store()

        // 登陆用户信息管理
        this.user = new Store(options.user || {})

        // 语言包管理
        this.language = new Language(this, this.cache, this.config)

        // 视图管理
        this.view = new View(this, options.tpls || {})
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
            this.store.set(name, val)
            return this
        },

        /**
         * 获取容器缓存的数据
         *
         * @param name
         * @param {*}
         */
        get: function (name, $def) {
            return this.store.get(name, $def)
        },

        /**
         * 创建一个模型
         *
         * @param name    模型名称
         * @param module  项目模块名称
         * @returns {Model}
         */
        createModel: function (name, module) {
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
         * 显示表单错误
         *
         * @param name 表单name属性
         * @param e    表单dom元素
         * @param msg  错误信息
         */
        formValidatorDisplayErrorMsg: function (name, e, msg) {
            msg = trans(msg)
            e.addClass('parsley-error')
            e.parent().append('<ul class="parsley-errors-list filled validator-error-' + name + '" id="parsley-id-4"><li class="parsley-required">' + msg + '</li></ul>')
        },

        /**
         * 移除表单验证错误
         *
         * @param $e   表单dom元素
         * @param name 表单name属性
         */
        formValidatorRemoveError: function ($e, name) {
            $e.removeClass('parsley-error')
            $('.validator-error-' + name).remove()
        },

        /**
         * 注册自定义表单验证规则
         *
         * @param validator
         */
        registerValidatorRules: function (validator) {
            // validator.registerCallback('length_between', function(value, param, field) {
            //     console.log(7890, value.length, param)
            //     return false
            // }).setMessage('length_between', 'This value length is invalid. It should be between 5 and 10 characters long.')

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
        formValidator: function (options, call, selector) {
            selector = selector || 'form'

            var self = this

            $(selector).submit(function () {
                return false;
            })

            var $form = document.querySelector(selector)
            var v = new FormValidator($form, options, function (errors, event) {
                if (errors.length < 1 && event.type == 'submit') {
                    // 验证成功后回调
                    typeof call != 'function' || call(event)
                }
            }, validateCall);

            // 注册自定义验证规则
            this.registerValidatorRules(v)

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
            function validateCall(field, errorObject) {
                var $e = $(field.element)
                // 移除表单错误
                self.formValidatorRemoveError($e, field.name)
                if (errorObject) {
                    self.formValidatorDisplayErrorMsg(field.name, $e, errorObject.message)
                }
            }

            return v
        },

        /**
         * 环境管理
         */
        env: {
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
        },

        /**
         * ui 组件
         */
        ui: {
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
            }
        }
    }

    // 视图管理
    function View(container, tpls)
    {
        var data = new Store()
        data.set('tpls', tpls)

        this.getTpl = function (name) {
            return data.get('tpls.' + name)
        }
    }

    /**
     * 语言包管理
     *
     * @param container {Container}
     * @param cache     {Cache}
     * @param config    {Store}
     * @constructor
     */
    function Language(container, cache, config)
    {
        var store = {}, cache = cache, lang = config.get('language'), defaultScope = container.controllerName(), self = this
        var cacheKey = 'language_' + lang, useCache = config.get('use-cache'), expireTime = config.get('lang-package-expire')

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
            var cachePackage = {}, i
            for (var lang in packages) {
                cachePackage = cache.get(cacheKey, {})
                for (i in packages[lang]) {
                    cachePackage[i] = packages[lang][i]
                }
                cache.set(cacheKey, cachePackage)
                cache.expire(cacheKey, expireTime)
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

    /**
     * Created by Jqh on 2017/6/27.
     */
    function Model(name, module, container) {
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
                    container.ui.notify().error(data.msg)
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
                    container.ui.notify().remove()
                    container.ui.notify().error(req.status + ' ' + trans(req.statusText) + ' ' + trans(req.responseText))
                    store.call.error(req, msg, e)
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
            // 标记请求开始
            store.isRequsting = true
            $.ajax({
                url: store.api,
                ifModified: false,
                type: store.method,
                cache: true,
                async: true,
                data: util.getData(),
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
            return this.request(util.parseApi('edit'), 'PUT')
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

        var self = this

        /**
         * 工具类
         *
         * @type {{getData: util.getData, parseApi: util.parseApi, getFormSelector: util.getFormSelector}}
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

                data = store.formHandler.get(this.getFormSelector())

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
                        return store.apiPrefix + store.name + '/' + self.get('id')
                    case 'delete':
                        var id = self.get('id')
                        if (id) {
                            return store.apiPrefix + store.name + '/' + id
                        }
                        return store.apiPrefix + store.name
                    case 'list':
                        return store.apiPrefix + store.name + '/list'
                    case 'detail':
                        return store.apiPrefix + store.name + '/view/' + self.get('id')
                    case 'action':
                        return store.apiPrefix + store.name + '/' + options.action
                }
            },

            /**
             * 获取表单选择器
             *
             * @returns {string}
             */
            getFormSelector: function () {
                return store.formSelector || (store.formSelector = '.' + store.name + '-form')
            },
        }
    }

    /**
     * 表单数据获取器
     *
     * @constructor
     */
    function Form() {
        var formEles = ['input', 'textarea', 'select']

        // 获取指定form中的所有的<input>对象
        function getElements(selector) {
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
        function inputSelector(element) {
            if (element.checked) return [element.name, element.value];
        }

        function input(element) {
            switch (element.type.toLowerCase()) {
                case 'checkbox':
                case 'radio':
                    return inputSelector(element);
                default:
                    return [element.name, element.value];
            }
            return false;
        }

        //组合URL
        function serializeElement(element) {
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
            var elements = getElements(selector);
            var data = {};
            for (var i = 0; i < elements.length; i++) {
                var component = serializeElement(elements[i]);
                if (!component || typeof component[1] == 'undefined') continue
                data[component[0]] = component[1]
            }
            return data;
        }
    }

    /**
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
    
    return new Container(options)
}

