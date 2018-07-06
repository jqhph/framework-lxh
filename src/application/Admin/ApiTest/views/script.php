<script>
__then__(function () {
    var $doms = {
            addPost: $('.add-post-key-val'),
            addQuery: $('.add-query-key-val'),
            addHeader: $('.add-header-key-val'),
            sbt: $('button.submit'),
            auto: $('.auto-input'),
            form: $('.request-form'),
            url: $('input[name="req-url"]'),
            method: $('select[name="req-method"]'),
            postBox: $('.req-post-box'),
            queryBox: $('.req-query-box'),
            headerBox: $('.req-header-box'),
            rtdef: $('.reset-def') // 重置缓存参数
        },
        inputTpl = $('#kv-input-tpl').html(),
        selectors = {
            rm: '.remove-key-val',
            kv: '.k-v-input',
            k: '.ikey',
            v: '.ival'
        },
        evt = 'add-default-param', // 触发添加默认值事件
        allEvt = 'add-all-param', // 触发添加所有参数到表单事件
        defParamDoms = {},
        current = LXHSTORE.IFRAME.current(),
        storage = new RequestDataStorage();

    // 添加缓存事件
    $(document).on(evt, function () {
        setup();
    });

    // 填充所有解析数据到表单
    $(document).on(allEvt, function (e, httpRequestParser) {
        console.log(666,e,d)
    });

    // 提交表单
    $doms.sbt.click(function () {
        var form = get_form_data();

        console.log('submit', form);
    });

    // 新增POST key-value输入框
    $doms.addPost.click(function () {
        add_input($doms.postBox);
    });

    $doms.addQuery.click(function () {
        add_input($doms.queryBox);
    });

    $doms.addHeader.click(function () {
        add_input($doms.headerBox);
    });

    // 字符串自动填充
    $doms.auto.click(function () {
        var auto = new AutoInput();
        auto.build();


    });


    // 重置默认参数
    $doms.rtdef.click(function () {
        setup();
    });

    /**
     * 设置默认提交参数到key value输入框
     */
    function set_default_params(def, dom, cls) {
        cls = cls ? cls + '-default-box' : '';

        var kname, vname, i, k, v, arr = [];

        for (k in def) {
            arr.unshift({k: k, v: def[k]});
        }
        // 先移除
        $('.'+cls).remove();
        for (i in arr) {
            k = arr[i].k;
            v = arr[i].v;
            kname = k+'k';
            vname = k+'v';

            // 渲染视图
            add_input(dom, k, v, true, 'color:#0072C6;font-weight:bold', cls);
            // 保存dom对象
            defParamDoms[k] = {
                k: $('input[name="'+kname+'"]'),
                v: $('input[name="'+vname+'"]')
            };
        }

        LXHSTORE.IFRAME.height(current);
    }

    /**
     * 获取表单数据
     */
    function get_form_data() {
        var form = {},
            $keys = $(selectors.k),
            $vals = $(selectors.v),
            vd;

        form.url = $doms.url.val();
        form.method = $doms.method.val();
        form.header = {};
        form.params = {};

        // 获取请求参数
        $keys.each(function (i, kd) {
            vd = $vals[i];
            form.params[$(kd).val()] = $(vd).val();
        });

        // 获取header头信息

        return form;
    }

    /**
     * 新增一组key-value输入框
     */
    function add_input(
        $box,
        k,
        v,
        append,
        color,
        cls
    ) {
        k = k || "";
        v = v || "";
        color = color || "";
        var view = new Blade(inputTpl, {key: k, value: v, style: color, class: cls}), rm;

        append ? $box.append(view.fetch()) :$box.prepend(view.fetch());
        rm = $(selectors.rm);
        rm.off('click');
        rm.click(function (e) {
            var parent = $(e.currentTarget).parents(selectors.kv);
            k = parent.find(selectors.k).val();
            delete defParamDoms[k];
            parent.remove();
        });

        LXHSTORE.IFRAME.height(current);
    }

    /**
     * 初始化
     */
    function setup() {
        set_default_params(storage.getHeader(), $doms.headerBox, 'header');
        set_default_params(storage.getQuery(), $doms.queryBox, 'query');
        set_default_params(storage.getPost(), $doms.postBox, 'post');
    }

    setup();
});
</script>

