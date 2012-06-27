<table id="list_tbl" class="">
    <tr class="empty"><td colspan="4" style="text-align:left;">
            <form id="search_f" action="javascript:void(null);" onsubmit="xajax_displayOwnerList(xajax.$('owner_filter').value,0);"><input id="owner_filter" name="owner_filter" style="margin-top:2px; margin-bottom: 2px; border: none; background-color: #EEEEEE; width: 7em;" type="text" value="{if $search == '%'}{else}{$search}{/if}"/><input style="border: 1px solid gray; margin-left: 1em; " type="submit" value="Filter"></form>
        </td></tr>
    <tr><th>Login</th><th>Name</th><th>Email</th><th>Status</th></tr>
	{section name=name loop=$owners}
    <tr id="list_row" class="{cycle values="odd,even"}" onclick="xajax_ownerDetailDisplay({$owners[name].id});">
        <td id="ownerlist_data1">{$owners[name].login}</td>
        <td id="ownerlist_data2">{$owners[name].name}</td>
        <td id="ownerlist_data4">{$owners[name].primaryemail}</td>
        <td id="ownerlist_data5">{$owners[name].status}</td>
    </tr>
	{/section}
    <tr class="empty"><td colspan="5">&nbsp;</td></tr>
    <tr class="empty">
        <td colspan="5" style="text-align:center;">
        {if $offset == 0 and $count > $rowlimit}
            <a href="#" onclick="xajax_displayOwnerList('{$search}',{$offset+$rowlimit});">Next page &gt;&gt;</a>
        {elseif $count > $rowlimit}
            <a href="#" onclick="xajax_displayOwnerList('{$search}',{$offset-$rowlimit});">&lt;&lt; Previous page</a>
            &nbsp;|&nbsp;
            <a href="#" onclick="xajax_displayOwnerList('{$search}',{$offset+$rowlimit});" >Next page &gt;&gt;</a>
        {elseif $offset > 0 and $count <= $rowlimit}
            <a href="#" onclick="xajax_displayOwnerList('{$search}',{$offset-$rowlimit});">&lt;&lt; Previous page</a>
        {/if}
        </td>
    </tr>
</table>
