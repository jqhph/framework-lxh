<script>window.formRules = []</script>
<form <?php echo $attributes ?>>
    <div class="box-body fields-group">
        <?php foreach($fields as $field): ?>
        <?php echo $field->render();
            echo $field->formatRules();
            ?>
        <?php endforeach; ?>
    </div>
    <div class="box-footer">
        <input type="hidden" name="_token" value="<?php ?>"><div class="col-sm-2"></div>
        <?php if ($formOptions['enableReset']) {?>
        <div class="col-sm-2"><div class="btn-group pull-left"><button type="reset" class="btn btn-default pull-right"><?php echo trans('Reset')?></button></div></div>
        <?php } ?>
        <?php if ($formOptions['enableSubmit']) {?>
        <div class="col-sm-4"><div class="btn-group pull-right"><button type="submit" class="btn btn-primary pull-right"><?php echo trans('Submit')?></button></div></div>
        <?php } ?>
        <div style="clear: both;height:5px;"></div>
    </div>
</form>
<script type="text/javascript">
<?php
foreach ($asyncJs as &$js) {
    echo "add_js('$js');";
}
?></script>
