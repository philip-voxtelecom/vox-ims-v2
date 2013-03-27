{if $meta.action eq 'create'}
    {assign var=title value='Create'}
{elseif $meta.action eq 'update'}
    {assign var=title value='Update'}
{else}
    {assign var=title value='View'}    
{/if}
<div class="titleLabel">{$title} Reseller</div>
<div style="margin-left: 2em;">
    <br/>
    <form  action="#" name="resellerCreateForm" id="resellerCreateForm" method="post" >
        {if $meta.action=='update'}
            <input type="hidden" name="id" id="id" value="{$reseller.id}"/>
        {/if}
        <fieldset class="sectionwrap">
            <legend>Reseller Detail</legend>
            <div class="form-row">
                <div class="field-label"><label for="name">Reseller Name</label>:</div>
                <div class="field-widget"><input value="{if isset($reseller)}{$reseller.name}{/if}" {if $meta.action=='view'}readonly="readonly"{/if} name="name" id="name" class="required" style="width:340px;" title="Enter reseller name"/></div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="primaryemail">Reseller Email</label>:</div>
                <div class="field-widget"><input value="{if isset($reseller)}{$reseller.primaryemail}{/if}" {if $meta.action=='view'}readonly="readonly"{/if} name="primaryemail" id="primaryemail" class="required validate-email" style="width:340px;" title="Enter reseller contact email"/></div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="login">Organisation ID</label>:</div>
                <div class="field-widget"><input value="{if isset($reseller)}{$reseller.login}{/if}" {if $meta.action eq 'view'}readonly="readonly"{/if} {if $meta.action=='update'}disabled="disabled"{/if} name="login" id="login" class="required" style="width:340px;" title="Enter reseller organisation ID"/></div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="password">Organisation Password</label>:</div>
                <div class="field-widget"><input value="{if isset($reseller)}{$reseller.password}{/if}" {if $meta.action eq 'view'}readonly="readonly"{/if} name="password" id="password" class="required" style="width:340px;" title="Enter reseller organisation password"/></div>
            </div>     
            <div class="form-row">
                <div class="field-label"><label for="comments">Notes</label>:</div>
                <div class="field-widget"><input value="{if isset($reseller)}{$reseller.comments}{/if}" {if $meta.action eq 'view'}readonly="readonly"{/if} name="comments" id="comments" class="" style="width:340px;" title="Comments"/></div>
            </div>
            {if $meta.action=="create"}
                <input type="hidden" name="status" id="status" value="active"/>
            {else}
                <div class="form-row">
                    <div class="field-label"><label for="status">Status</label>:</div>
                    <div class="field-widget">
                        <select {if $meta.action eq 'view'}disabled="disabled"{/if} name="status" id="status" class="validate-selection">
                            <option value="null">Please select</option>
                            <option value="active" {if $reseller.status=="active"}selected="selected"{/if}>Active</option>
                            <option value="inactive" {if $reseller.status=="inactive"}selected="selected"{/if}>Inactive</option>
                            {if !empty($meta.show_deleted)}
                                <option value="deleted" {if $group.status=="deleted"}selected="selected"{/if}>Deleted</option>
                            {/if}
                        </select>
                    </div>
                </div>

            {/if}

            {if $meta.action ne 'view'}
                <div class="center"><button type="button" class="detailButton"
                                            onclick="var valid = new Validation('resellerCreateForm');
                                                if (!valid.validate()) return false;
                                                xajax_ownerSubmit('{$meta.action}',xajax.getFormValues('resellerCreateForm'));">
                        {$meta.action|capitalize} Reseller
                    </button>
                </div>
            {/if}
            <br/><br/>
        </fieldset>
    </form>

</div>