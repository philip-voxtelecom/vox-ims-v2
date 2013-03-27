<div class="titleLabel">Delete Product Group</div>
<div style="margin-left: 2em;">
    <form action="#" name="productGroupDeleteForm" id="productGroupDeleteForm" method="post">
        <table class="detailTable">
            <tr >
                <td class="label">Product Group</td><td class="detail">{$group.name}</td>
            </tr>
            <tr onclick="togglePrint(this);">
                <td class="label">Products</td>
                <td class="detail">
                    {foreach item=i key=k from=$products}
                        {$i.name}<br/>
                    {/foreach}
                </td>
            </tr>
            <tr onclick="togglePrint(this);">
                <td class="label">Status</td><td class="detail">{$group.status|capitalize}</td>
            </tr>
        </table>
        <div class="form-row">
            <div class="field-label">
                <strong>I understand deleting is an irreversable action. Products in this group will no longer be available for provisioning </strong><br/><br/>
                <strong>Check box to confirm</strong><input type="checkbox" name="confirm" id="confirm" class="validate-one-required" /> 
            </div>
            <div id="advice-validate-one-required-confirm" class="validation-advice" style="display: none">Please check the box above to confirm action</div>
        </div>

        <input type="hidden" name="id" id="id" value="{$group.id}"/>
        <button type="button" class="detailButton"
                onclick="
                    if (! Validation.validate('confirm'))
                        return; 
                    xajax_productGroupSubmit('delete',xajax.getFormValues('productGroupDeleteForm'));">
            Delete
        </button>

    </form>
</div>

</div>