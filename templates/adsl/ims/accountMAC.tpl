{if $meta.action=='create'}
    {assign var=title value='Create'}
{else}
    {assign var=title value='Update'}
{/if}
<div class="titleLabel">{$title} Account</div>
<div style="margin-left: 2em;">
    <form  action="#" name="accountMACForm" id="accountMACForm" method="post" >
        <fieldset class="sectionwrap">
            <legend>Account Detail</legend>
            <div id="create_user_detail">
                <div class="form-row">
                    <div class="field-label"><label for="_save_name">Description</label>:</div>
                    <div class="field-widget"><input value="{if isset($viewobject)}{$viewobject->data->account->description}{/if}" name="_save_name" id="_save_name" {if empty($viewobject) or $viewobject->action != 'update'}class="required"{/if} style="width:340px;" title="Enter account reference" onchange="xajax.$('name_detail').innerHTML=this.value;"/></div>
                </div>

                <!--
                <div class="form-row">
                    <div class="field-label"><label for="_save_cellno">Cell number</label>:</div>
                    <div class="field-widget"><input value="{if isset($viewobject)}{$viewobject->data->account->notifycell}{/if}" name="_save_cellno" id="_save_cellno" class="validate-phone" style="width:340px;" title="Enter account owner cell number for notifications" onchange="xajax.$('cell_detail').innerHTML=this.value;"/></div>
                </div>
                -->
                <div class="form-row">
                    <div class="field-label"><label for="_save_email">Email address</label>:</div>
                    <div class="field-widget"><input value="{if isset($viewobject)}{$viewobject->data->account->notifyemail}{/if}" name="_save_email" id="_save_email" class="validate-email" style="width:340px;" title="Enter account email address for notifications" onchange="xajax.$('email_detail').innerHTML=this.value;"/></div>
                </div>
                <div class="form-row">
                    <div class="field-label"><label for="_save_mailreport">Email usage reports</label>:</div>
                    <div class="field-widget">
                        <select name="_save_mailreport" id="_save_mailreport" class="required" onchange="xajax.$('mailreport_detail').innerHTML=this.options[this.selectedIndex].text;">
                            <option value="never" {if $viewobject->data->account->mailreport=='never'}selected="selected"{/if}>Never</option>
                            <option value="daily" {if $viewobject->data->account->mailreport=='daily'}selected="selected"{/if}>Daily</option>
                            <option value="weekly" {if $viewobject->data->account->mailreport=='weekly'}selected="selected"{/if}>Weekly</option>
                            <option value="monthly" {if $viewobject->data->account->mailreport=='monthly'}selected="selected"{/if}">Monthly</option>
                        </select>
                    </div>
                </div>

                <!--
                <div class="form-row">
                    <div class="field-label"><label for="_save_note">Note</label>:</div>
                    <div class="field-widget"><textarea  value="{if isset($viewobject)}{$viewobject->data->account->note}{$viewobject->data->account->mailreport}{/if}"name="_save_note" id="_save_note" cols="40" rows="4" title="Note for account" onchange="xajax.$('note_detail').innerHTML=this.value;">{if isset($viewobject)}{$viewobject->data->account->note}{/if}</textarea></div>
                </div>
                -->

                <input type="hidden" name="id" id="id" value="{if isset($viewobject)}{$viewobject->data->account->id}{/if}"/>
            </div>
        </fieldset>

        <fieldset class="sectionwrap">
            <legend>Product Setup</legend>
            {if $meta.action=='create'}
                <div id="product_group">
                    <div class="form-row">
                        <div class="field-label"><label for="productgroup">Product Group</label>:</div>
                        <select name="productgroup" id="productgroup" class="validate-selection" onchange="
                            xajax.$('productgroup_detail').innerHTML=this.options[this.selectedIndex].text;
                            jQuery('input[name=_save_user]').prop('value','');
                            jQuery('input[name=_save_username]').prop('value','');
                            jQuery('input[name=_save_user]').prop('disabled',true);
                            var groupid=$(this).value;
                            var ret = xajax_productGroupView('viewproducts',
                                {ldelim}
                                    id: groupid, 
                                    as: 'select', 
                                    target: 'productlistTarget', 
                                    name: 'product', 
                                    class: 'validate-selection', 
                                    onchange: 'if ($(this).value == \'null\') {ldelim} jQuery(\'input[name=_save_user]\').prop(\'disabled\',true); return; {rdelim} else {ldelim} jQuery(\'input[name=_save_user]\').prop(\'disabled\',false); {rdelim};var id=$(this).value; var ret = xajax_productView(\'read\',{ldelim}id: id,value: \'realm\',target: \'_save_realm\'{rdelim});xajax.$(\'product_detail\').innerHTML=this.options[this.selectedIndex].text;'
                                {rdelim});
                                ">
                            <option value="null">Please select</option>
                            {section name=productgroup loop=$productgroups}
                                {if $productgroups[productgroup].status|lower == 'active'}
                                    <option label="{$productgroups[productgroup].name}" value="{$productgroups[productgroup].id}">{$productgroups[productgroup].name}</option>
                                {/if}
                            {/section}
                        </select>
                    </div>
                </div>
                <div id="product_option">
                    <div class="form-row">
                        <div class="field-label"><label for="_save_product">Product</label>:</div>
                        <div id='productlistTarget'>
                            <select name="product" id="product" class="validate-selection" onchange="xajax.$('product_detail').innerHTML=this.options[this.selectedIndex].text;">
                                <option value="null">Please select product group above</option>
                            </select>
                        </div>
                    </div>
                </div>
            {else}
                <div id="product_label">
                    <div class="form-row">
                        <div class="field-label"><label >Current Product Option</label>:</div>
                        <div id='label'>
                            {$accountProduct.name}
                        </div>
                    </div>
                </div>
                <div id="product_option">
                    <div class="form-row">
                        <div class="field-label"><label for="_save_product">Product</label>:</div>
                        <div id='productlistTarget'>
                            <select name="product" id="product" onchange="xajax.$('product_detail').innerHTML=this.options[this.selectedIndex].text;">
                                <option value="{$accountProduct.id}">Please select new product option</option>
                                {section name=productlist loop=$productlist}
                                    {if $productlist[productlist].status|lower == 'active'}
                                        <option value="{$productlist[productlist].uid}">{$productlist[productlist].name}</option>
                                    {/if}
                                {/section}
                            </select>
                        </div>
                    </div>
                </div>   
            {/if}

            {if $meta.action=='create'}
                <input type="hidden" name="_save_realm" id="_save_realm" value=""/>
                <div class="form-row">
                    <div class="field-label"><label for="_save_user">Username</label>:</div>
                    <div class="field-widget"><input value="" name="_save_user" id="_save_user" class="required  validate-alphanum" style="width:340px;" title="Username to create" disabled="disabled"
                                                     onkeyup="Validation.reset('isUsernameAvailable');xajax.$('_save_username').value=this.value+'@'+xajax.$('_save_realm').value;xajax.$('username_detail').innerHTML=xajax.$('_save_username').value;"/></div>
                </div>
                <div class="form-row">
                    <div class="field-label"><label for="_save_username">Resultant Username</label>:</div>
                    <div class="field-widget">
                        <input value="" name="_save_username" id="_save_username" style="width:340px;" title="Resultant username" readonly="readonly" />
                        <input value="" type="hidden" id="isUsernameAvailable" name="isUsernameAvailable" class="validate-username"/>
                    </div>                        
                </div>
                {foreach from=$productoptions key=option item=property}
                    {if !(!empty($property.immutable.update) and isset($viewobject) and $viewobject->action == 'update')}
                        <div class="form-row">
                            <div class="field-label"><label for="_option_{$option}">{$property.description}</label>:</div>
                            <div class="field-widget"><input value="{$property.defaultvalue}" name="_save_{$option}" id="_save_{$option}" class="{if $property.mandatory == true}required{/if} {if isset($property.validation.class)}validate-{$property.validation.class}{/if}" style="width:340px;" title="{$property.hint}" onchange="xajax.$('{$option}_detail').innerHTML=this.value;"/></div>
                        </div>
                    {/if}
                {/foreach}
            {else}
                {foreach from=$productoptions key=option item=property}
                    <div class="form-row">
                        <div class="field-label"><label for="_option_{$option}">{$property.description}</label>:</div>
                        <div class="field-widget"><input value="{if isset($viewobject)}{$viewobject->data->account->$option}{else}{$property.defaultvalue}{/if}" name="_save_{$option}" id="_save_{$option}" {if empty($viewobject) or $viewobject->action != 'update'}{if $property.mandatory == true}class="required"{/if}{/if} style="width:340px;" title="{$property.hint}" onchange="xajax.$('{$option}_detail').innerHTML=this.value;"/></div>
                    </div>
                {/foreach}
            {/if}
            <div class="form-row">
                <div class="field-label"><label for="_save_password">Password</label>:</div>
                <div class="field-widget">
                    <input name="_save_password" id="_save_password" {if empty($viewobject) or $viewobject->action != 'update'}class="required validate-password"{/if} style="width:340px;" title="Enter password for account" onchange="xajax.$('password_detail').innerHTML=this.value;"/>
                    <input type="button" id="passwordgen" name="passwordgen" value="Generate" onClick="GeneratePassword('_save_password',8);xajax.$('password_detail').innerHTML=xajax.$('_save_password').value;"/>
                </div>

            </div>
            {foreach from=$accountoptions key=option item=property}
                {if !(!empty($property.immutable.update) and isset($viewobject) and $viewobject->action == 'update')}
                    <div class="form-row">
                        <div class="field-label"><label for="_option_{$option}">{$property.description}</label>:</div>
                        <div class="field-widget"><input value="{$property.defaultvalue}" name="_save_{$option}" id="_save_{$option}" {if $property.mandatory == true}class="required {if isset($property.validation.class)}validate-{$property.validation.class}{/if}"{/if} style="width:340px;" title="{$property.hint}" onchange="xajax.$('{$option}_detail').innerHTML=this.value;"/></div>
                    </div>
                {/if}
            {/foreach}
        </fieldset>

        <fieldset class="sectionwrap">
            {if $meta.action == 'create'}
                <legend>Summary</legend>
            {else}
                <legend>Summary of changes</legend>
            {/if}
            <div id="form_controls">
                <table class="detailTable">
                    <tr >
                        <td class="label" onclick="togglePrint(this.parentNode);">Account Description</td><td class="detail" id="name_detail"></script></td>
                    </tr>
                    {if $meta.action == 'create'}
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Product Group</td><td class="detail" id="productgroup_detail"></td>
                    </tr>
                    {/if}
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Product Option</td><td class="detail" id="product_detail"></td>
                    </tr>

                    {foreach from=$productoptions key=option item=property}
                        {if !(!empty($property.immutable.update) and isset($viewobject) and $viewobject->action == 'update')}

                            <tr>
                                <td class="label" onclick="togglePrint(this.parentNode);">{$property.description}</td><td class="detail" id="{$option}_detail"></td>
                            </tr>                        
                        {/if}
                    {/foreach}
                    {if $meta.action == 'create'}
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Username</td><td class="detail" id="username_detail"></td>
                    </tr>
                    {/if}
                    <tr >
                        <td class="label" onclick="togglePrint(this.parentNode);">Password</td><td class="detail" id="password_detail">
                            <!--
                            <span style="display: none;" id='pass'></span>
                            <span id='stars' onclick="xajax.$('stars').style.display='none'; xajax.$('pass').style.display='block';">******</span></td>
                            -->
                    </tr>
                    <!--
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Cell number</td><td class="detail" id="cell_detail"></td>
                    </tr>
                    -->
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Email</td><td class="detail" id="email_detail"></td>
                    </tr>
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Mail usage reports</td><td class="detail" id="mailreport_detail"></td>
                    </tr>                    
                    {foreach from=$accountoptions key=option item=property}
                        <tr>
                            <td class="label" onclick="togglePrint(this.parentNode);">{$property.description}</td><td class="detail" id="{$option}_detail"></td>
                        </tr>                        

                    {/foreach}                    
                    <!--
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Note</td><td class="detail" id="note_detail"></td>
                    </tr>
                    -->

                </table>
                <div class="center"><button type="button" class="detailButton"
                                            onclick="var valid = new Validation('accountMACForm');
                                                if (!valid.validate()) return false;
                                                xajax_accountSubmit('{$meta.action}',xajax.getFormValues('accountMACForm'));">
                        {$meta.action|capitalize} Account
                    </button>
                </div>
            </div>
            <br/><br/>
        </fieldset>
        <input type="hidden" name="isUpdate" id="isUpdate" value="0"/>
    </form>
</div>