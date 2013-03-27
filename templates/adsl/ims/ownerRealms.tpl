<div class="titleLabel">Realms</div>
<table id="list_tbl" class="">
    <tr><th>Name</th><th>Realm Status</th><th>Org Realm Status</th></tr>
	{foreach item=i key=k from=$realms}
    <tr id="list_row" class="{cycle values="odd,even"}">
        <td id="grouplist_data2">{$i.realm}</td>
        <td id="grouplist_data5">{$i.status}</td>
        <td id="grouplist_data5">{$i.rstatus}</td>
    </tr>
	{/foreach}
</table>