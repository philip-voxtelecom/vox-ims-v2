<input type="hidden" name="cap_name" id="cap_name" value="{$cap_name}"/>
<div class="form-row">
    <div class="field-label"><label for="_save_detail_cap">Cap*</label>:</div>
    <div class="field-widget">
        <select id="_save_detail_cap" name="_save_detail_cap" class="validate-selection" title="Choose your cap">
            <option value="null">&nbsp;</option>
            <option {if $cap == '1000000000'}selected="yes"{/if} value="1000000000">1GB</option>
            <option {if $cap == '5000000000'}selected="yes"{/if} value="5000000000">5GB</option>
            <option {if $cap == '12000000000'}selected="yes"{/if} value="12000000000">12GB</option>
            <option {if $cap == '24000000000'}selected="yes"{/if} value="24000000000">24GB</option>
        </select>
    </div>
</div>
<div class="form-row">
    <div class="field-label"><label for="_save_detail_sub">Reference</label>:</div>
    <div class="field-widget"><input value="{$subs}" name="_save_detail_subs" id="_save_detail_subs" style="width: 340px;" title="Enter reference" /></div>
</div>
<div class="form-row">
    <div class="field-label"><label for="_save_detail_stopdate">Termination date (yyyy-mm-dd)</label>:</div>
    <div class="field-widget"><input value="{$stopdate}" name="_save_detail_stopdate" id="_save_detail_stopdate" class="validate-date-au inputDateType" style="width: 340px;" title="Date at which the account will be terminated" /></div>
</div>
<div class="form-row">
    <div class="field-label"><label for="_save_detail_notify">Notification values (e.g. 50,90 - max 3)</label>:</div>
    <div class="field-widget"><input value="{$notify}" name="_save_detail_notify" id="_save_detail_notify" class="validate-notify" style="width: 340px;" title="Percentage values of bundle at which notifications will be sent" /></div>
</div>
<div class="form-row">
    <div class="field-label"><label for="_save_detail_simcons">Simultaneous connections</label>:</div>
    <div class="field-widget"><input value="{$simcons}" name="_save_detail_simcons" id="_save_detail_simcons" class="validate-number" style="width: 340px;" title="Number of simultaneous connections allowed" /></div>
</div>
<input type="hidden" name="product_id" id="product_id" value="{$product_id}" />
<input type="hidden" name="user_id" id="user_id" value="{$user_id}" />
<input type="hidden" name="selected" id="selected" value="{$selected}"  />
