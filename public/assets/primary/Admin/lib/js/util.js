(function (o) {
    o.dataStore = {}

    // 数组去重
    o.array_unique = function ($this) {
        var res = [], json = {}, i
        for (i = 0; i < $this.length; i++) {
            if (!json[$this[i]]) {
                res.push($this[i]);
                json[$this[i]] = 1;
            }
        }
        return res;
    }

    // 添加初始化完成后执行的动作
    o.lxhActions = []
    o.__then__ = function (call) {
        lxhActions.push(call)
    }

    // 添加需要引入的js
    o.jsLibArr = []
    o.require_js = function (data) {
        if (typeof data == 'string') {
            jsLibArr.push(data)
        } else {
            for (var i in data) {
                jsLibArr.push(data[i])
            }
        }
    }

    // 添加需要引入的css
    o.cssLibArr = []
    o.require_css = function (data) {
        if (typeof data == 'string') {
            cssLibArr.push(data)
        } else {
            for (var i in data) {
                cssLibArr.push(data[i])
            }
        }
    }
    /**
     * Convert name from Camel Case format to underscore.
     * ex. camelCase to camel_case
     *
     * @param string
     * @return string
     */
    o.to_under_score = function (str) {
        str = str.replace(/([A-Z])/g, function (full, match) {
            return '-' + match.toLowerCase()
        })
        if (str.indexOf('-') === 0) {
            return str.replace('-', '')
        }
        return str
    }

    /**
     * 解析视图路径名
     *
     * @param c controller
     * @param a action
     * @returns {*}
     */
    o.parse_view_name = function (c, a) {
        return 'module/' + to_under_score(c) + '/' + to_under_score(a)
    }

    // 把json对象转化为get字符串
    o.build_http_params = function (param, key) {
        var paramStr = "";
        if (param instanceof String || param instanceof Number || param instanceof Boolean) {
            if (key) paramStr += "&" + key + "=" + encodeURIComponent(param);
        } else {
            $.each(param, function (i) {
                var k = key == null ? i : key + (param instanceof Array ? "[" + i + "]" : "." + i)
                paramStr += '&' + build_http_params(this, k);
            });
        }
        return paramStr.substr(1);
    }

    // loading效果
    o.loading = function (el, circle, timeout) {
        el = el || '#lxh-app'
        function loading() {
            var $el = typeof el == 'object' ? el : $(el)
            if (circle) {
                $el.append('<div class=" loading loading-circle"></div>')
            } else {
                $el.append('<div class=" loading"><div class="loading1"></div><div class="loading2"></div><div class="loading3"></div></div>')
            }
            this.close = function () {
                $el.find('.loading').remove()
            }
            if (timeout) setTimeout(this.close, timeout)
        }
        return new loading()
    }

    // 格式化php时间戳
    o.format_php_timestamp = function (time, format) {
        if (! time || parseInt(time) < 1) return ''
        return new Date(parseInt(time + '000')).format(format || 'yyyy-mm-dd hh:ii:ss')
    }

    // new Date(1458692752478).format('yyyy-mm-dd hh:ii:ss')
    Date.prototype.format = function(format) {
        var date = {
            "m+": this.getMonth() + 1,
            "d+": this.getDate(),
            "h+": this.getHours(),
            "i+": this.getMinutes(),
            "s+": this.getSeconds(),
            "q+": Math.floor((this.getMonth() + 3) / 3),
            "S+": this.getMilliseconds()
        };
        if (/(y+)/i.test(format)) {
            format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
        }
        for (var k in date) {
            if (new RegExp("(" + k + ")").test(format)) {
                format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
            }
        }
        return format;
    }
})(window);

