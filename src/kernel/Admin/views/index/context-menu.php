<div class="context-menu">
    <div class="context-menu-btn open-left"><i class="zmdi zmdi-menu" style="margin-left:3px;"></i></div>
    <?php if ($contextMenus) {?>
    <div class="context-menu-box">
        <?php foreach ($contextMenus as &$menu) {?>
        <div class="menu-list">
            <span><?php echo $menu['text']; ?></span>
            <?php if (!empty($menu['children'])) {
                foreach ($menu['children'] as &$child) {
                ?>
            <div class="child">
                <span><?php echo $child?></span>
            </div>
            <?php }} ?>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>
<script>
$('.context-menu').hover(function () {
    setTimeout(function () {
        var $box = $('.context-menu-box');
        $box.css('top', '-' + ($box.height()) + 'px');
    }, 10);
});
</script>