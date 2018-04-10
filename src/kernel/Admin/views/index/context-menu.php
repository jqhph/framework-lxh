<div class="context-menu">
    <div class="context-menu-btn open-left"><i class="zmdi zmdi-menu" style="margin-left:3px;"></i></div>
    <?php if ($contextMenus) {?>
    <div class="context-menu-box">
        <?php foreach ($contextMenus as &$menu) {?>
        <div class="menu-list">
            <span><?php echo $menu['text']; ?></span>
            <?php if (!empty($menu['children'])) {
                echo '<div class="child">';
                foreach ($menu['children'] as &$child) {
                ?>
                <span><?php echo $child?></span>
            <?php }
                echo '</div>';
            } ?>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>
<script>
(function () {
    $('.context-menu').hover(function () {
        setTimeout(function () {
            var $box = $('.context-menu-box');
            $box.css('top', '-' + ($box.height()) + 'px');
        }, 10);
    });
})()
</script>