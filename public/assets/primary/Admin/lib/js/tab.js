(function (w) {
    function Tab(iframe) {
        var self = this,
            tpl = $('#header-tab-tpl').html(),
            $menu = $('ul.tab-menu'),
            store = {}

        // 切换显示tab页
        this.switch = function (name) {
            iframe.switch(name)
            this.show(name)
        }

        this.show = function (name) {
            var $this = $('[data-action="tab-'+ name +'"]')
            // 移除tab按钮选中效果
            this.removeActive()
            // 添加tab按钮选中效果
            $this.addClass('active')
            // 去除按钮点击特效
            $this.find('a').removeClass('waves-effect waves-info')
            // $this.removeClass()
            // 隐藏关闭按钮
            $this.find('.tab-close').hide()

            return $this
        }

        // 重新加载iframe
        this.reload = function (name, url) {
            delete store[name]
            iframe.removeStore(name)
            this.open(name, url)
        }

        // 打开一个新的tab页
        this.open = function (name, url, label) {
            url = url || name
            label = label || name

            if (typeof store[name] != 'undefined') {
                this.switch(name)
                return false;
            }

            iframe.create(name, url)

            store[name] = true

            create_btn(name, label)

            var $tabBtn = this.show(name)
            // 绑定点击事件
            $tabBtn.find('.tab-close').click(function () {
                this.close(name)
            }.bind(this))
            // 点击tab切换显示iframe
            $tabBtn.find('span.tab-label').click(function () {
                this.switch(name)
            }.bind(this))
            // $tabBtn.find('.reload').click(function () {
            //      self.reload(name, url)
            // })
        }

        // 关闭tab窗
        this.close = function ($this) {
            var name
            if (typeof $this != 'object') {
                name = $this
                $this = $('[data-action="tab-'+ $this +'"]')
            } else {
                name = $this.data('action').replace('tab-', '')
            }
            // 移除按钮
            $this.remove()
            // 移除iframe
            iframe.remove(name)

            delete store[name]
        }

        this.removeActive = function () {
            var $all = $('li.tab')
            // 移除所有tab按钮选中特效
            $all.removeClass('active')
            $all.find('a').addClass('waves-effect waves-info')
            $all.find('.tab-close').show(300)
        }

        // 穿件tab按钮
        function create_btn(name, label) {
            if ($menu.find('[data-action="tab-' +name+ '"]').length > 0) {
                return false;
            }
            var html = tpl.replace('{name}', name).replace('{label}', label)

            $menu.append(html)
        }
    }

    function Iframe() {
        var tpl = $('#iframe-tpl').html(),
            store = {},
            $app = $('#lxh-app'),
            current

        // 切换显示iframe
        this.switch = function (name) {
            this.hide()
            var $iframe = $('#wrapper-' + name || document)
            // 显示当前iframe
            $iframe.show()
            current = name
        }

        this.removeStore = function (name) {
            delete store[name]
        }

        this.current = function () {
            return current
        }

        this.remove = function (name) {
            delete store[name]
            $('#wrapper-' + name).remove()
        }

        // 创建iframe弹窗
        this.create = function (name, url) {
            if (typeof store[name] != 'undefined') return true;

            current = name

            url = url || name;

            store[name] = true;

            var html = tpl.replace('{$name}', name).replace('{$url}', url);

            // 隐藏所有iframe
            this.hide()

            $app.append(html)

            var $iframe = $('#wrapper-' + name)
            // 显示当前iframe
            $iframe.show(220)

            $iframe.find('iframe').on('load', function (e) {
                this.height($(e.currentTarget))
            }.bind(this))
        }

        // 自动设置高度
        this.height = function ($this) {
            if (typeof $this[0] == 'undefined') return;

            var iframe = $this[0],
                iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow,
                height;
            if (iframeWin.document.body) {
                height = iframeWin.document.documentElement.scrollHeight || iframeWin.document.body.scrollHeight;
                // iframe.height = height
                $this.css('height', height + 'px')
            }
        };

        this.hide = function () {
            $('.lxh-wrapper').hide()
        }
    }

    var iframe = new Iframe(),
        tab = new Tab(iframe)

    w.$top = {
        tab: tab,
        iframe: iframe
    }

})(window)