<script>
    (function (w) {
        var cache = LXHSTORE.cache,
            keys = {
                post: 'api-default-params',
                query: 'api-default-query',
                header: 'api-default-header'
            };

        /**
         *
         * @param {RequestDataBuffer} buffer
         * @constructor
         */
        function RequestDataStorage(buffer) {
            this.buffer = buffer;
        }

        RequestDataStorage.prototype = {
            /**
             *
             * @param {RequestDataBuffer} buffer
             */
            commit: function (buffer) {
                buffer = buffer || this.buffer;
                if (!buffer) return false;
                this.setHeader(buffer.getHeader());
                this.setQuery(buffer.getQuery());
                this.setPost(buffer.getPost());
            },

            set: function (cate, data) {
                if (typeof keys[cate] == 'undefined') {
                    throw new Error('不支持的缓存类型：'+cate);
                }
                cache.set(keys[cate], data);
            },

            get: function (cate, key, def) {
                if (typeof keys[cate] == 'undefined') {
                    throw new Error('不支持的缓存类型：'+cate);
                }
                var d = cache.get(keys[cate]);
                if (!key) {
                    return d || {};
                }
                return d[key] || (def || null);
            },

            add: function (cate, key, val) {
                if (typeof keys[cate] == 'undefined') {
                    throw new Error('不支持的缓存类型：'+cate);
                }
                var d = this.get(cate);
                d[key] = val;
                return this.set(cate, d)
            },

            delete: function (cate, key) {
                if (typeof keys[cate] == 'undefined') {
                    throw new Error('不支持的缓存类型：'+cate);
                }
                if (key) {
                    return this.deleteKey(cate, key);
                }
                return cache.delete(keys[cate])
            },

            deleteKey: function (cate, key) {
                if (typeof keys[cate] == 'undefined') {
                    throw new Error('不支持的缓存类型：'+cate);
                }
                var d = this.get(cate);
                delete d[key];
                return cache.set(cate, d)
            },

            setPost: function (data) {
                cache.set(keys.post, data)
            },

            getPost: function (key, def) {
                return key ? this.get('post', key, def) : (this.get('post') || {});
            },

            deletePost: function (key) {
                return this.delete('post', key)
            },

            addPost: function (key, val) {
                return this.add('post', key, val);
            },

            setQuery: function (data) {
                cache.set(keys.query, data)
            },

            getQuery: function (key, def) {
                return key ? this.get('query', key, def) : (this.get('query') || {});
            },

            deleteQuery: function (key) {
                return this.delete('query', key)
            },

            addQuery: function (key, val) {
                return this.add('query', key, val);
            },

            setHeader: function (data) {
                cache.set(keys.header, data)
            },

            getHeader: function (key, def) {
                return key ? this.get('header', key, def) : (this.get('header') || {});
            },

            deleteHeader: function (key) {
                return this.delete('header', key)
            },

            addHeader: function (key, val) {
                return this.add('header', key, val);
            }
        };

        /**
         * 缓冲数据
         *
         * @constructor
         */
        function RequestDataBuffer() {
            this.buffer = {
                post: {}, // 缓存默认的POST数据
                query: {}, // 缓存默认的query数据
                header: {} // 缓存默认的header数据
            };
        }

        RequestDataBuffer.prototype = {
            all: function () {
                return this.buffer
            },

            set: function (cate, data) {
                this.buffer[cate] = data;
            },

            get: function (cate, key, def) {
                if (typeof this.buffer[cate] == 'undefined') {
                    throw new Error('不支持的缓存类型：'+cate);
                }
                return key ? (this.buffer[cate][key] || (def || null)) : (this.buffer[cate] || {});
            },

            add: function (cate, key, val) {
                var d = this.get(cate);
                d[key] = val;
                return this.set(cate, d)
            },

            delete: function (cate, key) {
                if (key) {
                    return this.deleteKey(cate, key);
                }
                this.buffer[cate] = {}
            },

            deleteKey: function (cate, key) {
                var d = this.get(cate);
                delete d[key];
                return this.set(cate, d)
            },

            setPost: function (data) {
                return this.set('post', data)
            },

            getPost: function (key, def) {
                return this.get('post', key, def)
            },

            addPost: function (key, val) {
                return this.add('post', key, val)
            },

            deletePost: function (key) {
                return this.delete('post', key);
            },

            setQuery: function (data) {
                return this.set('query', data)
            },

            getQuery: function (key, def) {
                return this.get('query', key, def)
            },

            addQuery: function (key, val) {
                return this.add('query', key, val)
            },

            deleteQuery: function (key) {
                return this.delete('query', key);
            },


            setHeader: function (data) {
                return this.set('header', data)
            },

            getHeader: function (key, def) {
                return this.get('header', key, def)
            },

            addHeader: function (key, val) {
                return this.add('header', key, val)
            },

            deleteHeader: function (key) {
                return this.delete('header', key);
            }
        };

        w.RequestDataBuffer = RequestDataBuffer;
        w.RequestDataStorage = RequestDataStorage;
    })(window);
