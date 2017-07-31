(function (o) {
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

    // 添加需要引入的js
    o.jsLibArr = []
    o.add_js = function (data) {
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
    o.add_css = function (data) {
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
})(window)