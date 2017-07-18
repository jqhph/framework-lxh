// 添加需要引入的js
window.jsLibArr = {}
function add_js(data) {
    if (typeof data == 'string') {
        jsLibArr[data] = data
    } else {
        for (var i in data) {
            jsLibArr[i] = data[i]
        }
    }
}

// 添加需要引入的css
window.cssLibArr = []
function add_css(data) {
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
function to_under_score(str) {
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
function parse_view_name(c, a) {
    return 'module/' + to_under_score(c) + '/' + to_under_score(a)

}