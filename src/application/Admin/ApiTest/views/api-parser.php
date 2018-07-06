<form class="form-horizontal parse-url-form" onsubmit="return false" accept-charset="UTF-8" pjax-container="1" enctype="multipart/form-data">
    <div class="box-body fields-group">
        <div class="form-group line col-md-12">
            <div class="col-sm-12">
                <div class="text">API</div>
                <div class="input-group" style="width:100%">
                    <input type="text" name="parse-url" value="" class="form-control " placeholder="URL">
                </div>
            </div>
        </div>
        <div class="form-group line col-md-12">
            <div class="col-sm-12" id="showparams">
                参数
            </div>
        </div>
    </div>
    <div>
        <input type="hidden" name="_token" value="">
        <div class="btn-group "><button type="submit" class="save-def btn btn-primary waves-effect pull-right">缓存默认请求参数</button></div>
        &nbsp;<div class="btn-group"><button type="reset" class="btn btn-default waves-effect pull-right">重置&nbsp; <i class="fa fa-undo"></i></button></div>&nbsp;
    </div>
</form>
<script>
    __then__(function () {
        var current = LXHSTORE.IFRAME.current(),
            $form = {},
            $showparams = $('#showparams'),
            cache = LXHSTORE.cache,
            cacheKey = 'api-default-params';
        var urlQueryRowClass = '.url-query-row',// 参数解析行
            defaultParams = cache.get(cacheKey, {});

        $form.f = $('.parse-url-form');
        $form.submit = $('.save-def');
        $form.parseUrl = $('input[name="parse-url"]');
        $form.parseUrl.on('keyup', parse_url);
        // $form.parseUrl.on('focus', parse_url);

        // 重置表单
        $form.f.on('reset', function () {
            $showparams.html('');
        });

        $form.submit.click(function () {
            // 缓存defaultParams
            cache.set(cacheKey, defaultParams);
            notify_success('缓存成功');
            return false;
        });

        /**
         * 解析url
         */
        function parse_url(e) {
            var val = $form.parseUrl.val(),
                content = {
                    url: val,
                    data: get_query_to_obj(val)
                };

            if (!val) return;

            // 显示参数
            $showparams.html(show_table(content.data));
            // 表格行点击事件
            $(urlQueryRowClass).on('click', function (e) {
                var _t = $(e.currentTarget);
                var box = _t.find('input[type="checkbox"]');
                defaultParams = cache.get(cacheKey, {});
                if (typeof e.toElement == undefined || e.toElement.outerHTML.indexOf('checkbox') == -1) {
                    box.prop("checked") ? box.prop("checked", false) : box.prop("checked", true);
                }
                var key = _t.find('.k').text(), val = _t.find('.v').text();
                // 重新判断一次
                if (box.prop("checked")) {
                    // 选中
                    defaultParams[key] = val;
                } else {
                    // 未选中
                    delete defaultParams[key];
                }
            });

            LXHSTORE.IFRAME.height(current);

            console.log('解析URL ', content);
        }

        /**
         * 把对象用表格的形式展示出来
         *
         */
        function show_table(data) {
            var html = '<table class="table table-bordered">';

            for (var i in data) {
                html += '<tr class="url-query-row url-'+i+'"><td><input type="checkbox"></td><td class="k">' + i + '</td><td class="v">' + data[i] + '</td><tr>';
            }

            return html + '</table>';
        }

        /**
         * 把query参数转化为对象
         *
         * @param url
         * @returns {*}
         */
        function get_query_to_obj(url) {
            if (!url) return '';

            try {
                url = decodeURI(decodeURIComponent(url));
            } catch(e) {
                notify_error('URL数据格式错误，解析失败！请检查复制信息是否完整。', 4500);
                return;
            }

            var result = {};
            var query = url;

            if (url.indexOf('?') != -1 && typeof url.split("?")[1] != undefined) {
                query = url.split("?")[1];
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

        // 注册到全局变量中
        window.parse_url = parse_url;
        window.get_query_to_obj = get_query_to_obj;
    });
</script>
