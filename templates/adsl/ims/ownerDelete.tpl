<div class="titleLabel">Delete Reseller</div>
<div style="margin-left: 2em;">
    <form action="#" name="resellerDeleteForm" id="resellerDeleteForm" method="post">
        <table class="detailTable">
            <tr >
                <td class="label">Reseller</td><td class="detail">{$reseller.name}</td>
            </tr>
            <tr onclick="togglePrint(this);">
                <td class="label">Status</td><td class="detail">{$reseller.status|capitalize}</td>
            </tr>
        </table>
        <div class="form-row">
            <div class="field-label">
                <strong>I understand deleting is an irreversable action. This reseller will no longer exist </strong><br/><br/>
                <strong>Check box to confirm</strong><input type="checkbox" name="confirm" id="confirm" class="validate-one-required" /> 
            </div>
            <div id="advice-validate-one-required-confirm" class="validation-advice" style="display: none">Please check the box above to confirm action</div>
        </div>

        <input type="hidden" name="id" id="id" value="{$reseller.id}"/>
        <button type="button" class="detailButton"
                onclick="
                    if (! Validation.validate('confirm'))
                        return; 
                    xajax_ownerSubmit('delete',xajax.getFormValues('resellerDeleteForm'));">
            Delete
        </button>

    </form>
</div>

</div>