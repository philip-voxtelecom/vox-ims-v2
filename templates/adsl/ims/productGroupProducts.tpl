<div class="titleLabel">Group Product List</div>
<table id="list_tbl" class="detailTable">
    <tr class="empty">
        <td colspan="4" style="text-align:left;">
            <form id="search_f" action="javascript:void(null);" onsubmit="xajax_productView('product',{ldelim}{rdelim});"><input id="group_filter" name="group_filter" style="margin-top:2px; margin-bottom: 2px; border: none; background-color: #EEEEEE; width: 7em;" type="text" value="{if $search == '%'}{else}{$search}{/if}"/><input style="border: 1px solid gray; margin-left: 1em; " type="submit" value="Filter"></form>
        </td>
    </tr>
    <tr><td class="label">Group</td><td class="detail">{$group.name}</td></tr>
    <tr><td class="label">Status</td><td class="detail">{$group.status|capitalize}</td></tr>
</table>
<table id="list_tbl" class="">
    <tr><th>&nbsp;</th><th>Name</th><th>Status</th></tr>
    {foreach item=i key=k from=$groupproducts}
        {if (($i.status!='deleted') or (!empty($meta.show_deleted) and $i.status=='deleted'))}
        <tr id="list_row" class="{cycle values="odd,even"}">
            <td onclick="xajax_productView('read',{ldelim}id:'{$i.uid}', groupid: '{$group.id}'{rdelim});">
                <img src="/css/images/info_icon.png" height="15px" width="15px"/>
            </td>
            <td style="width: 90%;" id="grouplist_data2">{$i.name}</td>
            <td id="grouplist_data5">{$i.status|capitalize}</td>
        </tr>
        {/if}
    {/foreach}

</table>