<div class="titleLabel">Create Account</div>
<div style="margin-left: 2em;">
    <form  action="#" name="accountCreateForm" id="accountCreateForm" method="post" >
        <fieldset class="sectionwrap">
            <legend>Account Detail</legend>
            {$accountDetail}
        </fieldset>

        <fieldset class="sectionwrap">
            <legend>Product Setup</legend>
            <div id="product_option">
                <div class="form-row">
                    <div class="field-label"><label for="_save_product">Product</label>:</div>
                    <select name="product" id="product" class="validate-selection" onchange="xajax.$('product_detail').innerHTML=this.options[this.selectedIndex].text;">
                        <option value="null">Please select</option>
                        {section name=product loop=$productlist}
                            {if $productlist[product].status|lower == 'active'}
                                <option label="{$productlist[product].description}" value="{$productlist[product].id}">{$productlist[product].description}</option>
                            {/if}
                        {/section}

                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="_save_realm">Realm</label>:</div>
                <div class="field-widget">
                    <select name="_save_realm" id="_save_realm" class="validate-selection" onchange="if (xajax.$('_save_realm').value != 'null') {ldelim}xajax.$('_save_username').value=xajax.$('_save_user').value+'@'+this.value;xajax.$('username_detail').innerHTML=xajax.$('_save_username').value;{rdelim}">
                        <option value="null">Please select</option>
                        {section name=realm loop=$realms}
                            {if $realms[realm].status|lower == 'active'}
                                <option value="{$realms[realm].realm}">{$realms[realm].realm}</option>
                            {/if}
                        {/section}

                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="_save_user">Username</label>:</div>
                <div class="field-widget"><input value="" name="_save_user" id="_save_user" class="required  validate-alphanum" style="width:340px;" title="Username to create" 
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
            <legend>Summary</legend>
            <div id="form_controls">
                <table class="detailTable">
                    <tr >
                        <td class="label" onclick="togglePrint(this.parentNode);">Account Description</td><td class="detail" id="name_detail"></script></td>
                    </tr>
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
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Username</td><td class="detail" id="username_detail"></td>
                    </tr>
                    <tr >
                        <td class="label" onclick="togglePrint(this.parentNode);">Password</td><td class="detail" id="password_detail" id="name_detail">
                            <span style="display: none;" id='pass'></span>
                            <span id='stars' onclick="xajax.$('stars').style.display='none'; xajax.$('pass').style.display='block';">******</span></td>
                    </tr>
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Cell number</td><td class="detail" id="cell_detail"></td>
                    </tr>
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
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Note</td><td class="detail" id="note_detail"></td>
                    </tr>                    
                    <!--
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Reference</td><td class="detail" id="reference_detail"></td>
                    </tr>
                    -->
                </table>
                <div class="center"><button type="button" class="detailButton"
                                            onclick="var valid = new Validation('accountCreateForm');
                                                if (!valid.validate()) return false;
                                                xajax_accountSubmit('create',xajax.getFormValues('accountCreateForm'));">
                        Create Account
                    </button>
                </div>
            </div>
            <br/><br/>
        </fieldset>
        <input type="hidden" name="isUpdate" id="isUpdate" value="0"/>
    </form>
</div>