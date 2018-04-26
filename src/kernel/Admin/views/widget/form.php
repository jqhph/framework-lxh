<script>window.formRules = []</script>
<?php if (! $content || ! $multiples) {?>
<form <?php echo $attributes ?>>
<?php } elseif ($content) {
    $content->prepend("<form $attributes>");
    $content->append("</form>");
}?>
    <div class="box-body fields-group">
        <?php foreach($fields as $field): ?>
        <?php echo $field->render();
            echo $field->formatRules();
            ?>
        <?php endforeach; ?>
    </div>
    <div>
        <?php if ($id) { ?>
        <input type="hidden"  name="__id__" value="<?php echo $id;?>" />
        <?php } ?>
        <input type="hidden" name="_token" value="<?php ?>">
        <?php if ($formOptions['enableSubmit']) {?>
            <div class="btn-group "><button type="submit" class="btn btn-primary waves-effect pull-right"><?php echo $formOptions['submitBtnLabel'] ?: trans('Submit')?></button></div>
        <?php } ?>

        <?php if ($formOptions['enableReset']) {?>
        &nbsp;<div class="btn-group"><button type="reset" class="btn btn-default waves-effect pull-right"><?php echo $formOptions['resetBtnLabel'] ?: trans('Reset')?>&nbsp; <i class="fa fa-undo"></i></button></div>&nbsp;
        <?php } ?>
        <?php if (!empty($append)) echo $append;?>

    </div>
<?php if (! $content || ! $multiples) {?>
</form>
<?php }?>