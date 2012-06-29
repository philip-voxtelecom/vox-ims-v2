<div id="account_action_list">

    <ul style="margin: 15px 5px 0px 5px;" id="Menu2" class="MM" style="padding-top: 5px; margin: auto;">
        {foreach from=$viewobject->globalmenu->menuitem key=id item=i}
            <li><a href="#" onclick="{$i->action};">{$i->face}</a></li> 
        {/foreach}
    </ul>

    <div id="action_title"
         style="margin: {if count($viewobject->globalmenu->menuitem) == 0}20{else}5{/if}px 5px 0px 5px; text-align: center; background-color: #EB7305; color: #FFFFFF; font-weight: bold;">{$viewobject->data->account->username}</div>
    <ul id="Menu2" class="MM" style="padding-top: 5px; margin: auto; ">
        {foreach from=$viewobject->menu->menuitem key=id item=i}
            <li><a style="background-color: #ffe0c6;" href="#" onclick="window.prevline = null; {$i->action}; xajax.$('search_return').style.display='';">{$i->face}</a></li>
        {/foreach}

        <li id="search_return" {if $return == false}style="display: none;"{/if}>
            <a href="#"  style="background-color: #ffe0c6;" onclick="window.prevline = null;
                viewarray={ldelim}search:'{$search}',offset:{$offset},limit:{$limit}{rdelim};
                xajax_accountView('listall',viewarray);">
                Return to list</a>
        </li>

    </ul>

</div>





