<div class="form-group line col-md-<?php echo $width['layout']?>">
    <div class="col-sm-<?php echo $width['field'] ?>">
        <div class="text"><?php echo $prepend ? $prepend . '&nbsp; ' : ''; ?><?php echo $label ?></div>
        <div class="input-group" <?php if (!$append) {echo 'style="width:100%"';}?>>
            <input class="form-control " style="display:inline-block;width:44%;max-width:280px" type="float" id="<?php echo $id['lat'] ?>" name="<?php echo $name['lat'] ?>" value="<?php echo isset($value['lat']) ? $value['lat'] : $defaultValue['lat']?>"  placeholder="<?php echo trans('latitude');?>" />
            <input class="form-control " style="margin-left:12px;display:inline-block;width:44%;max-width:280px" type="float" id="<?php echo $id['lng'] ?>" name="<?php echo $name['lng'] ?>" value="<?php echo isset($value['lng']) ? $value['lng'] : $defaultValue['lng']?>"   placeholder="<?php echo trans('longitude');?>" />
            <a class='btn btn-primary search-map' style="margin-top:5.5px;"><i class='fa fa-search'></i></a>
            <?php if ($help) {
                echo view('admin::form.help-block', ['help' => &$help])->render();
            }?>
        </div>
        <div id="map_<?php echo $id['lat'].$id['lng'] ?>" style="top:20px;width:100%;height:<?php echo $height?>"></div>
    </div>
</div>
