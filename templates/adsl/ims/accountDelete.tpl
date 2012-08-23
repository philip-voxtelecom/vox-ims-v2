<div class="titleLabel">Delete Account</div>
<div style="margin-left: 2em;">
    <form action="#" name="accountDeleteForm" id="accountDeleteForm" method="post">
        <table class="detailTable">
            <tr >
                <td class="label">Account Reference</td><td class="detail">{$viewobject->data->description}</td>
            </tr>
            <tr onclick="togglePrint(this);">
                <td class="label">Product</td><td class="detail">{$viewobject->data->product}</td>
            </tr>
            <tr onclick="togglePrint(this);">
                <td class="label">Username</td><td class="detail">{$viewobject->data->username}</td>
            </tr>
            <tr onclick="togglePrint(this);">
                <td class="label">Status</td><td class="detail">{$viewobject->data->status|capitalize}</td>
            </tr>
        </table>
        <div class="form-row">
            <div class="field-label">
                <input type="checkbox" name="confirm" id="confirm" class="validate-one-required" /><strong>I understand deleting this account is an irreversable action </strong>
            </div>
            <div id="advice-validate-one-required-confirm" class="validation-advice" style="display: none">Please check the box above to confirm action</div>

        </div>

        <input type="hidden" name="id" id="id" value="{$viewobject->data->id}"/>
        <input type="hidden" name="offset" id="offset" value="{$offset}"/>
        <input type="hidden" name="search" id="search" value="{$search}"/>
        <input type="hidden" name="limit" id="limit" value="{$limit}"/>
        <button type="button" class="detailButton"
                onclick="
                    if (! Validation.validate('confirm'))
                        return; 
                    xajax_accountSubmit('delete',xajax.getFormValues('accountDeleteForm'));">
            Delete
        </button>

    </form>
</div>

</div>
