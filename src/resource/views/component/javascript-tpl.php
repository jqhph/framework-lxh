<script type="text/html" id="modal-tpl">
<div id="{id}" class="modal fade {class}" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" >
    <div class="modal-dialog" style="width:{width};">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-action="modal-basic-close" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" >{title}</h4>
            </div>
            <div class="modal-body">{content}</div>
            <div class="modal-footer">
                @if {confirmBtn}
                <button data-action="confirm" type="button" class="btn {confirmBtnClass} waves-effect waves-light">{confirmBtnLabel}</button>
                @endif

                @if {closeBtn}
                <button data-action="close" type="button" class="btn btn-default waves-effect" data-dismiss="modal">{closeBtnLabel}</button>
                @endif

                @foreach {buttons} {row}
                <button data-action="{row.label}" type="button" class="btn {row.class} waves-effect" >{row.label}</button>
                @endforeach
                {footer}
            </div>
        </div>
    </div>
</div>
</script>