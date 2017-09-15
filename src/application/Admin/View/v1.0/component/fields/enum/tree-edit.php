<?php
$labelCategory = empty($labelCategory) ? 'menu' : $labelCategory;

?>

<div class="form-group">
    <?php if (! empty($label)) {?>
    <label class="col-md-2 control-label"><?php echo trans($label, 'fields'); ?></label>
    <?php } ?>
    <div class="col-md-8">
        <select name="<?php echo $name;?>" class="form-control">
            <?php foreach ($list as & $v) {
                if ($v['id'] == $id && empty($v['required'])) continue;
                ?>

                <option <?php if ($value == $v['id']) echo 'selected';?> value="<?php echo $v['id'];?>">
                    <?php echo trans($v['name'], $labelCategory);?></option>

                <?php
                if (! empty($v['subs'])) {
                    $secondSubsCount = count($v['subs']) - 1;
                    foreach ($v['subs'] as $k => & $s) {
                        if ($s['id'] == $id) continue;
                    ?>
                    <option <?php if ($value == $s['id']) echo 'selected';?>  value="<?php echo $s['id'];?>">
                        &nbsp;&nbsp;<?php if ($k == $secondSubsCount) {echo '└─ ';} else {echo '├─ ';}
                        echo trans($s['name'], $labelCategory);?></option>
                <?php }

                    if (! empty($s['subs'])) {
                        $thridSubsCount = count($s['subs']) - 1;
                        foreach ($s['subs'] as $i => & $t) {
                            if ($t['id'] == $id) continue; ?>
                        <option <?php if ($value == $t['id']) echo 'selected';?>  value="<?php echo $t['id'];?>">
                            &nbsp;&nbsp;&nbsp;&nbsp;<?php  if ($i == $thridSubsCount) {echo '└─ ';} else {echo '├─ ';}
                            echo trans($t['name'], $labelCategory);?></option>

                <?php }
                    }

                }?>
            <?php } ?>
        </select>
    </div>
</div>