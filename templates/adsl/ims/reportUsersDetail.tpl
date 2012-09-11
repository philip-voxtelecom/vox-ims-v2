
<table id="list_tbl" class="">
    <th>Account</th><th>Uploads</th><th><th>Downloads</th><th>Total</th><th>Status</th></tr>
    {section name=name loop=$viewobject[accounts]}
    <tr class="{cycle values="odd,even"}">
        <td id="list_data1">{$viewobject[name].username}</td>
        <td id="list_data1">{$viewobject[name].uploads}</td>
        <td id="list_data1">{$viewobject[name].downloads}</td>
        <td id="list_data1">{$viewobject[name].totalUsage}</td>
    </tr>
{/section}
</table>
