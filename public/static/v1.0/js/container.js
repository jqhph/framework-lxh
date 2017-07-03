/**
 * Created by Jqh on 2017/6/28.
 */
window.Lxh = function (options) {
    function Container(options) {
        var actions = []
        this.controllerName = function () {
            return options.controller
        }
        this.moduleName = function () {
            return options.module
        }
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
        statusCode: {
            success: 10001,
            failed: 10002,
            invalid_arguments: 10003,
        },
        set: function (name, val) {
            this.store.set(name, val)
            return this
        },
        get: function (name, $def) {
            return this.store.get(name, $def)
        },
        createModel: function (name, module) {
            return new Model(name, module, this)
        },
        createStore: function (data) {
            return new Store(data)
        },
        form: function () {
            return this.get('form') || this.set('form', new Form()).get('form')
        },
        env: {
            val: 'dev',
            set: function (type) {
                this.val = type
            },
            isProd: function () {
                return this.val == 'prod'
            },
            isDev: function () {
                return this.val == 'dev'
            },
            isTest: function () {
                return this.val == 'test'
            }
        },
        // ui 组件
        ui: {
            // loading
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
            // notify
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

    // 语言包管理
    function Language(container, cache, config)
    {
        var store = {}, cache = cache, lang = config.get('language'), defaultScope = container.controllerName(), self = this
        var cacheKey = 'language_' + lang, useCache = config.get('use-cache'), expireTime = config.get('lang-package-expire')

        store[lang] = new Store()

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

        // 缓存语言包
        this.save = function (packages) {
            if (! useCache) {
                return
            }
            var cachePackage = {}, i
            for (var lang in packages) {
                cachePackage = cache.get(cacheKey)
                for (i in packages[lang]) {
                    cachePackage[i] = packages[lang][i]
                }
                cache.set(cacheKey, cachePackage)
                cache.expire(cacheKey, expireTime)
            }
        }


        /**
         * 获取语言包数据
         *
         * @param array scopes 语言包模块数组
         * @param function call 获取成功后回调函数
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

        // 翻译
        window.trans = this.trans = function (label, category, scope) {
            category = category || 'labels', scope = scope || defaultScope
            var res = store[lang].get(scope + '.' + category + '.' + label)
            return res || store[lang].get('Global.' + category + '.' + label, label)
        }

        // 翻译字段选项
        window.trans_option = this.transOption = function (value, label, scope) {
            scope = scope || defaultScope
            var res = store[lang].get(scope + '.options.' + label + '.' + value)
            return res || store[lang].get('Global.options.' + label + '.' + value, value)
        }

        // 设置预言包类型
        this.type = function (type) {
            lang = type
            store[lang] || (store[lang] = new Store())
        }

        // 获取所有语言包数据
        this.all = function (language) {
            return store[language || lang].all()
        }

    }

    /**
     * Created by Jqh on 2017/6/27.
     */
    function Model(name, module, container) {
        var store = {
            isRequsting: false,
            attrs: {},
            module: 'Admin',
            name: '',
            api: '',
            // 超时时间
            timeout: 10000,
            // 请求返回内容
            responseContent: {

            },
            formSelector: '',
            apiPrefix: '/api/',
            method: 'POST',
            formHandler: null,
            data: null,
            call: {
                success: function (data) {

                },
                failed: function (data) {
                    container.ui.notify().error(data.msg)
                },
                // ajax 错误回调函数
                error: function (req, msg, e) {
                    container.ui.notify().remove()
                    container.ui.notify().error(req.status + ' ' + trans(req.statusText) + ' ' + trans(req.responseText))
                    store.error(req, msg, e)
                },
                any: function () {

                }
            }
        }

        store.formHandler = container.form()
        store.name = name

        this.set = function (k, v) {
            if (typeof k == 'object') {
                store.attrs = k
            } else {
                store.attrs[k] = v
            }
            return this
        }

        // 删除属性
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

        this.data = function (data) {
            store.data = data
            return this
        }

        // 设置事件
        this.on = function (name, call) {
            if (typeof call != 'function') throw new Error('Invalid arguments.')
            store.call[name] = call || store.call[name]
            return this
        }

        // 请求是否已结束，是返回true，否则返回false
        this.requestEnded = function () {
            return (! store.isRequsting)
        }

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

        // 执行动作
        this.touchAction = function (action, method) {
            return this.request(util.parseApi('action', {action: action}), method)
        }

        // 添加
        this.add = function () {
            return this.request(util.parseApi('add'), 'POST')
        }

        // 修改
        this.edit = function () {
            return this.request(util.parseApi('edit'), 'PUT')
        }

        // 删除一行或多行数据
        this.delete = function (data) {
            return this.request(util.parseApi('delete'), 'DELETE')
        }

        // 获取列表
        this.fetchList = function () {
            return this.request(util.parseApi('list'), 'GET')
        }

        // 获取单行数据
        this.fetchRow = function () {
            return this.request(util.parseApi('detail'), 'GET')
        }

        var self = this
        var util = {
            // 获取要提交的数据
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
            // 获取表单选择器
            getFormSelector: function () {
                return store.formSelector || (store.formSelector = '.' + store.name + '-form')
            },
        }
    }

    function Form() {
        var formEles = ['input', 'textarea', 'select']

        // 获取指定form中的所有的<input>对象
        function getElements(selector) {
            var form = document.querySelector(selector);
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

        //调用方法
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

    // 数据存储
    function Store(data)
    {
        data = data || {}
        this.all = function () {
            return data
        }
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
        this.set = function (key, val) {
            if (typeof key == 'object') {
                data = key
            } else {
                data[key] = val
            }
        }
        this.add = function (key, name, val) {
            data[key] = data[key] || {}

            data[key][name] = val
        }
        this.push = function (key, val) {
            data[key] = data[key] || []

            data[key].push(val)
        }
        this.pushSub = function (key, name, val) {
            data[key] = data[key] || {}

            data[key][name] = data[key][name] || []

            data[key][name].push(val)
        }
    }
    
    return new Container(options)
}

