<div class="filter-input col-sm-<?php echo $width['field']?> " >
    <div class="form-inline ">
        <div class="form-group">
            <div class="input-daterange input-group  date-search-box input-group-sm" >
                <span class="input-group-addon"><b><?php echo $label ?></b></span>
                <input value="<?php echo $start;?>" type="text" class="form-control <?php echo $name;?>" name="<?php echo $startName;?>" placeholder="<?php echo trans_with_global('start')?>">
                <span class="input-group-addon btn-custom btn-trans b-0 text-white">to</span>
                <input value="<?php echo $end;?>" type="text" class="form-control <?php echo $name;?>" name="<?php echo $endName;?>"  placeholder="<?php echo trans_with_global('end')?>">
            </div>
        </div>
    </div>
</div>