<table id="list_tbl" class="">
    <tr class="empty"><td colspan="4" style="text-align:left;">
            <form id="search_f" action="javascript:void(null);" onsubmit="xajax_displayProductList(xajax.$('product_filter').value,0);"><input id="product_filter" name="product_filter" style="margin-top:2px; margin-bottom: 2px; border: none; background-color: #EEEEEE; width: 7em;" type="text" value="{if $search == '%'}{else}{$search}{/if}"/><input style="border: 1px solid gray; margin-left: 1em; " type="submit" value="Filter"></form>
        </td></tr>
    <tr><th>ID</th><th>Name</th><th>Status</th></tr>
	{section name=name loop=$products}
    <tr id="list_row" class="{cycle values="odd,even"}" onclick="xajax_productDetailDisplay({$products[name].id});">
        <td id="productlist_data1">{$products[name].productId}</td>
        <td id="productlist_data2">{$products[name].name}</td>
        <td id="productlist_data5">{$products[name].status}</td>
    </tr>
	{/section}

</table>
