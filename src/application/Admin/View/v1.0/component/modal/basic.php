<script type="text/html" id="modal-basic">
    <div class="modal fade {class}" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" >
        <div class="modal-dialog" style="width:55%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-action="modal-basic-close" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" >{title}</h4>
                </div>
                <div class="modal-body">{content}</div>
                <div class="modal-footer">
                    @if {closeButton}
                        <button data-action="modal-basic-close" type="button" class="btn btn-default waves-effect" data-dismiss="modal">{closeButtonLabel}</button>
                    @endif

                    @if {saveButton}
                        <button data-action="modal-basic-save" type="button" class="btn {saveButtonClass} waves-effect waves-light">{saveButtonLabel}</button>
                    @endif

                    @foreach {buttons} {row}
                        <button data-action="{row.label}" type="button" class="btn {row.class} waves-effect" >{row.label}</button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</script>