</script>

<script>
(function (w) {
    var selectors = {
        tpl: '#auto-input-tpl',
        tbtpl: '#parse-table-tpl',
        parse: '.parse-raw',
        raw: 'textarea[name="req-raw"]',
        resc: '.parse-result-container',
        parsedHeader: '.parsed-header',
        parsedQuery: '.parsed-query',
        parsedPost: '.parsed-post'
    },
        pd = w.parent.document,
        cache = LXHSTORE.cache,
        oldKey = 'http-old-raw',
        evt = 'add-default-param', // 触发添加默认值事件
        allEvt = 'add-all-param', // 触发添加所有参数到表单事件
        buffer = new RequestDataBuffer(),
        storage = new RequestDataStorage(buffer); // 请求数据缓存对象

    function AutoInput() {
        this.currentParser = null;
    }

    /**
     * 构建弹窗表单模板
     *
     * @return string
     */
    AutoInput.prototype.build = function () {
        var self = this;
        var index = layer.open({
            title: '解析HTTP请求数据',
            type: 1,
            content: $(selectors.tpl).html(),
            area: ['70%', '80%'],
            maxmin: true,
            shadeClose: true,
            shade: false,
            btn: ['解析', '添加选中数据到请求表单并缓存', '添加全部数据到请求表单不缓存'],
            btn1: self.parseRaw.bind(self),
            btn2: function () {
                if (!self.currentParser) {
                    self.parseRaw();
                }

                storage.commit();
                if (self.currentParser.raw()) {
                    notify_success('操作成功！');
                } else {
                    notify_success('清除缓存成功！');
                }

                // 触发添加默认值事件
                $(document).trigger(evt);
                console.log('storage', buffer.all());
                return false
            },
            btn3: function () {
                if (!self.currentParser) {
                    self.parseRaw();
                }

                if (!self.currentParser.raw()) {
                    notify_error('无数据！');
                    return false;
                }

                $(document).trigger(allEvt, self.currentParser);
                notify_success('操作成功！');
            },
            success: self.setOldRaw.bind(self)
        });
    };

    AutoInput.prototype.parseRaw = function () {
        var raw = $(selectors.raw, pd).val(),
            parser = new HttpRequestParser(raw);
        parser.parse();
        if (raw) {
            this.saveRaw(raw);
            this.buildTables(parser);
        } else {
            $(selectors.resc, pd).html(" ");
        }

        this.currentParser = parser;
    };

    AutoInput.prototype.setOldRaw = function () {
        var oldData = cache.get(oldKey);
        if (oldData) {
            $(selectors.raw, pd).val(oldData);
            this.parseRaw();
        }
    };

    AutoInput.prototype.saveRaw = function (raw) {
        if (raw) {
            cache.set(oldKey, raw);
        }
    };

    /**
     *
     * @param {HttpRequestParser} parser
     */
    AutoInput.prototype.buildTables = function (parser) {
        var self = this, $container = $(selectors.resc, pd), tpl = $(selectors.tbtpl).html();
        // 清空
        $container.html(" ");

        // 显示URL
        if (parser.getUrl()) {
            var list = {}, m = parser.getVersion() + ' &nbsp; ' + parser.getMethod();
            list[m] = parser.getUrl();
            var first = new Blade(tpl, {
                title: "RESULT",
                list: list,
                colspan: 2
            });
            $container.append(first.fetch());
        }

        (function () {
            var list = parser.getQuery(), checkeds = {}, i, v;

            for (i in list) {
                if (v = storage.getQuery(i)) {
                    checkeds[i] = 1;
                    buffer.addQuery(i, v);
                }
            }
            // 显示query信息
            var query = new Blade(tpl, {
                title: "QUERY",
                list: list,
                class: 'parsed-query',
                colspan: 2,
                checkeds: checkeds,
                checkbox: 1,
            });
            $container.append(query.fetch());
        })();

        // 显示HEADER头信息
        (function () {
            var list = parser.getHeader(), checkeds = {}, i, v;

            for (i in list) {
                if (v = storage.getHeader(i)) {
                    checkeds[i] = 1;
                    buffer.addHeader(i, v);
                }
            }
            var header = new Blade(tpl, {
                title: "HEADER",
                list: list,
                class: 'parsed-header',
                checkbox: 1,
                colspan: 3,
                checkeds: checkeds
            });
            $container.append(header.fetch())
        })();

        (function () {
            var list = parser.getData(), checkeds = {}, i, v;

            for (i in list) {
                if (v = storage.getPost(i)) {
                    checkeds[i] = 1;
                    buffer.addPost(i, v);
                }
            }
            // 显示HEADER头信息
            var post = new Blade(tpl, {
                title: "DATA",
                list: list,
                class: 'parsed-post',
                checkbox: 1,
                colspan: 3,
                checkeds: checkeds
            });
            $container.append(post.fetch());
        })();

        // 绑定事件
        this.bindTableEvents();

    };

    AutoInput.prototype.bindTableEvents = function () {
        var self = this;
        // 选中header头信息
        $(selectors.parsedHeader, pd).click(function (e) {
            console.log('parsed header');
            set_buffer(e, 'header');
        });

        // 选中query数据
        $(selectors.parsedQuery, pd).click(function (e) {
            console.log('parsed query');
            set_buffer(e, 'query');
        });

        // 选中POST数据
        $(selectors.parsedPost, pd).click(function (e) {
            console.log('parsed post');
            set_buffer(e, 'post');
        });

        function set_buffer(e, cate) {
            var tds = $(e.currentTarget).find('td'),
                k = $(tds[1]).text(),
                v = $(tds[2]).text();
            if (checked_row(e)) {
                buffer.add(cate, k, v);
            } else {
                buffer.delete(cate, k)
            }
        }

    };

    /**
     *  选中返回true，否则返回false
     *
     * @param e
     * @returns {boolean}
     */
    function checked_row(e) {
        var _t = $(e.currentTarget);
        var box = _t.find('input[type="checkbox"]');
        if (typeof e.toElement == undefined || e.toElement.outerHTML.indexOf('checkbox') == -1) {
            box.prop("checked") ? box.prop("checked", false) : box.prop("checked", true);
        }

        // 重新判断一次
        return box.prop("checked") ? true : false;
    }

    w.AutoInput = AutoInput;
})(window);
</script>

