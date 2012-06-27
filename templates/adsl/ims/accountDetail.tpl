<div id="create_user_detail">
    <div class="form-row">
        <div class="field-label"><label for="_save_name">Description *</label>:</div>
        <div class="field-widget"><input value="{if isset($viewobject)}{$viewobject->data->account->description}{/if}" name="_save_name" id="_save_name" {if empty($viewobject) or $viewobject->action != 'update'}class="required"{/if} style="width:340px;" title="Enter account reference" onchange="xajax.$('name_detail').innerHTML=this.value;"/></div>
    </div>
    <div class="form-row">
        <div class="field-label"><label for="_save_password">Password *</label>:</div>
        <div class="field-widget">
            <input name="_save_password" id="_save_password" {if empty($viewobject) or $viewobject->action != 'update'}class="required"{/if} style="width:340px;" title="Enter password for account" onchange="xajax.$('password_detail').innerHTML=this.value;"/>
            <input type="button" id="passwordgen" name="passwordgen" value="Generate" onClick="GeneratePassword('_save_password',8);xajax.$('password_detail').innerHTML=xajax.$('_save_password').value;"/>
        </div>

    </div>
    <div class="form-row">
        <div class="field-label"><label for="_save_cellno">Cell number</label>:</div>
        <div class="field-widget"><input value="{if isset($viewobject)}{$viewobject->data->account->notifycell}{/if}" name="_save_cellno" id="_save_cellno" class="validate-phone" style="width:340px;" title="Enter account owner cell number for notifications" onchange="xajax.$('cell_detail').innerHTML=this.value;"/></div>
    </div>
    <div class="form-row">
        <div class="field-label"><label for="_save_email">Email address</label>:</div>
        <div class="field-widget"><input value="{if isset($viewobject)}{$viewobject->data->account->notifyemail}{/if}" name="_save_email" id="_save_email" class="validate-email" style="width:340px;" title="Enter account email address for notifications" onchange="xajax.$('email_detail').innerHTML=this.value;"/></div>
    </div>
    {foreach from=$accountoptions key=option item=property}
        {if !(!empty($property.immutable.update) and isset($viewobject) and $viewobject->action == 'update')}
        <div class="form-row">
            <div class="field-label"><label for="_option_{$option}">{$property.description}</label>:</div>
            <div class="field-widget"><input value="{$property.defaultvalue}" name="_save_{$option}" id="_save_{$option}" {if $property.mandatory == true}class="required {if isset($property.validation.class)}validate-{$property.validation.class}{/if}"{/if} style="width:340px;" title="{$property.hint}" onchange="xajax.$('{$option}_detail').innerHTML=this.value;"/></div>
        </div>
        {/if}
    {/foreach}
    <div class="form-row">
        <div class="field-label"><label for="_save_note">Note</label>:</div>
        <div class="field-widget"><textarea  value="{if isset($viewobject)}{$viewobject->data->account->note}{/if}"name="_save_note" id="_save_note" cols="40" rows="4" title="Note for account" onchange="xajax.$('note_detail').innerHTML=this.value;">{if isset($viewobject)}{$viewobject->data->account->note}{/if}</textarea></div>
    </div> 

    <input type="hidden" name="id" id="id" value="{if isset($viewobject)}{$viewobject->data->account->id}{/if}"/>
</div>
