<div class="titleLabel">Account List</div>
<table id="list_tbl" class="">
    <tr class="empty"><td colspan="4" style="text-align:left;">
            <form id="search_f" action="javascript:void(null);" onsubmit="window.prevline = null; if (xajax.$('account_filter').value == '') {ldelim} searchfilter='%' {rdelim} else {ldelim} searchfilter=xajax.$('account_filter').value {rdelim}; xajax_displayAccountList(searchfilter,0,null,xajax.$('showcancelled').checked);"><input id="account_filter" name="account_filter" style="margin-top:2px; margin-bottom: 2px; border: none; background-color: #EEEEEE; width: 7em;" type="text" value="{if $search == '%'}{else}{$search}{/if}"/>&nbsp;Hide Cancelled<input type="checkbox" name='showcancelled' id="showcancelled" {if !empty($showcancelled)}checked{/if}/><input style="border: 1px solid gray; margin-left: 1em; " type="submit" value="Filter"></form>
        </td></tr>
    <tr><th>Select</th><th>Account</th><th>Name</th><th>Status</th></tr>
	{section name=name loop=$accounts}
    <tr id="listrow{$accounts[name].id}" class="{cycle values="odd,even"}"
        onclick="if ( window.prevline )
                       {ldelim}
                           xajax.$(window.prevline).className = window.prevlineclass;
                       {rdelim};
                       window.prevlineclass = this.className;
                       window.prevline = this.id;
                       xajax.$('id{$accounts[name].id}').checked='yes';
                       this.className = 'selected_row';
                       xajax_accountActionDisplay({$accounts[name].id});
                       new Effect.Pulsate('right_bar_content', {ldelim} pulses: 1,duration: 0.5,from: 0.4 {rdelim});"
        >
 
        <td id="list_data1">{$accounts[name].username}</td>
        <td style="width: 100%" id="userlist_data2">{$accounts[name].description}</td>

    </tr>
	{/section}
    <tr class="empty">
        <td colspan="4" style="text-align:center; "><div class="formpaginate" style="overflow:hidden;">
        {if $offset == 0 and $count > $rowlimit}
                <a href="#" onclick="xajax_displayAccountList('{$search}',{$offset+$rowlimit},null,xajax.$('showcancelled').checked);"><span class="next">Next page &gt;&gt;</span></a>
        {elseif $count > $rowlimit}
                <a href="#" onclick="xajax_displayAccountList('{$search}',{$offset-$rowlimit},null,xajax.$('showcancelled').checked);"><span class="prev">&lt;&lt; Previous page</span></a>
                &nbsp;|&nbsp;
                <a href="#" onclick="xajax_displayAccountList('{$search}',{$offset+$rowlimit},null,xajax.$('showcancelled').checked);" ><span class="next">Next page &gt;&gt;</span></a>
        {elseif $offset > 0 and $count <= $rowlimit}
                <a href="#" onclick="xajax_displayAccountList('{$search}',{$offset-$rowlimit},null,xajax.$('showcancelled').checked);"><span class="prev">&lt;&lt; Previous page</span></a>
        {/if}</div>
        </td>
    </tr>
</table>
