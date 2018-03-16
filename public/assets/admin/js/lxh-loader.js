(function (w) {
    var cache = LXHSTORE.cache,
        version = cache.getToken(),
        configs = LXHSTORE.loaderConfig,
        useCache = configs.save || false,
        lifetime = configs.lifetime || 8640000;

    function Loader(srcs, completed) {
        var queue = [],
            unstores = [],
        // 保证代码按顺序执行
            map = {};

        var loader = {
            add: function (src) {
                if (typeof src == 'object') {
                    for (var i in src) {
                        this.add(src[i]);
                    }
                    return this;
                }

                if (! src) return;
                if (src.indexOf('.css') == -1) {
                    src = src.indexOf('.js') == -1 ? (src+'.js') : src;
                }

                src = normalize_url(src);
                map[src] = '';
                queue.push(src);
                return this;
            },
            // 发起请求
            request: function () {
                var code, i;
                for (i in queue) {
                    if (queue[i].indexOf('.css') != -1) {
                        async_load_style(queue[i]);
                        continue;
                    }

                    if (useCache && saveable(queue[i]) && (code = cache.get(get_cache_key(queue[i])))) {
                        run(code);
                        is_completed(queue[i]);
                        continue;
                    }
                    $.ajax({
                        url : queue[i],
                        dataType: 'text',
                        ifModified: false,
                        success: function (code) {
                            map[this.url] = code;
                            // 判断队列所有内容是否加载完毕
                            is_completed(this.url);
                            // 保存到缓存
                            save(this.url, code);
                        }
                    });
                }
            },

            // 禁止本地存储的路径
            disableStorage: function (path) {
                if (!path) return this;
                if (typeof path == 'object') {
                    for (var i in path) {
                        this.disableStorage(path[i]);
                    }
                    return this;
                }

                unstores.push(get_path(path));
                return this;
            },

            completed: function (callback) {
                completed = callback;
                return this;
            }

        };

        loader.add(srcs);

        for (var i in loader) {
            this[i] = loader[i].bind(this);
        }

        /////////////////////////////////////////////////
        function saveable(path) {
            path = get_path(path);

            for (var i in unstores) {
                if (unstores[i] == path) return false;
            }
            return true;
        }

        function async_load_style(css) {
            //异步延迟加载样式
            var link = $('<link />');
            link.attr('href', css);
            link.attr('rel', 'stylesheet');
            link.load(function () {
                // 判断队列所有内容是否加载完毕
                is_completed(css);
            });
            link.appendTo($('head'));
        }

        // 保存到缓存
        function save(url, code) {
            if (! useCache || !saveable(url)) return;
            // 缓存
            var key = get_cache_key(url);

            cache.set(key, code);
            cache.expire(key, lifetime);
        }

        // 判断队列所有内容是否加载完毕
        function is_completed(url) {
            queue = array_remove(queue, url);
            if (queue.length < 1 && completed) {
                for (var i in map) {
                    run(map[i]);
                }

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

    function get_cache_key(url) {
        return get_path(url).replace(/\//gi, '');
    }

    function get_path(url) {
        return url ? url.split('?')[0] : '';
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