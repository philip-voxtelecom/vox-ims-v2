{if $meta.action=='create'}
    {assign var=title value='Create'}
{else}
    {assign var=title value='Update'}
{/if}
<div class="titleLabel">{$title} Product Group</div>
<div style="margin-left: 2em;">
    <br/>
    <form  action="#" name="productGroupCreateForm" id="productGroupCreateForm" method="post" >
        {if $meta.action=='update'}
            <input type="hidden" name="id" id="id" value="{$group.id}"/>
        {/if}
        <fieldset class="sectionwrap">
            <legend>Product Group Detail</legend>
            <div class="form-row">
                <div class="field-label"><label for="_save_name">Description</label>:</div>
                <div class="field-widget"><input value="{if isset($group)}{$group.name}{/if}" name="name" id="name" class="required" style="width:340px;" title="Enter group name"/></div>
            </div>
            {if $meta.action=="create"}
                <input type="hidden" name="status" id="status" value="active"/>
            {else}
                <div class="form-row">
                    <div class="field-label"><label for="status">Status</label>:</div>
                    <div class="field-widget">
                        <select name="status" id="status" class="validate-selection">
                            <option value="null">Please select</option>
                            <option value="active" {if $group.status=="active"}selected="selected"{/if}>Active</option>
                            <option value="inactive" {if $group.status=="inactive"}selected="selected"{/if}>Inactive</option>
                            {if !empty($meta.show_deleted)}
                                <option value="deleted" {if $group.status=="deleted"}selected="selected"{/if}>Deleted</option>
                            {/if}
                        </select>
                    </div>
                </div>
            {/if}
            <div class="center"><button type="button" class="detailButton"
                                        onclick="var valid = new Validation('productGroupCreateForm');
                                            if (!valid.validate()) return false;
                                            xajax_productGroupSubmit('{$meta.action}',xajax.getFormValues('productGroupCreateForm'));">
                    {$meta.action|capitalize} Product Group
                </button>
            </div>
            <br/><br/>
        </fieldset>
    </form>
    <div>The purpose of product groups is to create products within them that allow for upgrade/downgrade paths within the group for an existing account</div>

</div>