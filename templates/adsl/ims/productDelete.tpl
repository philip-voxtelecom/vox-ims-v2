<div class="titleLabel">Delete Product</div>
<div style="margin-left: 2em;">
    <form action="#" name="productDeleteForm" id="productDeleteForm" method="post">
        <table class="detailTable">
            <tr >
                <td class="label">Product</td><td class="detail">{$product.name}</td>
            </tr>
            <tr onclick="togglePrint(this);">
                <td class="label">Status</td><td class="detail">{$product.status|capitalize}</td>
            </tr>
        </table>
        <div class="form-row">
            <div class="field-label">
                <strong>I understand deleting is an irreversable action. This product will no longer be available for provisioning </strong><br/><br/>
                <strong>Check box to confirm</strong><input type="checkbox" name="confirm" id="confirm" class="validate-one-required" /> 
            </div>
            <div id="advice-validate-one-required-confirm" class="validation-advice" style="display: none">Please check the box above to confirm action</div>
        </div>

        <input type="hidden" name="id" id="id" value="{$product.id}"/>
        <button type="button" class="detailButton"
                onclick="
                    if (! Validation.validate('confirm'))
                        return; 
                    xajax_productSubmit('delete',xajax.getFormValues('productDeleteForm'));">
            Delete
        </button>

    </form>
</div>

</div>