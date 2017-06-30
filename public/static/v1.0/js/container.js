/**
 * Created by Jqh on 2017/6/28.
 */
window.Lxh = (function () {
    function Container(options) {
        var actions = []
        this.addAction = function (call) {
            actions.push(call)
            return this
        }
        this.call = function () {
            for (var i in actions) {
                actions[i](this)
            }
        }
        this.cache = new Cache()
        this.store = new Store()
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
            return new Model(name, module)
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
        user: function () {

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
                function show (selector, timeout) {
                    var $portlet = $(selector).closest(selector);
                    // This is just a simulation, nothing is going to be reloaded
                    $portlet.append('<div class="panel-disabled"><div class="loader-1"></div></div>')
                    if (! timeout) return
                    setTimeout(function () {
                        close(selector)
                    }, timeout)
                }
                function close (selector) {
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

    /**
     * Created by Jqh on 2017/6/27.
     */
    function Model(name, module) {
        var store = {
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
                    Lxh.ui.notify().error(data.msg)
                },
                // ajax 错误回调函数
                error: function (req, msg, e) {
                    Lxh.ui.notify().remove()
                    Lxh.ui.notify().error(req.status + ' ' + req.statusText + ' ' + req.responseText)
                    store.error(req, msg, e)
                },
                any: function () {

                }
            }
        }
        store.formHandler = Lxh.form()
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

        this.request = function (api, method) {
            store.method = method || store.method
            store.api = api || store.api
            $.ajax({
                url: store.api,
                ifModified: false,
                type: store.method,
                cache: true,
                async: true,
                data: util.getData(),
                timeout: store.timeout,
                success: function(data) {
                    if (typeof data != 'object' && data.indexOf('{') == 0) data = JSON.parse(data)
                    store.responseContent[store.method + store.api] = data
                    if (data.status) {
                        if (data.status == Lxh.statusCode.success) {
                            store.call.success(data)
                        } else {
                            store.call.failed(data)
                        }
                    } else {
                        store.call.success(data)
                    }
                    store.call.any(data)
                },
                error: store.call.error
            });
        }

        // 执行动作
        this.touchAction = function (action, method) {
            return this.request(util.getApi('action', {action: action}), method)
        }

        // 添加
        this.add = function () {
            return this.request(util.getApi('add'), 'POST')
        }

        // 修改
        this.edit = function () {
            return this.request(util.getApi('edit'), 'PUT')
        }

        // 删除一行或多行数据
        this.delete = function (data) {
            return this.request(util.getApi('delete'), 'DELETE')
        }

        // 获取列表
        this.fetchList = function () {
            return this.request(util.getApi('list'), 'GET')
        }

        // 获取单行数据
        this.fetchRow = function () {
            return this.request(util.getApi('detail'), 'GET')
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

                for (var i in store.attrs) {
                    data[i] = store.attrs
                }
                self.set(data)
                return data
            },
            getApi: function (type, options) {
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
                        return store.apiPrefix + store.name + '/action/' + options.action
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
            data[key] = val
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

    function Cache() {
        this.storage = window.localStorage || {}
        this.prefix = {
            general: "$lxh_",
            timeout: "@lxh_"
        }

        // 设置缓存，timeout为秒
        this.set = function (key, val, timeout) {
            if (val instanceof Object) {
                val = JSON.stringify(val)
            }
            this.storage.setItem(this.prefix.general + key, val)
            if (timeout) {
                this.expire(key, timeout)
            }
        }
        // 获取缓存
        this.get = function (key, def) {
            //检测是否过期
            if (this.clearTimeout(key)) return null
            var val = this.storage.getItem(this.prefix.general + key)

            if (val) {
                if (val.indexOf("{") === 0 || val.indexOf("[") === 0) {
                    return JSON.parse(val)
                }
                return val
            }
            return (def || null)
        }

        // 清除所有过期的key
        this.clearPastDueKey = function () {
            for (var key in this.storage) {
                if (key.indexOf(this.prefix.timeout) == -1) {
                    continue
                }
                this.clearTimeout(key.replace(this.prefix.timeout, ""))
            }
        }

        this.clearTimeout = function (key) {
            var d, timeoutKey = this.prefix.timeout + key, timeout = this.storage.getItem(timeoutKey)
            if (timeout) {
                d = new Date().getTime()
                if (timeout < d) {//已过期
                    delete this.storage[this.prefix.general + key]
                    delete this.storage[timeoutKey]
                    return true
                }
            }
            return false
        }

        //设置缓存时间，tiemeout毫秒后过期
        this.expire = function (key, timeout) {
            var d = new Date().getTime() + (parseInt(timeout))
            this.storage.setItem(this.prefix.timeout + key, d)
        }
        // 具体某一时间点过期
        this.expireAt = function (key, timeout) {
            this.storage.setItem(this.prefix.timeout + key, timeout)
        }

        this.clearPastDueKey()
    }
    
    return new Container()
})()

