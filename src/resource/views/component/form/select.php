<div class="form-group <?php //echo !$errors->has($errorKey) ?: 'has-error' ?>">
    <label for="<?php echo $id ?>" class="col-sm-<?php echo $width['label'] ?> control-label"><?php echo $label ?></label>
    <div class="col-sm-<?php echo $width['field'] ?>">
<!--        @include('admin::form.error')-->

        <select class="form-control <?php echo $class ?>" style="width: 100%;" name="<?php echo $name ?>" <?php echo $attributes ?> >
            <?php foreach($options as &$option): ?>
            <option value="<?php echo $option['value'] ?>" <?php echo  $option['value'] == $value ?'selected':''  ?>><?php echo $option['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($help) {
            echo view('admin::form.help-block')->render();
        }?>
    </div>
</div>
