<script type="text/html" id="kv-input-tpl">
    <div class="k-v-input {class}">
        <div class="form-group  col-md-2">
            <div class="col-sm-12">
                <div class="text"></div>
                <div class="input-group input-group-sm" style="width:100%">
                    <input style="{style}" type="text" name="{key}k" value="{key}" class="form-control input-sm ikey" placeholder="KEY">
                </div>
            </div>
        </div>
        <div class="form-group  col-md-10">
            <div class="col-sm-12">
                <div class="text"></div>
                <div class="input-group input-group-sm" style="width:100%">
                    <input type="text" name="{key}v" value='{value}' class="form-control input-sm ival" placeholder="VALUE">
                    <span class="input-group-addon remove-key-val"><b class="red">X</b></span>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/html" id="auto-input-tpl">
    <div class="box-body fields-group ">
        <div class="auto-input-container">
            <div class="form-group  col-md-12">
                <div class="col-sm-12">
                    <div class="text" style="color:#000;padding:10px 0px">原始HTTP请求数据</div>
                    <div class="input-group input-group-sm" style="width:100%">
                        <textarea style="width:100%" rows="5"  name="req-raw"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group  col-md-12">
                <div class="col-sm-12">
                    <div class="input-group parse-result-container" style="width:100%">
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div style="clear:both;margin:20px 0;padding:0 15px;"></div>
</script>

<script type="text/html" id="parse-table-tpl">
    <table class="table table-bordered">
        @if {title}
        <tr><td colspan="{@compare checkbox ?? 3 :: 2}" ><b class="blue">{title}</b></td></tr>
        @endif
        @foreach {list} {k} {v}
        <tr class="{class}">
            @if {checkbox}
            <td><input type="checkbox"
                @if {checkeds[k]}
                   checked="checked"
                @endif
                ></td>
            @endif
            <td>{k}</td><td>{v}</td>
        </tr>
        @endforeach
    </table>
</script>
