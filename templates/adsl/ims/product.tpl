<div class="titleLabel">Product</div>
<table id="list_tbl" class="">
    <tr><th>Name</th><th>Value</th></tr>
	{foreach item=i key=k from=$product}
    <tr id="list_row" class="{cycle values="odd,even"}">
        <td id="grouplist_data2">{$k}</td>
        <td id="grouplist_data5">{$i}</td>
    </tr>
	{/foreach}
    <tr id="list_row" class="{cycle values="odd,even"}">
        <td id="grouplist_data2">Group</td>
        <td id="grouplist_data5">{$group.name}</td>
    </tr>
</table>