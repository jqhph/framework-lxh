<div class="form-group m-b-0">
    <label class="col-md-1 control-label"></label>
    <div>
        <button class="btn btn-primary waves-effect waves-light" type="submit">
            <?php echo trans_with_global('Save');?>
        </button>
        <a href="<?php echo empty($back) ? 'javascript:history.go(-1)' : $back;?>" type="reset" class="btn btn-default waves-effect waves-light m-l-5">
            <?php echo trans_with_global('Cancel');?>
        </a>
    </div>
</div>