<div class="titleLabel">Account List</div>
<table id="list_tbl" class="">
    <tr class="empty"><td colspan="4" style="text-align:left;">
            <form id="search_f" action="javascript:void(null);" onsubmit="window.prevline = null; searchfilter=xajax.$('account_filter').value; xajax_accountView('listall',{ldelim}offset: 0, limit: {$limit}, search: searchfilter{rdelim});"><input id="account_filter" name="account_filter" style="margin-top:2px; margin-bottom: 2px; border: none; background-color: #EEEEEE; width: 7em;" type="text" value="{if $search == '%'}{else}{$search}{/if}"/><input style="border: 1px solid gray; margin-left: 1em; " type="submit" value="Search"></form>
        </td></tr>
    <th>Account</th><th>Description</th><th>Status</th></tr>
    {section name=name loop=$accounts}
    <tr id="listrow{$accounts[name].id}" class="{cycle values="odd,even"}"
        onclick="if ( window.prevline )
        {ldelim}
            xajax.$(window.prevline).className = window.prevlineclass;
        {rdelim};
            window.prevlineclass = this.className;
            window.prevline = this.id;
            this.className = 'selected_row';
            viewarray={ldelim}id:{$accounts[name].id},search:'{$search}',offset:{$offset},limit:{$limit}{rdelim};
            xajax_accountView('actions',viewarray);
            new Effect.Pulsate('right_bar_content', {ldelim} pulses: 1,duration: 0.5,from: 0.4 {rdelim});"
        >

        <td id="list_data1">{$accounts[name].username}</td>
        <td style="width: 100%" id="userlist_data2">{if isset($accounts[name].description)}{$accounts[name].description}{/if}</td>
        <td id="list_data1">{$accounts[name].status}</td>
    </tr>
{/section}
<tr class="empty">
    <td colspan="4" style="text-align:center; "><div class="formpaginate" style="width: 100%; overflow:hidden;">
            {if $offset == 0 and $count > $limit}     
                <a href="#" onclick="
                    viewarray={ldelim}search:'{$search}',offset:{$offset+$limit},limit:{$limit}{rdelim};
                    xajax_accountView('listall',viewarray);"
                   ><span class="next">Next page &gt;&gt;</span></a>
            {elseif $count > $limit and $count > $offset+$limit}
                <a href="#" onclick="
                    viewarray={ldelim}search:'{$search}',offset:{$offset-$limit},limit:{$limit}{rdelim};
                    xajax_accountView('listall',viewarray);"
                   ><span class="prev">&lt;&lt; Previous page</span></a>
                &nbsp;|&nbsp;
                <a href="#" onclick="
                    viewarray={ldelim}search:'{$search}',offset:{$offset+$limit},limit:{$limit}{rdelim};
                    xajax_accountView('listall',viewarray);" 
                   ><span class="next">Next page &gt;&gt;</span></a>
            {elseif $offset > 0 and $count <= $offset+$limit}
                <a href="#" onclick="
                    viewarray={ldelim}search:'{$search}',offset:{$offset-$limit},limit:{$limit}{rdelim};
                    xajax_accountView('listall',viewarray);"
                   ><span class="prev">&lt;&lt; Previous page</span></a>
            {/if}</div>
    </td>
</tr>
</table>
