<div class="titleLabel">Edit Account</div>
<div style="margin-left: 2em;">
    <form  action="#" name="accountUpdateForm" id="accountUpdateForm" method="post" >
        <fieldset class="sectionwrap">
            <legend>Account Detail</legend>
            {$accountDetail}
        </fieldset>

        <fieldset class="sectionwrap">
            <legend>Product Detail</legend>
            {$accountProduct}
            <div class="form-row">
                <div class="field-label"><label for="_save_password">Password *</label>:</div>
                <div class="field-widget">
                    <input name="_save_password" id="_save_password" {if empty($viewobject) or $viewobject->action != 'update'}class="required"{/if} style="width:340px;" title="Enter password for account" onchange="xajax.$('password_detail').innerHTML=this.value;"/>
                    <input type="button" id="passwordgen" name="passwordgen" value="Generate" onClick="GeneratePassword('_save_password',8);xajax.$('password_detail').innerHTML=xajax.$('_save_password').value;"/>
                </div>
            </div>
        </fieldset>

        <fieldset class="sectionwrap">
            <legend>Change Summary</legend>
            <div id="form_controls">
                <table class="detailTable">
                    <tr >
                        <td class="label" onclick="togglePrint(this.parentNode);">Reference</td><td class="detail" id="name_detail"></td>
                    </tr>
                    <tr >
                        <td class="label" onclick="togglePrint(this.parentNode);">Password</td><td class="detail" id="password_detail" id="password_detail"></td>
                    </tr>
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Cell number</td><td class="detail" id="cell_detail"></td>
                    </tr>
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Email</td><td class="detail" id="email_detail"></td>
                    </tr>
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Note</td><td class="detail" id="note_detail"></td>
                    </tr>   
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Mail Report</td><td class="detail" id="mailreport_detail"></td>
                    </tr> 
                    {foreach from=$accountoptions key=option item=property}
                        {if !(!empty($property.immutable.update) and isset($viewobject) and $viewobject->action == 'update')}
                            <tr>
                                <td class="label" onclick="togglePrint(this.parentNode);">{$property.description}</td><td class="detail" id="{$option}_detail"></td>
                            </tr>                        
                        {/if}
                    {/foreach}
                    {foreach from=$productoptions key=option item=property}
                        {if !(!empty($property.immutable.update) and isset($viewobject) and $viewobject->action == 'update')}
                            <tr>
                                <td class="label" onclick="togglePrint(this.parentNode);">{$property.description}</td><td class="detail" id="{$option}_detail"></td>
                            </tr>                        
                        {/if}
                    {/foreach}
                    <!--
                    <tr>
                        <td class="label" onclick="togglePrint(this.parentNode);">Reference</td><td class="detail" id="reference_detail"></td>
                    </tr>
                    -->
                </table>
                <div class="center">
                    <input type="hidden" name="offset" id="offset" value="{$offset}"/>
                    <input type="hidden" name="search" id="search" value="{$search}"/>
                    <input type="hidden" name="limit" id="limit" value="{$limit}"/>
                    <button type="button" class="detailButton"
                            onclick="var valid = new Validation('accountUpdateForm');
                                if (!valid.validate()) return false; this.style.visibility='hidden';
                                xajax_accountSubmit('update',xajax.getFormValues('accountUpdateForm'));">
                {if $viewobject->action != 'update'}Create Account{else}Update Account{/if}
            </button>
        </div>
    </div>
    <br/><br/>
</fieldset>
</form>
</div>