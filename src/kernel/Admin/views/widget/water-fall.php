<style>
#<?php echo \Lxh\Admin\Admin::SPAID()?> .tiles li{width:<?php echo $options['itemWidth']?>px;}
</style>
<div class="wtf-wrapper">
<!--    <ol class="wtf-filters">-->
<!--        <li data-filter="amsterdam">Amsterdam</li>-->
<!--    </ol>-->
    <ul class="tiles">
        <?php foreach ($items as &$item) { ?>
        <li class="item-card" <?php echo $item['filters'] ? 'data-filter-class="' . json_encode($item['filters']) . '"' : '';?>><?php echo $item['content'];?></li>
        <?php } ?>
    </ul>
</div>
<script>
    __then__(function (){
        var $spa = $('#<?php echo Lxh\Admin\Admin::SPAID()?>');

        // Prepare layout options.
        var options = $.extend({
            container: $spa.find('.wtf-wrapper .tiles')
        }, <?php echo json_encode($options)?>);

        var handler = $spa.find('.tiles li'),
            filters = $spa.find('.wtf-filter');

        handler.wookmark(options);
        setTimeout(function () {
            handler.wookmark(options);
        }, 1500);

        var onClickFilter = function(event) {
            var item = $(event.currentTarget),
                activeFilters = [];
            item.toggleClass('active');
            filters.filter('.active').each(function() {
                activeFilters.push($(this).data('filter'));
            });

            handler.wookmarkInstance.filter(activeFilters, 'or');
        };
        filters.click(onClickFilter);
    });
</script>
