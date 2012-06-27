{foreach from=$productoptions key=option item=property}
    <div class="form-row">
        <div class="field-label"><label for="_option_{$option}">{$property.description}</label>:</div>
        <div class="field-widget"><input value="{if isset($viewobject)}{$viewobject->data->account->$option}{else}{$property.defaultvalue}{/if}" name="_save_{$option}" id="_save_{$option}" {if empty($viewobject) or $viewobject->action != 'update'}{if $property.mandatory == true}class="required"{/if}{/if} style="width:340px;" title="{$property.hint}" onchange="xajax.$('{$option}_detail').innerHTML=this.value;"/></div>
    </div>
{/foreach}