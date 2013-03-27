<table id="list_tbl" class="">
    <tr class="empty"><td colspan="4" style="text-align:left;">
            <form id="search_f" action="javascript:void(null);" onsubmit="xajax_ownerView('listall',{ldelim}{rdelim});"><input id="owner_filter" name="owner_filter" style="margin-top:2px; margin-bottom: 2px; border: none; background-color: #EEEEEE; width: 7em;" type="text" value="{if $search == '%'}{else}{$search}{/if}"/><input style="border: 1px solid gray; margin-left: 1em; " type="submit" value="Filter"></form>
        </td></tr>
    <tr><th>Reseller</th><th>Org ID</th><th>Email</th><th>Status</th></tr>
	{section name=name loop=$owners}
    <tr id="list_row" class="{cycle values="odd,even"}" onclick="xajax_ownerView('read',{ldelim}id: '{$owners[name].id}'{rdelim});">
        <td id="ownerlist_data2">{$owners[name].name}</td>
        <td id="ownerlist_data1">{$owners[name].login}</td>
        <td id="ownerlist_data4">{$owners[name].primaryemail}</td>
        <td id="ownerlist_data5">{$owners[name].status|capitalize}</td>
    </tr>
	{/section}
    <tr class="empty"><td colspan="5">&nbsp;</td></tr>
</table>
