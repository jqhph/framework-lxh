/**
 * Created by Jqh on 2017/7/19.
 */
define([], function () {
    $(document).ready(function() {
        // 计算进度条百分比
        function compute_progressbar_percent(total, current) {
            var $percent = ((parseInt(current) + 1) / total) * 100;
            console.log(total, current, $percent)
            $('#progressbarwizard').find('.bar').css({width:$percent+'%'})
        }

        // 当前tab显示位置
        var current = 0,
            // tab数量
            total = 3

        compute_progressbar_percent(total, current)

        $('a[data-action="prev-tab"]').click(function (e) {
            if (current < 1) return

            current --;

            compute_progressbar_percent(total, current)

            e.preventDefault()

            $('a[data-action="tab"]').eq(current).tab('show')

        })

        $('a[data-action="next-tab"]').click(function (e) {
            if (current > total - 2) return

            current ++;

            compute_progressbar_percent(total, current)

            e.preventDefault()
            $('a[data-action="tab"]').eq(current).tab('show')


        })

    });
})
