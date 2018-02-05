<script>window.formRules = [];
var SPAID = '<?php echo Lxh\Admin\Admin::SPAID();?>', __CONTROLLER__ = '<?php echo __CONTROLLER__?>',
    __ACTION__ = '<?php echo __ACTION__?>', __MODULE__ = '<?php echo __MODULE__;?>';</script>
<div class="container" id="<?php echo Lxh\Admin\Admin::SPAID();?>">
<div class="content-wrapper">
<?php if ($header || $description) {?>
<section class="content-header"><h1><?php echo $header; ?><small> &nbsp;<?php echo $description;?></small></h1></section>
<?php } else {
    echo '<div style="height:10px;"></div>';
}?>
<section class="content"><?php echo $content;?></section>
</div>
</div>
<script>
<?php
    echo $js;
    echo $css;
    echo $asyncJs;
?>; __then__(function(){<?php echo $script?>});
</script>

