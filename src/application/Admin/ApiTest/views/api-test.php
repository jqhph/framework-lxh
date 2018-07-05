<style>
    .remove-key-val{cursor:pointer}
</style>
<form class="form-horizontal request-form" onsubmit="return false" accept-charset="UTF-8" pjax-container="1" >
    <div class="box-body fields-group">
        <div class="form-group line col-md-12">
            <div class="col-sm-12">
                <div class="text">URL</div>
                <div class="input-group" style="width:100%">
                    <input type="text" name="req-url" value="" class="form-control username" placeholder="URL">
                </div>
            </div>
        </div>

        <div class="form-group line col-md-12">
            <div class="col-sm-12">
                <div class="text">METHOD</div>
                <div class="input-group" style="width:100%">
                    <select class="form-shadow input-sm col-md-12" name="req-method" style="margin-right:10px;background:#fff;">
                        <option value="GET">GET</option>
                        <option value="POST" selected="">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                        <option value="OPTION">OPTION</option>
                        <option value="HEAD">HEAD</option>
                    </select>

                </div>
            </div>
        </div>

        <div class="form-group col-md-12" style="padding: 0 5px">
            <div class="col-sm-12">
                <div class="text" style="color:#000">QUERY</div>
                <div class="input-group"></div>
            </div>
        </div>

        <div class="req-box"></div>

    </div>
    <div style="clear: both;margin-top:15px;">
        <input type="hidden" name="_token" value="">
        <div class="btn-group "><button type="submit" class="add-key-val btn btn-success waves-effect pull-right">新增参数</button></div>

        <div class="btn-group "><button type="submit" class="auto-input btn btn-primary waves-effect pull-right">解析HTTP请求信息</button></div>

        <div class="btn-group "><button type="submit" class="set-header btn btn-primary waves-effect pull-right">设置HEADER头信息</button></div>

        <div class="btn-group "><button type="submit" class="reset-def btn btn-primary waves-effect pull-right">重置默认参数</button></div>

        <div class="btn-group "><button type="submit" class="submit btn btn-primary waves-effect pull-right">提交</button></div>
        &nbsp;<div class="btn-group"><button type="reset" class="btn btn-default waves-effect pull-right">重置&nbsp; <i class="fa fa-undo"></i></button></div>&nbsp;
    </div>
</form>

<?php
echo view('api-test::tpl')->render();
echo view('api-test::script')->render();
?>


