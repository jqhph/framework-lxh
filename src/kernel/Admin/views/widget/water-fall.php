<style>
#<?php echo \Lxh\Admin\Admin::SPAID()?> .tiles li{width:<?php echo $options['itemWidth']?>px;}
</style>
<div class="wtf-wrapper">
    <?php if ($filters) {?>
   <div class="btn-group wtf-filters" style="margin-bottom:20px;">
       <?php foreach ($filters as &$filter) {?>
       <a class="btn btn-default btn-sm" data-filter="<?php echo $filter['value'];?>"><span><?php echo $filter['label'];?></span></a>
       <?php }?>
   </div>
    <?php } ?>
    <ul class="tiles">
        <?php foreach ($items as &$item) { ?>
        <li class="item-card" <?php echo $item['filters'] ? 'data-filter-class=\'' . json_encode($item['filters']) . '\'' : '';?>><?php echo $item['content'];?></li>
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
            filters = $spa.find('.wtf-filters a');

        handler.wookmark(options);
        setTimeout(function () {
            handler.wookmark(options);
        }, 1500);
        var onClickFilter = function(event) {
            var item = $(event.currentTarget),
                activeFilters = [];
            item.toggleClass('btn-default');
            item.toggleClass('btn-primary');
            filters.filter('.btn-primary').each(function() {
                activeFilters.push($(this).data('filter'));
            });

            handler.wookmarkInstance.filter(activeFilters, '<?php echo $filterMode;?>');
        };
        filters.click(onClickFilter);
    });
</script>
