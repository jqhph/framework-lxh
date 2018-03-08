(function (w) {
    var cache = LXHSTORE.cache,
        version = cache.getToken(),
        configs = LXHSTORE.loaderConfig,
        useCache = configs.save || false,
        lifetime = configs.lifetime || 8640000;

    function Loader(src, completed) {
        var queue = [];

        start(src, queue);

        function start(src, queue) {
            if (typeof data == 'string') {
                add(src);
            } else {
                for (var i in src) {
                    add(src[i]);
                }
            }

            request();
        }

        function add(src) {
            if (! src) return;
            if (src.indexOf('.css') == -1) {
                src = src.indexOf('.js') == -1 ? (src+'.js') : src;
            }

            queue.push(normalize_url(src));
        }

        function request() {
            var code, i;
            for (i in queue) {
                if (queue[i].indexOf('.css') != -1) {
                    async_load_style(queue[i]);
                    continue;
                }

                if (useCache && (code = cache.get(get_cache_key(queue[i])))) {
                    run(code);
                    is_completed(queue[i]);
                    continue;
                }
                $.ajax({
                    url : queue[i],
                    dataType: 'text',
                    // ifModified: true,
                    success: function (code) {
                        // 执行代码
                        run(code);
                        // 判断队列所有内容是否加载完毕
                        is_completed(this.url);
                        // 保存到缓存
                        save(this.url, code);
                    }
                });
            }
        }

        function async_load_style(css) {
            //异步延迟加载样式
            var link = $('<link />');
            link.attr('href', css);
            link.attr('async', true);
            link.attr('rel', 'stylesheet');
            link.load(function () {
                // 判断队列所有内容是否加载完毕
                is_completed(css);
            });
            link.appendTo($('head'));
        }

        // 判断队列所有内容是否加载完毕
        function is_completed(url) {
            queue = array_remove(queue, url);
            if (queue.length < 1 && completed) {
                // 加载完毕
                completed();
            }
        }
    }

    function run(code) {
        try {
            eval.call(w, code);
        } catch (e) {
            console.error('ERROR: ', e);
        }
    }

    function parse_alias(url) {
        url = url.split('?');

        if (typeof configs.alias[url[0]] != 'undefined') {
            url[0] = configs.alias[url[0]];
        }
        return url.join('?');
    }
    function parse_path(url) {
        url = url.split('/');

        if (typeof configs.paths[url[0]] != 'undefined') {
            url[0] = configs.paths[url[0]];
        }

        return url.join('/');
    }

    // 保存到缓存
    function save(url, code) {
        if (! useCache) return;
        // 缓存
        var key = get_cache_key(url);

        cache.set(key, code);
        cache.expire(key, lifetime);
    }

    function get_cache_key(url) {
        return get_path(url).replace(/\//gi, '');
    }

    function get_path(url) {
        return url.split('?')[0];
    }

    // 获取正常的url
    function normalize_url(url) {
        url = parse_path(parse_alias(url));
        if (url.indexOf('?') == -1) {
            return url + '?_js&_=' + version
        }
        return url;
    }

    w.LxhLoader = Loader
})(window);