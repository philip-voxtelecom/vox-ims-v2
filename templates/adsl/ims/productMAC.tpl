{if $meta.action=='create'}
    {assign var=title value='Create'}
{else}
    {assign var=title value='Update'}
{/if}
<div class="titleLabel">{$title} Product</div>
<div style="margin-left: 2em;">
    <br/>
    <form  action="#" name="productMACForm" id="productMACForm" method="post" >
        {if $meta.action=='create'}
            <input type="hidden" name="groupid" id="groupid" value="{$groupid}"/>
        {else}
            <input type="hidden" name="groupid" id="groupid" value="{$product.groupid}"/>
            <input type="hidden" name="id" id="id" value="{$product.id}"/>
        {/if}

        <fieldset class="sectionwrap">
            <legend>Product Detail</legend>
            <div class="form-row">
                <div class="field-label"><label for="name">Name</label>:</div>
                <div class="field-widget"><input value="{if isset($product)}{$product.name}{/if}" name="name" id="name" {if $meta.action != 'update'}class="required"{/if} style="width:340px;" title="Enter product name" onchange="xajax.$('name').innerHTML=this.value;"/></div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="description">Description</label>:</div>
                <div class="field-widget"><input value="{if isset($product)}{$product.description}{/if}" name="description" id="description" {if $meta.action != 'update'}class="required"{/if} style="width:340px;" title="Enter product description" onchange="xajax.$('description').innerHTML=this.value;"/></div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="simultaneousUse">Number of Simultaneous Users</label>:</div>
                <div class="field-widget"><input value="{if isset($product)}{$product.simultaneousUse}{/if}" name="simultaneousUse" id="simultaneousUse" {if $meta.action != 'update'}class="required validate-number"{/if} style="width:340px;" title="Enter number of simultaneous connections" onchange="xajax.$('simultaneousUse').innerHTML=this.value;"/></div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="accesstype">Access Type</label>:</div>
                <div class="field-widget">
                    <div class="field-widget">
                        <select name="accesstype" id="accesstype" class="validate-selection" title="Select access type" onchange="if (xajax.$('accesstype').value != 'null')">
                            <option value="null">Please select</option>
                            {section name=accesstype loop=$accesstypes}
                                {if $accesstypes[accesstype].status|lower == 'active'}
                                    <option value="{$accesstypes[accesstype].uid}" {if $product.accesstype==$accesstypes[accesstype].uid}selected="selected"{/if}>{$accesstypes[accesstype].name}</option>
                                {/if}
                            {/section}
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="accessqos">Access Quality</label>:</div>
                <div class="field-widget">
                    <select name="accessqos" id="accessqos" class="validate-selection" title="Select access quality" onchange="if (xajax.$('accessqos').value != 'null')">
                        <option value="null">Please select</option>
                        {section name=accessqos loop=$accessqoss}
                            {if $accessqoss[accessqos].status|lower == 'active'}
                                <option value="{$accessqoss[accessqos].uid}" {if $product.accessqos==$accessqoss[accessqos].uid}selected="selected"{/if}>{$accessqoss[accessqos].name}</option>
                            {/if}
                        {/section}
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="accessqos">Realm</label>:</div>
                <div class="field-widget">
                    <select name="realm" id="realm" class="validate-selection" onchange="if (xajax.$('realm').value != 'null')">
                        <option value="null">Please select</option>
                        {section name=realm loop=$realms}
                            {if $realms[realm].status|lower == 'active'}
                                <option value="{$realms[realm].realm}" {if $product.realm==$realms[realm].realm}selected="selected"{/if}>{$realms[realm].realm}</option>
                            {/if}
                        {/section}
                    </select>
                </div>    
            </div>
            <div class="form-row">
                <div class="field-label"><label for="isTopupable">Enable topup on product</label>:</div>
                <div class="field-widget">
                    <select name="isTopupable" id="isTopupable" class="validate-selection">
                        <option value="null">Please select</option>
                        <option value="1" {if $product.isTopupable=='1'}selected="selected"{/if}>Yes</option>
                        <option value="0" {if $product.isTopupable=='0'}selected="selected"{/if}>No</option>
                    </select>
                </div>    
            </div>
            <div class="form-row">
                <div class="field-label"><label for="rolloverDay">Day of month for quota rollover</label>:</div>
                <div class="field-widget">
                    <select name="rolloverDay" id="rolloverDay" class="validate-selection">
                        <option value="null">Please select</option>
                        {section name=foo start=1 loop=32 step=1}
                            <option value="{$smarty.section.foo.index}" {if $product.rolloverDay==$smarty.section.foo.index}selected="selected"{/if}>{$smarty.section.foo.index}</option>
                        {/section}
                    </select>
                </div>    
            </div>
            <div class="form-row">
                <div class="field-label"><label for="availablequotawheels">Available Quota Wheels</label>:</div>
                <div class="field-widget">
                    <select style="width: 80%;" multiple name="availablequotawheels" id="availablequotawheels">
                        {section name=quotawheel loop=$availablequotawheels}
                            {if $availablequotawheels[quotawheel].status|lower == 'active'}
                                <option id="{$availablequotawheels[quotawheel].uid}" value="{$availablequotawheels[quotawheel].uid}" >{$availablequotawheels[quotawheel].name} - {$availablequotawheels[quotawheel].plan}</option>
                            {/if}
                        {/section}
                    </select>
                    <a href="#" id="add" onclick="
                        jQuery('#availablequotawheels option:selected').clone().appendTo('#quotawheels');
                        var foundedinputs = [];
                        jQuery('select[name=quotawheels] option').each(function() {ldelim}
                        if(jQuery.inArray(this.value, foundedinputs) != -1) $(this).remove();
                        foundedinputs.push(this.value);
                       {rdelim});"
                       >add &gt;&gt;</a>
                </div>    
            </div>
            <div class="form-row">
                <div class="field-label"><label for="quotawheels">Selected Quota Wheels</label>:</div>
                <div class="field-widget">
                    <select style="width: 80%;" multiple name="quotawheels" id="quotawheels">
                        {section name=quotawheel loop=$product.quotaWheels}
                            {if $product.quotaWheels[quotawheel].status|lower == 'active'}
                                <option id="{$product.quotaWheels[quotawheel].uid}" value="{$product.quotaWheels[quotawheel].uid}" >{$product.quotaWheels[quotawheel].name} - {$product.quotaWheels[quotawheel].plan}</option>
                            {/if}
                        {/section}
                    </select>
                    <a href="#" id="remove" onclick="jQuery('#quotawheels option:selected').remove();">&lt;&lt; remove</a>
                </div>    
            </div>        
            {if $meta.action=="create"}
                <input type="hidden" name="status" id="status" value="active"/>
            {else}
                <div class="form-row">
                    <div class="field-label"><label for="status">Status</label>:</div>
                    <div class="field-widget">
                        <select name="status" id="status" class="validate-selection">
                            <option value="null">Please select</option>
                            <option value="active" {if $product.status=="active"}selected="selected"{/if}>Active</option>
                            <option value="inactive" {if $product.status=="inactive"}selected="selected"{/if}>Inactive</option>
                            {if !empty($meta.show_deleted)}
                                <option value="deleted" {if $product.status=="deleted"}selected="selected"{/if}>Deleted</option>
                            {/if}
                        </select>
                    </div>
                </div>
            {/if}
            <div class="center">
                <button type="button" class="detailButton"
                        onclick="jQuery('#quotawheels option').attr('selected', 'selected');
                            var valid = new Validation('productMACForm');
                            if (!valid.validate()) return false;
                            xajax_productSubmit('{$meta.action}',xajax.getFormValues('productMACForm'));">
                    {$meta.action|capitalize} Product
                </button>
            </div>
            <br/><br/>
        </fieldset>
    </form>
</div>