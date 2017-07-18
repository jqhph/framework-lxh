<div class="form-group">
    <label class="col-md-2 control-label"><?php echo trans($label, 'fields'); ?></label>
    <div class="col-md-8">
        <select name="<?php echo $name;?>" class="form-control">
            <option value="0" ><?php echo trans_with_global('Top level');?></option>
            <?php foreach ($list as & $v) {
                if ($v['id'] == $id) continue;
                ?>

                <option <?php if ($value == $v['id']) echo 'selected';?> value="<?php echo $v['id'];?>">
                    <?php echo trans_with_global($v['name'], 'menu');?></option>

                <?php
                    $secondSubsCount = count($v['subs']) - 1;
                if (! empty($v['subs'])) {
                    foreach ($v['subs'] as $k => & $s) {
                        if ($s['id'] == $id) continue;
                    ?>
                    <option <?php if ($value == $s['id']) echo 'selected';?>  value="<?php echo $s['id'];?>">
                        &nbsp;&nbsp;<?php if ($k == $secondSubsCount) {echo '└─ ';} else {echo '├─ ';}
                        echo trans_with_global($s['name'], 'menu');?></option>
                <?php }

                    $thridSubsCount = count($s['subs']) - 1;
                    if (! empty($s['subs'])) {
                        foreach ($s['subs'] as $i => & $t) {
                            if ($t['id'] == $id) continue; ?>
                        <option <?php if ($value == $t['id']) echo 'selected';?>  value="<?php echo $t['id'];?>">
                            &nbsp;&nbsp;&nbsp;&nbsp;<?php  if ($i == $thridSubsCount) {echo '└─ ';} else {echo '├─ ';}
                            echo trans_with_global($t['name'], 'menu');?></option>

                <?php }
                    }

                }?>
            <?php } ?>
        </select>
    </div>
</div>