<div id="accountlist_action_list">

    <ul id="Menu2" class="MM" style="padding-top: 15px; margin: auto;">
        {foreach from=$viewobject->globalmenu->menuitem key=id item=i}
            <li><a href="#" onclick="{$i->action};">{$i->face}</a></li> 
        {/foreach}
        <li><a href="#" onclick="xajax_accountView('printlist',{ldelim}search: '{$search}', limit: 0{rdelim});">Print Account List</a></li>
        <li><a href="#" onclick="xajax_accountView('exportlist',{ldelim}search: '{$search}', limit: 0{rdelim});">Export Account List</a></li>

    </ul>
</div>