(function (w) {
    function Tab(iframe) {
        var self = this,
            tpl = $('#header-tab-tpl').html(),
            $menu = $('ul.tab-menu'),
            store = {},
            firstIndex = 'home',
            def = {name: firstIndex, url: '', label: ''},
            histories = [def],
            current = def;

        this.current = function () {
            return this.switch()
        };

        // 返回当前tab按钮和iframe的JQ元素对象
        this.currentEl = function () {
            var name = current.name;
            return {'tab': $('[data-action="tab-'+ name +'"]'), 'iframe': $('#wrapper-' + name)};
        };

        // 切换显示tab页
        this.switch = function (name, url, label) {
            if (! name) {
                name = current.name;
                url = current.url;
                label = current.label;
            } else {
                current = {name: name, url: url, label: label};
            }
            iframe.switch(name, url);
            this.show(name, url, label);
            this.addHistory(name, url, label);
        };

        // 返回上一级tab
        this.back = function (step) {
            step = (parseInt(step) || 1);
            var data = histories[step] || def;
            histories.splice(step - 1, 1);
            this.switch(data.name, data.url, data.label);
        };

        this.show = function (name, url, label) {
            var $this = $('[data-action="tab-'+ name +'"]');
            if ($this.length < 1) {
                return this.open(name, url, label)
            }
            // 移除tab按钮选中效果
            this.removeActive();
            // 添加tab按钮选中效果
            $this.addClass('active');
            // 去除按钮点击特效
            $this.find('a').removeClass('waves-effect waves-info');
            // $this.removeClass()
            // 隐藏关闭按钮
            // $this.find('.tab-close').hide()
            return $this
        };

        this.addHistory = function (name, url, label) {
            histories = unset(histories, 'name', name)
            histories.unshift({name: name, url: url, label: label})
        };

        // 重新加载iframe
        this.reload = function (name, url, label) {
            if (! name) {
                name = current.name;
                url = current.url;
                label = current.label;
            }

            delete store[name];
            iframe.remove(name);
            this.open(name, url, label);
            this.addHistory(name, url, label)
        };

        // 打开一个新的tab页
        this.open = function (name, url, label) {
            url = url || name;
            label = label || name;

            current = {name: name, url: url, label: label};
            if (typeof store[name] != 'undefined') {
                this.switch(name);
                return false;
            }
            firstIndex = firstIndex || name;

            iframe.create(name, url);

            store[name] = true;

            create_btn(name, label);

            var $tabBtn = this.show(name);
            // 绑定点击事件
            $tabBtn.find('.tab-close').off('click');
            $tabBtn.find('.tab-close').click(function () {
                this.close(name);
            }.bind(this));
            // 点击tab切换显示iframe
            $tabBtn.off('click');
            $tabBtn.click(function () {
                this.switch(name)
            }.bind(this));
            $tabBtn.find('.icon-refresh').off('click');
            $tabBtn.find('.icon-refresh').click(function () {
                self.reload(name, url)
            })
        };

        // 关闭tab窗
        this.close = function ($this) {
            if (! $this) {
                $this = current.name;
            }

            var name;
            if (typeof $this != 'object') {
                name = $this;
                $this = $('[data-action="tab-'+ $this +'"]');
            } else {
                name = $this.data('action').replace('tab-', '');
            }
            // 移除按钮
            $this.remove();
            // 移除iframe
            iframe.remove(name);

            delete store[name];
            // 删除历史记录
            histories = unset(histories, 'name', name)
            // 返回上一页
            if (current.name === name) {
                this.back()
            }
        };

        this.removeActive = function () {
            var $all = $('li.tab');
            // 移除所有tab按钮选中特效
            $all.removeClass('active')
            $all.find('a').addClass('waves-effect waves-info');
            $all.find('.tab-close').show(300)
        };

        // 删除数组元素
        function unset(arr, k, value) {
            var i, res = [];
            for (i in arr) {
                if (arr[i][k] == value) continue;
                res.push(arr[i]);
            }
            return res
        }

        // 创建tab按钮
        function create_btn(name, label) {
            if ($menu.find('[data-action="tab-' +name+ '"]').length > 0) {
                return false;
            }
            var html = tpl.replace('{name}', name).replace('{name}', name).replace('{label}', label)
            $menu.append(html)
        }
    }

    function Iframe() {
        var tpl = $('#iframe-tpl').html(),
            store = {},
            $app = $('#lxh-app'),
            current;

        // 切换显示iframe
        this.switch = function (name, url) {
            this.hide();
            var $iframe = $('#wrapper-' + name || document)

            if ($iframe.length < 1) {
                return this.create(name, url)
            }

            // 显示当前iframe
            $iframe.show();
            current = name
        };

        this.removeStore = function (name) {
            delete store[name]
        };

        this.current = function () {
            return current
        };

        this.remove = function (name) {
            delete store[name];
            $('#wrapper-' + name).remove()
        };

        // 创建iframe弹窗
        this.create = function (name, url) {
            if (typeof store[name] != 'undefined') return true;

            var $loading = w.loading($app);
            current = name;
            url = url || name;

            store[name] = true;

            var html = tpl.replace('{$name}', name).replace('{$url}', url);

            // 隐藏所有iframe
            this.hide();

            $app.append(html);

            var $iframe = $('#wrapper-' + name);
            // 显示当前iframe
            $iframe.show(220);

            $iframe.find('iframe').load(function (e) {
                this.height($(e.currentTarget));
                $loading.close()
            }.bind(this))
        };

        // 自动设置高度
        this.height = function ($iframe) {
            if (! $iframe) $iframe = $('#wrapper-' + current);
            if (typeof $iframe[0] == 'undefined') return;
            var iframe = $iframe[0],
                iframeWin = (iframe.contentWindow || iframe.contentDocument.parentWindow) || iframe,
                height;
            if (iframeWin.document.body) {
                height = iframeWin.document.documentElement.scrollHeight || iframeWin.document.body.scrollHeight;
                // iframe.height = height
                $iframe.css('height', height + 'px')
            }
        };

        this.hide = function () {
            $('.lxh-wrapper').hide()
        }
    }

    w.Tab = Tab
    w.Iframe = Iframe
})(window);