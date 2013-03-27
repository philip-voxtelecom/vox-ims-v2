<div class="titleLabel">Change Status</div>
<div style="margin-left: 2em;">
    <p><strong>Please select account status</strong></p>
    <form action="#" name="accountStatusUpdateForm" id="accountStatusUpdateForm" method="post">
        {assign var='selected' value=$viewobject->data->status|lower}
        <select name="_save_status">
            {foreach from=$viewobject->options->option key=options item=option}
                <option {if $selected==$option->value|lower}selected{/if} value="{$option->value|lower}">{$option->name|capitalize}</option>
            {/foreach}
        </select>
        {assign var='id' value=$viewobject->data->id}
        <br/>
        <input type="hidden" name="id" id="id" value="{$id}"/>
        <input type="hidden" name="offset" id="offset" value="{$offset}"/>
        <input type="hidden" name="search" id="search" value="{$search}"/>
        <input type="hidden" name="limit" id="limit" value="{$limit}"/>

    </form>
</div>
<button type="button" class="detailButton"
        onclick="xajax_accountSubmit('update',xajax.getFormValues('accountStatusUpdateForm'));">
    Apply Status
</button>
