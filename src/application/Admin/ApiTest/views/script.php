<script>
__then__(function () {
    var $doms = {
            add: $('.add-key-val'),
            sbt: $('button.submit'),
            auto: $('.auto-input'),
            sthr: $('.set-header'),
            form: $('.request-form'),
            url: $('input[name="req-url"]'),
            method: $('select[name="req-method"]'),
            body: $('.req-box'),
            rtdef: $('.reset-def') // 重置缓存参数
        },
        inputTpl = $('#kv-input-tpl').html(),
        selectors = {
            rm: '.remove-key-val',
            kv: '.k-v-input',
            k: '.ikey',
            v: '.ival'
        },
        cache = LXHSTORE.cache,
        cacheKey = 'api-default-params',
        defParamDoms = {},
        current = LXHSTORE.IFRAME.current();

    // 提交表单
    $doms.sbt.click(function () {
        var form = get_form_data();

        console.log('submit', form);
    });

    // 新增key-value输入框
    $doms.add.click(function () {
        add_input();
    });

    // 字符串自动填充
    $doms.auto.click(function () {
        var auto = new AutoInput();
        auto.build();


    });

    // 设置HEADER头信息
    $doms.sthr.click(function () {

    });

    // 重置默认参数
    $doms.rtdef.click(function () {
        set_default_params();
    });

    /**
     * 设置默认提交参数到key value输入框
     */
    function set_default_params() {
        var def = cache.get(cacheKey, {}),
            kname,
            vname,
            i,
            k,
            v,
            arr = [];

        for (k in def) {
            arr.unshift({k: k, v: def[k]});
        }

        for (i in arr) {
            k = arr[i].k;
            v = arr[i].v;
            kname = k+'k';
            vname = k+'v';

            // 先移除
            $('input[name="'+vname+'"]').parents(selectors.kv).remove();

            // 渲染视图
            add_input(k, v, true, '#009688');
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
    function add_input(k, v, prepend, color) {
        k = k || "";
        v = v || "";
        color = color || "";
        var view = new Blade(inputTpl, {key: k, value: v, color: color}), rm;

        prepend ? $doms.body.prepend(view.fetch()) : $doms.body.append(view.fetch());
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
        set_default_params();
    }

    setup();
});
</script>

<script>
(function (w) {
    var selectors = {
        tpl: '#auto-input-tpl',
        tbtpl: '#parse-table-tpl',
        parse: '.parse-raw',
        raw: 'textarea[name="req-raw"]',
        resc: '.parse-result-container'
    }, pd = w.parent.document;

    function AutoInput() {

    }

    /**
     * 构建弹窗表单模板
     *
     * @return string
     */
    AutoInput.prototype.build = function () {
        var self = this;
        var index = layer.open({
            type: 1,
            content: $(selectors.tpl).html(),
            area: ['70%', '80%'],
            maxmin: true,
            shadeClose: true,
            shade: false,
            success: self.bindEvents.bind(self),
            btn: ['解析', '添加到模拟请求数据中'],
            btn1: function () {
                var raw = $(selectors.raw, pd).val(),
                    parser = new HttpRequestParser(raw);
                parser.parse();

                self.buildTables(parser);
            },
            btn2: function () {
                console.log(333, '添加到测试数据')
                return false
            }
        });
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
            var first = new Blade(tpl, {
                title: "RESULT",
                list: {url: parser.getUrl(), method: parser.getMethod()},
                colspan: 2
            });
            $container.append(first.fetch());
        }

        // 显示query信息
        var query = new Blade(tpl, {
            title: "QUERY",
            list: parser.getQuery(),
            class: 'parsed-query',
            colspan: 2
        });
        $container.append(query.fetch());

        // 显示HEADER头信息
        var header = new Blade(tpl, {
            title: "HEADER",
            list: parser.getHeader(),
            class: 'parsed-header',
            checkbox: 1,
            colspan: 3
        });
        $container.append(header.fetch());

        // 显示HEADER头信息
        var post = new Blade(tpl, {
            title: "DATA",
            list: parser.getData(),
            class: 'parsed-post',
            checkbox: 1,
            colspan: 3
        });
        $container.append(post.fetch());

    };

    AutoInput.prototype.bindEvents = function () {
        var self = this;
        //
        $(selectors.parse, pd).click(function (e) {
            var raw = $(selectors.raw, pd).val();
            var parser = new HttpRequestParser(raw);
            parser.parse();

            self.buildTables(parser);
        });
    };

    w.AutoInput = AutoInput;
})(window);
</script>

<script>
(function (w) {
    var wsp = "\n\n", osp = "\r\n";

    function HttpRequestParser(content) {
        this.content = content;
        this.result  = {header: {}, data: {}, query: {}, url: '', method: '', version: ''};
        this.header  = '';
        this.data    = '';
        this.sp      = osp; // 协议分隔符

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

    /**
     * 判断请求数据是否是webform形式提交
     *
     * @returns {boolean}
     */
    HttpRequestParser.prototype.isFormData = function () {
        if (!this.data) {
            return false;
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

        // 解析POST请求数据
        if (this.isFormData()) {
            this.result.data = parse_form_data(this.data);
        } else {
            this.result.data = parse_query_data(this.data);
        }

        // 解析HEADER头信息
        if (this.header) {
            parse_header_first_row(this);
            parse_header(this);
        }

        if (this.result.url) {
            this.result.query = parse_query_data(this.result.url);
        }

        console.log('http request data', this.result);

        return this.result;
    };

    HttpRequestParser.prototype.getHeader = function () {
        return this.result.header;
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

    /**
     * 解析header头
     */
    function parse_header(_t) {
        if (!_t.header) return false;

        var i, v, sp = ':';
        for (i in _t.header) {
            if (_t.header[i].indexOf(': ') != -1) {
                sp = ': ';
            }
            v = _t.header[i].split(sp);
            _t.result.header[v[0]] = typeof v[1] == undefined ? "" : v[1];
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
            layer.msg('URL数据格式错误，解析失败！请检查复制信息是否完整。', {
                offset: 't',
                anim: 3,
                icon: 2,
                time: 4500
            });
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
    function parse_form_data(data) {
        if (!data) return data;

        var parser = new FormDataParser(data);

        return parser.parse();
    }

    ///////////////////////////////////////////
    /**
     * form data解析器
     *
     * @param data
     * @constructor
     */
    function FormDataParser(data) {
        this.result = '';
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
            if (this.isUnimportant(v)) {
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
    FormDataParser.prototype.isUnimportant = function (data) {
        if (
            data.indexOf('--') != -1
            || data.indexOf('Content-Length:') != -1
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
