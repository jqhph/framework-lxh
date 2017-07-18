/**
 * 初始化js
 *
 * Created by Jqh on 2017/7/3.
 */
/*! Sea.js 2.2.3 | seajs.org/LICENSE.md */
!function(a,b){function c(a){return function(b){return{}.toString.call(b)=="[object "+a+"]"}}function d(){return B++}function e(a){return a.match(E)[0]}function f(a){for(a=a.replace(F,"/");a.match(G);)a=a.replace(G,"/");return a=a.replace(H,"$1/")}function g(a){var b=a.length-1,c=a.charAt(b);return"#"===c?a.substring(0,b):".js"===a.substring(b-2)||a.indexOf("?")>0||".css"===a.substring(b-3)||"/"===c?a:a+".js"}function h(a){var b=v.alias;return b&&x(b[a])?b[a]:a}function i(a){var b=v.paths,c;return b&&(c=a.match(I))&&x(b[c[1]])&&(a=b[c[1]]+c[2]),a}function j(a){var b=v.vars;return b&&a.indexOf("{")>-1&&(a=a.replace(J,function(a,c){return x(b[c])?b[c]:a})),a}function k(a){var b=v.map,c=a;if(b)for(var d=0,e=b.length;e>d;d++){var f=b[d];if(c=z(f)?f(a)||a:a.replace(f[0],f[1]),c!==a)break}return c}function l(a,b){var c,d=a.charAt(0);if(K.test(a))c=a;else if("."===d)c=f((b?e(b):v.cwd)+a);else if("/"===d){var g=v.cwd.match(L);c=g?g[0]+a.substring(1):a}else c=v.base+a;return 0===c.indexOf("//")&&(c=location.protocol+c),c}function m(a,b){if(!a)return"";a=h(a),a=i(a),a=j(a),a=g(a);var c=l(a,b);return c=k(c)}function n(a){return a.hasAttribute?a.src:a.getAttribute("src",4)}function o(a,b,c,d){var e=T.test(a),f=M.createElement(e?"link":"script");c&&(f.charset=c),A(d)||f.setAttribute("crossorigin",d),p(f,b,e,a),e?(f.rel="stylesheet",f.href=a):(f.async=!0,f.src=a),U=f,S?R.insertBefore(f,S):R.appendChild(f),U=null}function p(a,c,d,e){function f(){a.onload=a.onerror=a.onreadystatechange=null,d||v.debug||R.removeChild(a),a=null,c()}var g="onload"in a;return!d||!W&&g?(g?(a.onload=f,a.onerror=function(){D("error",{uri:e,node:a}),f()}):a.onreadystatechange=function(){/loaded|complete/.test(a.readyState)&&f()},b):(setTimeout(function(){q(a,c)},1),b)}function q(a,b){var c=a.sheet,d;if(W)c&&(d=!0);else if(c)try{c.cssRules&&(d=!0)}catch(e){"NS_ERROR_DOM_SECURITY_ERR"===e.name&&(d=!0)}setTimeout(function(){d?b():q(a,b)},20)}function r(){if(U)return U;if(V&&"interactive"===V.readyState)return V;for(var a=R.getElementsByTagName("script"),b=a.length-1;b>=0;b--){var c=a[b];if("interactive"===c.readyState)return V=c}}function s(a){var b=[];return a.replace(Y,"").replace(X,function(a,c,d){d&&b.push(d)}),b}function t(a,b){this.uri=a,this.dependencies=b||[],this.exports=null,this.status=0,this._waitings={},this._remain=0}if(!a.seajs){var u=a.seajs={version:"2.2.3"},v=u.data={},w=c("Object"),x=c("String"),y=Array.isArray||c("Array"),z=c("Function"),A=c("Undefined"),B=0,C=v.events={};u.on=function(a,b){var c=C[a]||(C[a]=[]);return c.push(b),u},u.off=function(a,b){if(!a&&!b)return C=v.events={},u;var c=C[a];if(c)if(b)for(var d=c.length-1;d>=0;d--)c[d]===b&&c.splice(d,1);else delete C[a];return u};var D=u.emit=function(a,b){var c=C[a],d;if(c)for(c=c.slice();d=c.shift();)d(b);return u},E=/[^?#]*\//,F=/\/\.\//g,G=/\/[^/]+\/\.\.\//,H=/([^:/])\/\//g,I=/^([^/:]+)(\/.+)$/,J=/{([^{]+)}/g,K=/^\/\/.|:\//,L=/^.*?\/\/.*?\//,M=document,N=e(M.URL),O=M.scripts,P=M.getElementById("seajsnode")||O[O.length-1],Q=e(n(P)||N);u.resolve=m;var R=M.head||M.getElementsByTagName("head")[0]||M.documentElement,S=R.getElementsByTagName("base")[0],T=/\.css(?:\?|$)/i,U,V,W=+navigator.userAgent.replace(/.*(?:AppleWebKit|AndroidWebKit)\/(\d+).*/,"$1")<536;u.request=o;var X=/"(?:\\"|[^"])*"|'(?:\\'|[^'])*'|\/\*[\S\s]*?\*\/|\/(?:\\\/|[^\/\r\n])+\/(?=[^\/])|\/\/.*|\.\s*require|(?:^|[^$])\brequire\s*\(\s*(["'])(.+?)\1\s*\)/g,Y=/\\\\/g,Z=u.cache={},$,_={},ab={},bb={},cb=t.STATUS={FETCHING:1,SAVED:2,LOADING:3,LOADED:4,EXECUTING:5,EXECUTED:6};t.prototype.resolve=function(){for(var a=this,b=a.dependencies,c=[],d=0,e=b.length;e>d;d++)c[d]=t.resolve(b[d],a.uri);return c},t.prototype.load=function(){var a=this;if(!(a.status>=cb.LOADING)){a.status=cb.LOADING;var c=a.resolve();D("load",c);for(var d=a._remain=c.length,e,f=0;d>f;f++)e=t.get(c[f]),e.status<cb.LOADED?e._waitings[a.uri]=(e._waitings[a.uri]||0)+1:a._remain--;if(0===a._remain)return a.onload(),b;var g={};for(f=0;d>f;f++)e=Z[c[f]],e.status<cb.FETCHING?e.fetch(g):e.status===cb.SAVED&&e.load();for(var h in g)g.hasOwnProperty(h)&&g[h]()}},t.prototype.onload=function(){var a=this;a.status=cb.LOADED,a.callback&&a.callback();var b=a._waitings,c,d;for(c in b)b.hasOwnProperty(c)&&(d=Z[c],d._remain-=b[c],0===d._remain&&d.onload());delete a._waitings,delete a._remain},t.prototype.fetch=function(a){function c(){u.request(g.requestUri,g.onRequest,g.charset,g.crossorigin)}function d(){delete _[h],ab[h]=!0,$&&(t.save(f,$),$=null);var a,b=bb[h];for(delete bb[h];a=b.shift();)a.load()}var e=this,f=e.uri;e.status=cb.FETCHING;var g={uri:f};D("fetch",g);var h=g.requestUri||f;return!h||ab[h]?(e.load(),b):_[h]?(bb[h].push(e),b):(_[h]=!0,bb[h]=[e],D("request",g={uri:f,requestUri:h,onRequest:d,charset:z(v.charset)?v.charset(h):v.charset,crossorigin:z(v.crossorigin)?v.crossorigin(h):v.crossorigin}),g.requested||(a?a[g.requestUri]=c:c()),b)},t.prototype.exec=function(){function a(b){return t.get(a.resolve(b)).exec()}var c=this;if(c.status>=cb.EXECUTING)return c.exports;c.status=cb.EXECUTING;var e=c.uri;a.resolve=function(a){return t.resolve(a,e)},a.async=function(b,c){return t.use(b,c,e+"_async_"+d()),a};var f=c.factory,g=z(f)?f(a,c.exports={},c):f;return g===b&&(g=c.exports),delete c.factory,c.exports=g,c.status=cb.EXECUTED,D("exec",c),g},t.resolve=function(a,b){var c={id:a,refUri:b};return D("resolve",c),c.uri||u.resolve(c.id,b)},t.define=function(a,c,d){var e=arguments.length;1===e?(d=a,a=b):2===e&&(d=c,y(a)?(c=a,a=b):c=b),!y(c)&&z(d)&&(c=s(""+d));var f={id:a,uri:t.resolve(a),deps:c,factory:d};if(!f.uri&&M.attachEvent){var g=r();g&&(f.uri=g.src)}D("define",f),f.uri?t.save(f.uri,f):$=f},t.save=function(a,b){var c=t.get(a);c.status<cb.SAVED&&(c.id=b.id||a,c.dependencies=b.deps||[],c.factory=b.factory,c.status=cb.SAVED)},t.get=function(a,b){return Z[a]||(Z[a]=new t(a,b))},t.use=function(b,c,d){var e=t.get(d,y(b)?b:[b]);e.callback=function(){for(var b=[],d=e.resolve(),f=0,g=d.length;g>f;f++)b[f]=Z[d[f]].exec();c&&c.apply(a,b),delete e.callback},e.load()},t.preload=function(a){var b=v.preload,c=b.length;c?t.use(b,function(){b.splice(0,c),t.preload(a)},v.cwd+"_preload_"+d()):a()},u.use=function(a,b){return t.preload(function(){t.use(a,b,v.cwd+"_use_"+d())}),u},t.define.cmd={},a.define=t.define,u.Module=t,v.fetchedList=ab,v.cid=d,u.require=function(a){var b=t.get(t.resolve(a));return b.status<cb.EXECUTING&&(b.onload(),b.exec()),b.exports};var db=/^(.+?\/)(\?\?)?(seajs\/)+/;v.base=(Q.match(db)||["",Q])[1],v.dir=Q,v.cwd=N,v.charset="utf-8",v.preload=function(){var a=[],b=location.search.replace(/(seajs-\w+)(&|$)/g,"$1=1$2");return b+=" "+M.cookie,b.replace(/(seajs-\w+)=1/g,function(b,c){a.push(c)}),a}(),u.config=function(a){for(var b in a){var c=a[b],d=v[b];if(d&&w(d))for(var e in c)d[e]=c[e];else y(d)?c=d.concat(c):"base"===b&&("/"!==c.slice(-1)&&(c+="/"),c=l(c)),v[b]=c}return D("config",a),u}}}(this);
!function(){function a(a){return function(b){return{}.toString.call(b)=="[object "+a+"]"}}function b(a){return"[object Function]"=={}.toString.call(a)}function c(a,c,e,f){var g=u.test(a),h=r.createElement(g?"link":"script");if(e){var i=b(e)?e(a):e;i&&(h.charset=i)}void 0!==f&&h.setAttribute("crossorigin",f),d(h,c,g,a),g?(h.rel="stylesheet",h.href=a):(h.async=!0,h.src=a),p=h,t?s.insertBefore(h,t):s.appendChild(h),p=null}function d(a,b,c,d){function f(){a.onload=a.onerror=a.onreadystatechange=null,c||seajs.data.debug||s.removeChild(a),a=null,b()}var g="onload"in a;return!c||!v&&g?(g?(a.onload=f,a.onerror=function(){seajs.emit("error",{uri:d,node:a}),f()}):a.onreadystatechange=function(){/loaded|complete/.test(a.readyState)&&f()},void 0):(setTimeout(function(){e(a,b)},1),void 0)}function e(a,b){var c,d=a.sheet;if(v)d&&(c=!0);else if(d)try{d.cssRules&&(c=!0)}catch(f){"NS_ERROR_DOM_SECURITY_ERR"===f.name&&(c=!0)}setTimeout(function(){c?b():e(a,b)},20)}function f(a){return a.match(x)[0]}function g(a){for(a=a.replace(y,"/"),a=a.replace(A,"$1/");a.match(z);)a=a.replace(z,"/");return a}function h(a){var b=a.length-1,c=a.charAt(b);return"#"===c?a.substring(0,b):".js"===a.substring(b-2)||a.indexOf("?")>0||".css"===a.substring(b-3)||"/"===c?a:a+".js"}function i(a){var b=w.alias;return b&&q(b[a])?b[a]:a}function j(a){var b,c=w.paths;return c&&(b=a.match(B))&&q(c[b[1]])&&(a=c[b[1]]+b[2]),a}function k(a){var b=w.vars;return b&&a.indexOf("{")>-1&&(a=a.replace(C,function(a,c){return q(b[c])?b[c]:a})),a}function l(a){var c=w.map,d=a;if(c)for(var e=0,f=c.length;f>e;e++){var g=c[e];if(d=b(g)?g(a)||a:a.replace(g[0],g[1]),d!==a)break}return d}function m(a,b){var c,d=a.charAt(0);if(D.test(a))c=a;else if("."===d)c=g((b?f(b):w.cwd)+a);else if("/"===d){var e=w.cwd.match(E);c=e?e[0]+a.substring(1):a}else c=w.base+a;return 0===c.indexOf("//")&&(c=location.protocol+c),c}function n(a,b){if(!a)return"";a=i(a),a=j(a),a=k(a),a=h(a);var c=m(a,b);return c=l(c)}function o(a){return a.hasAttribute?a.src:a.getAttribute("src",4)}var p,q=a("String"),r=document,s=r.head||r.getElementsByTagName("head")[0]||r.documentElement,t=s.getElementsByTagName("base")[0],u=/\.css(?:\?|$)/i,v=+navigator.userAgent.replace(/.*(?:AppleWebKit|AndroidWebKit)\/?(\d+).*/i,"$1")<536;seajs.request=c;var w=seajs.data,x=/[^?#]*\//,y=/\/\.\//g,z=/\/[^/]+\/\.\.\//,A=/([^:/])\/+\//g,B=/^([^/:]+)(\/.+)$/,C=/{([^{]+)}/g,D=/^\/\/.|:\//,E=/^.*?\/\/.*?\//,r=document,F=location.href&&0!==location.href.indexOf("about:")?f(location.href):"",G=r.scripts,H=r.getElementById("seajsnode")||G[G.length-1];f(o(H)||F),seajs.resolve=n,define("seajs/seajs-css/1.0.5/seajs-css",[],{})}();

(function (window) {
    var config = get_config()

    var $cache = new Cache()

    dispatcher()

    // var router = new Router(config.options.config, dispatcher)

    function dispatcher() {
        // var currentView = parse_view_name(config.options.controller, config.options.action, config.options.config['js-version'])
        // config.publicJs.push(currentView)

        // 设置缓存token
        $cache.setToken(config.options.config['js-version'])

        config.options.cache = $cache
        // 处理需要加载的js数组
        config.publicJs = get_public_js(config.publicJs, config.options.config['js-version'])
        // 处理需要加载的js数组
        config.publicCss = get_public_css(config.publicCss, config.options.config['js-version'])

        config.seaConfig = get_sea_config(config.seaConfig, config.options.config['js-version'])

        seajs.config(config.seaConfig)
        // 加载css
        seajs.use(config.publicCss)

        // 优先加载jquery
        // seajs.use('jquery', function (q) {
        seajs.use(config.publicJs, function () {
            var plugIns = arguments // 所有加载进来的js插件变量数组
            init(function () {
                $(function () {
                    if (typeof lxh_action == 'function') {
                        // 运行当前页js
                        lxh_action.apply(this, plugIns)
                    }
                })
            })

        })
        // })
    }

    /**
     * 初始化
     *
     * @param call
     */
    function init(call) {
        window.$lxh = new Lxh(config.options)

        var lang = $lxh.config().get('language')

        var serverOptions = $lxh.createStore({})
        if (typeof load_data == 'function') {
            serverOptions.set(load_data())
        }

        // 语言包设置
        $lxh.language().type(lang)
        // 注入语言包数据
        $lxh.language().fill(serverOptions.get('language') || null, true)
        // 注入模板数据
        $lxh.tpl().fill(serverOptions.get('tpl') || null, true)

        // 生成table 展示隐藏字段功能按键
        $('[data-pattern]').each(function () {
            var $tableScrollWrapper = $(this);
            if (typeof $tableScrollWrapper.responsiveTable != 'undefined') {
                $tableScrollWrapper.responsiveTable($tableScrollWrapper.data());
            }
        });
        
        call()
    }

    function get_lang_cache_key(lang) {
        return 'language_' + lang
    }

    /**
     * 检测缓存中是否存在语言包，返回需要加载的语言包模块
     *
     * @param lang
     * @param scopes
     * @param useCache
     * @returns {*}
     */
    function check_cache_language(lang, scopes, useCache) {
        var cacheKey = get_lang_cache_key(lang), package = $cache.get(cacheKey), t = [], i

        if (typeof add_lang_scopes == 'function') {
            var addScopes = add_lang_scopes()
            for (i in addScopes) {
                scopes.push(addScopes[i])
            }
        }

        if (! package || ! useCache) return scopes || []
        for (i in scopes) {
            if (! package[scopes[i]]) t.push(scopes[i])
        }
        return t || []
    }


    function get_tpl_cache_key()
    {
        return 'tpls'
    }

    /**
     * 检测缓存中是否存在需要载入的模板，返回需要载入的模板名称
     */
    // function check_cache_tpl(names, useCache)
    // {
    //     var cacheKey = get_tpl_cache_key(), tpls = $cache.get(cacheKey), t = [], i
    //
    //     if (typeof add_tpls == 'function') {
    //         var addTpls = add_tpls()
    //         for (i in addTpls) {
    //             names.push(addTpls[i])
    //         }
    //     }
    //
    //     if (! tpls || ! useCache) return names
    //     for (i in names) {
    //         if (! tpls[names[i]]) t.push(names[i])
    //     }
    //     return t
    // }

    /**
     * 处理需要加载的css数组
     *
     * @param publicCss
     * @param v
     * @returns {*}
     */
    function get_public_css(publicCss, v) {
        if (typeof cssLibArr != 'undefined') {
            for (var i in cssLibArr) {
                publicCss.push(cssLibArr[i])
            }
        }

        for (i in publicCss) {
            publicCss[i] = publicCss[i] + '?v=' + v
        }
        return publicCss
    }

    /**
     * 处理需要加载的js数组
     *
     * @param publicJs
     * @param version
     * @returns {*}
     */
    function get_public_js(publicJs, version) {
        if (typeof jsLibArr != 'undefined') {
            for (var i in jsLibArr) {
                publicJs.push(jsLibArr[i] + '.js?v=' + version)
            }
        }

        var scopes = check_cache_language(config.options.config.language, config.langScopes, config.options.config['use-cache'])
        var loads = {}

        // 判断是否需要载入语言包
        if (scopes.length > 0) {
            // publicJs.push('api/language?scopes=' + scopes.join(',') + '&lang=' + config.options.config.language)
            loads.language = scopes.join(',')
        }

        // var tplnames = check_cache_tpl(config.tplnames, config.options.config['use-cache'])

        // 判断是否需要载入模板
        // if (tplnames.length > 0) {
        //     loads.tpl = tplnames.join(',')
        // }

        var jsApi = get_load_data_js_api(loads)

        if (jsApi) {
            publicJs.unshift(jsApi)
        }

        return publicJs


        function get_load_data_js_api(data)
        {
            var api = 'api/data'

            var p = ''
            for (var i in data) {
                p += '&n[]=' + i + ':' + data[i]
            }

            if (p) {
                return api + '?' + p
            }
            return ''
        }
    }

    /**
     * 处理seajs配置
     *
     * @param config
     * @param version
     * @returns {*}
     */
    function get_sea_config(config, version)
    {
        for (var i in config.alias) {
            config.alias[i] = config.alias[i] + '.js?v=' + version
        }
        return config
    }


    function Router(config, callback)
    {
        var store = {
            routes: {},
            defaultController: 'Index',
            defaultAction: 'Index',
            controller: null,
            action: null,
            options: {
                hashbang: true
            },
            params: {}
        }

        store.routes = config.routes

        for (var i in store.routes) {
            page(store.routes[i], dispatch)
        }

        // 启动路由
        page(store.options)

        function dispatch(ctx, next) {
            store.controller = ctx.params.controller || store.defaultController
            store.action = ctx.params.action || store.defaultAction
            store.params = ctx.params

            callback(store.controller, store.action, store.params)
        }
    }

    /**
     * 缓存管理类
     *
     * @constructor
     */
    function Cache() {
        this.storage = window.localStorage || {}

        /**
         * token值，用于跟服务器的token进行对比，如两值不同则刷新缓存
         *
         * @type {null|int|string}
         */
        this.token = null

        /**
         * 缓存前缀
         *
         * @type {{general: string, timeout: string}}
         */
        this.prefix = {
            general: "$lxh_",
            timeout: "@lxh_"
        }

        /**
         * 设置token
         *
         * @param token
         */
        this.setToken = function (token) {
            this.token = token
        }

        /**
         * 缓存token
         *
         * @param token
         */
        this.saveToken = function (token) {
            this.set('$$token', token || this.token)
        }

        /**
         * 设置缓存
         *
         * @param key
         * @param val
         */
        this.set = function (key, val) {
            if (val instanceof Object) {
                val = JSON.stringify(val)
            }
            this.storage.setItem(this.prefix.general + key, val)
        }

        /**
         * 获取缓存
         *
         * @param key
         * @param def
         * @returns {*}
         */
        this.get = function (key, def) {
            if (! this.checkTokenValid(key)) {
                return def || null
            }
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

        /**
         * 检查是否应该更新缓存，是则返回false，否则返回true
         *
         * @param key
         * @returns {boolean}
         */
        this.checkTokenValid = function (key) {
            if (key == '$$token') {
                return true
            }
            if (this.token != this.get('$$token')) {
                this.clearAll()
                this.saveToken()
                return false
            }
            return true
        }

        /**
         * 清除所有过期的key
         *
         */
        this.clearPastDueKey = function () {
            for (var key in this.storage) {
                if (key.indexOf(this.prefix.timeout) == -1) {
                    continue
                }
                this.clearTimeout(key.replace(this.prefix.timeout, ""))
            }
        }

        /**
         * 检查key是否过期，是则清除并返回true，否则返回false
         *
         * @param key
         * @returns {boolean}
         */
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

        /**
         * 设置缓存时间，tiemeout毫秒后过期
         *
         * @param key
         * @param timeout
         */
        this.expire = function (key, timeout) {
            var d = new Date().getTime() + (parseInt(timeout))
            this.storage.setItem(this.prefix.timeout + key, d)
        }

        /**
         * 具体某一时间点过期
         *
         * @param key
         * @param timeout
         */
        this.expireAt = function (key, timeout) {
            this.storage.setItem(this.prefix.timeout + key, timeout)
        }

        /**
         * 清除所有缓存
         *
         */
        this.clearAll = function () {
            for (var i in this.storage) {
                delete this.storage[i]
            }
        }

        this.clearPastDueKey()
    }


})(window)
