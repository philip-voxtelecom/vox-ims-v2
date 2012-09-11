<div id="create_user_detail">
    <div class="form-row">
        <div class="field-label"><label for="_save_name">Description</label>:</div>
        <div class="field-widget"><input value="{if isset($viewobject)}{$viewobject->data->account->description}{/if}" name="_save_name" id="_save_name" {if empty($viewobject) or $viewobject->action != 'update'}class="required"{/if} style="width:340px;" title="Enter account reference" onchange="xajax.$('name_detail').innerHTML=this.value;"/></div>
    </div>

    <div class="form-row">
        <div class="field-label"><label for="_save_cellno">Cell number</label>:</div>
        <div class="field-widget"><input value="{if isset($viewobject)}{$viewobject->data->account->notifycell}{/if}" name="_save_cellno" id="_save_cellno" class="validate-phone" style="width:340px;" title="Enter account owner cell number for notifications" onchange="xajax.$('cell_detail').innerHTML=this.value;"/></div>
    </div>
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
                <option value="monthly {if $viewobject->data->account->mailreport=='monthly'}selected="selected"{/if}">Monthly</option>
            </select>
        </div>
    </div>
    
    <div class="form-row">
        <div class="field-label"><label for="_save_note">Note</label>:</div>
        <div class="field-widget"><textarea  value="{if isset($viewobject)}{$viewobject->data->account->note}{/if}"name="_save_note" id="_save_note" cols="40" rows="4" title="Note for account" onchange="xajax.$('note_detail').innerHTML=this.value;">{if isset($viewobject)}{$viewobject->data->account->note}{/if}</textarea></div>
    </div> 

    <input type="hidden" name="id" id="id" value="{if isset($viewobject)}{$viewobject->data->account->id}{/if}"/>
</div>
