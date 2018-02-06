<style>
#<?php echo \Lxh\Admin\Admin::SPAID()?> .tiles li{width:<?php echo $options['itemWidth']?>px;}
</style>
<div class="wtf-wrapper">
<!--    <ol class="wtf-filters">-->
<!--        <li data-filter="amsterdam">Amsterdam</li>-->
<!--    </ol>-->
    <ul class="tiles">
        <?php foreach ($cards as &$card) { ?>
        <li data-filter-class='<?php echo json_encode($card['filters'])?>'><?php echo $card['content'];?></li>
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
        }, 1200);

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