<script>
(function (w) {
    var wsp = "\n\n", osp = "\r\n";

    function HttpRequestParser(content) {
        this.content      = content;
        this.result       = {header: {}, data: {}, query: {}, url: '', method: '', version: ''};
        this.headerEctype = {};
        this.header       = '';
        this.data         = '';
        this.sp           = osp; // 协议分隔符

        if (!content) {
            return;
        }
        if (content.indexOf(osp) == -1) {
            this.sp = wsp;
        }
        content = content.split(this.sp);

        // 非请求头信息
        if (content[0].indexOf('Host:') == -1) {
            this.data = content.join(this.sp);
        } else {
            this.header = content[0].replace(/[\r]*/, "").split("\n");
            if (content.length > 1) {
                content.shift();
                this.data = content.join(this.sp);
            }
        }

    }

    HttpRequestParser.prototype.raw = function () {
        return this.content;
    };

    /**
     * 判断请求数据是否是webform形式提交
     *
     * @returns {boolean}
     */
    HttpRequestParser.prototype.isFormData = function () {
        if (!this.data) {
            return false;
        }
        if (get_form_boundary(this)) {
            return true;
        }
        if (
            this.data.indexOf("Content-Disposition: form-data;") != -1 &&
            this.data.indexOf("\n") != -1
        ) {
            return true;
        }
        return false;
    };

    /**
     * 解析http请求信息
     */
    HttpRequestParser.prototype.parse = function () {
        if (!this.header && !this.data) {
            return false;
        }

        // 解析HEADER头信息
        if (this.header) {
            parse_header_first_row(this);
            parse_header(this);
        }

        // 解析POST请求数据
        if (this.isFormData()) {
            this.result.data = parse_form_data(this.data, get_form_boundary(this));
        } else {
            this.result.data = parse_query_data(this.data);
        }

        if (this.result.url) {
            this.result.query = parse_query_data(this.result.url);
        }

        console.log('http request data', this.result);

        return this.result;
    };

    HttpRequestParser.prototype.getHeader = function (key, def) {
        if (!key) {
            return this.result.header;
        }
        key = key.toLocaleUpperCase();
        return this.headerEctype[key] || (def || null);
    };

    HttpRequestParser.prototype.getUrl = function () {
        return this.result.url;
    };

    HttpRequestParser.prototype.getData = function () {
        return this.result.data;
    };

    HttpRequestParser.prototype.getQuery = function () {
        return this.result.query;
    };

    HttpRequestParser.prototype.getMethod = function () {
        return this.result.method;
    };

    HttpRequestParser.prototype.getVersion = function () {
        return this.result.version;
    };

    function get_form_boundary(_t) {
        var contentType = _t.getHeader('Content-Type');
        if (contentType) {
            contentType = contentType.split(';');
            if (contentType[0].toLocaleLowerCase() == 'multipart/form-data' && typeof contentType[0] != 'undefined') {
                if (contentType[0].indexOf('boundary=') != -1) {
                    return contentType[0].replace("boundary=", "");
                }
            }
        }
        return "";
    }

    /**
     * 解析header头
     */
    function parse_header(_t) {
        if (!_t.header) return false;

        var i, v, sp = ':';
        for (i in _t.header) {
            v = _t.header[i].split(sp);
            v[0] = trim(v[0]);
            _t.headerEctype[v[0].toLocaleUpperCase()] = _t.result.header[v[0]] = typeof v[1] == undefined ? "" : trim(v[1]);
        }
    }

    /**
     * 解析URL和Method
     */
    function parse_header_first_row(_t) {
        if (!_t.header) return false;
        var tmp = _t.header.shift().split(" ");

        _t.result.method  = tmp[0];
        _t.result.url     = tmp[1];
        _t.result.version = tmp[2];
    }

    function parse_query_data(data) {
        if (!data) return '';

        try {
            data = decodeURI(decodeURIComponent(data));
        } catch(e) {
            notify_success('URL数据格式错误，解析失败！请检查复制信息是否完整。', 4500);
            return;
        }

        var result = {};
        var query = data;

        if (data.indexOf('?') != -1 && typeof data.split("?")[1] != undefined) {
            query = data.split("?")[1];
        }

        var queryArr = query.split("&");

        if (queryArr.length < 2) return result;

        queryArr.forEach(function(item){
            var key = item.split("=")[0],
                value = item.split("=")[1];
            if (!key) return;
            result[key] = value || "";
        });
        return result;

    }

    /**
     * 解析form data数据
     */
    function parse_form_data(data, boundary) {
        if (!data) return data;

        var parser = new FormDataParser(data, boundary);

        return parser.parse();
    }

    ///////////////////////////////////////////
    /**
     * form data解析器
     *
     * @param {string} data
     * @param {string} boundary
     * @constructor
     */
    function FormDataParser(data, boundary) {
        this.result = '';
        this.boundary = trim(boundary || '');
        if (!data) return;

        this.data = array_unique(data.replace(/[\r]*/, "").split("\n"));
    }

    /**
     * 解析form data格式数据
     *
     * @returns {boolean}
     */
    FormDataParser.prototype.parse = function () {
        if (!this.data) return false;

        var i, v, obj = {}, key = false;
        for (i in this.data) {
            v = this.data[i];
            if (this.isBoundary(v)) {
                continue;
            }
            if (this.isKeyRow(v)) {
                key = this.parseKey(v); // 标注下个值就是value
                obj[key] = '';
                continue;
            }

            if (key) {
                obj[key] = v;
                key = '';
            }
        }

        return this.result = obj;
    };

    /**
     * 判断是否是可过滤数据
     *
     * @param data
     * @returns {boolean}
     */
    FormDataParser.prototype.isBoundary = function (data) {
        if (this.boundary) {
            if (data.indexOf(this.boundary) != -1) {
                return true;
            }
        }

        if (
            data.indexOf('--') != -1
            || data.toLocaleLowerCase().indexOf('content-length:') != -1
        ) {
            return true;
        }
        return false;
    };

    /**
     * 判断是否是字段名称行
     *
     * @param data
     * @returns {boolean}
     */
    FormDataParser.prototype.isKeyRow = function (data) {
        if (data.indexOf('name=') != -1 && data.indexOf('Content-Disposition: form-data;') != -1) {
            return true;
        }
        return false;
    };

    /**
     * 解析字段名称
     *
     * @param data
     * @returns {string}
     */
    FormDataParser.prototype.parseKey = function (data) {
        if (!data) return "";
        var t = data.match(/.*"([0-9-_a-zA-Z]*)".*/);

        return t[1];
    };


    w.HttpRequestParser = HttpRequestParser;
    w.FormDataParser = FormDataParser;
    w.parse_query_data = parse_query_data;
})(window);
</script>

<script>
    (function (w) {
        function msg(content, time, icon, anim) {
            time = time || 3500;
            layer.msg(content, {
                offset: 't',
                anim: anim || 3,
                icon: icon,
                time: time
            });
        }

        function success(content, time) {
            msg(content, time, 1)
        }

        function error(content, time) {
            msg(content, time, 2)
        }

        w.notify_success = success;
        w.notify_error = error;
    })(window);
</script>