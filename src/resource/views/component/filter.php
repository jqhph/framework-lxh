<form <?php echo $attributes ?>>
    <div class="box-body fields-group">
        <?php foreach($fields as $field): ?>
            <?php echo $field->render(); ?>
        <?php endforeach; ?>
        <div style="clear: both;"></div>
    </div>
    <div class="box-footer">
        <input type="hidden" name="_token" value="<?php ?>">
        <div class="col-sm-12">
            <div class="btn-group pull-left" >
                <button type="submit" class="btn btn-sm btn-primary pull-right"><?php echo trans('Search')?>&nbsp;&nbsp;&nbsp;<i class="fa fa-search"></i></button>
            </div>
            <?php if ($filterOptions['enableReset']) {?>
                <div class="btn-group pull-left" style="margin-left:15px;"><button type="reset" class="btn btn-sm btn-default pull-right">
                        <?php echo trans('Reset')?>&nbsp;&nbsp;&nbsp;<i class="fa fa-undo"></i></button></div>
            <?php } ?>
        </div>
        <div style="clear: both;height:5px;"></div>
    </div>
</form>