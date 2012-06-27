<div id="current_usage">
    <div class="titleLabel" >Current Usage</div>
    <table id="list_tbl" class="">
        <th>Day</th><th>Uploads (MB)</th><th>Downloads (MB)</th><th>Total (MB)</th></tr>
        {foreach from=$viewobject key=k item=v}
            <tr id="listrow{$k}" class="{cycle values="odd,even"}">
                <td id="list_data1" style="text-align: left">{$k}</td>
                {assign var=uploads value=$v.uploads/1024/1024}
                <td id="list_data1" style="text-align: right">{$uploads|string_format:"%.2f"}</td>
                {assign var=downloads value=$v.downloads/1024/1024}
                <td id="list_data1" style="text-align: right">{$downloads|string_format:"%.2f"}</td>
                {assign var=totalUsage value=$v.totalUsage/1024/1024}
                <td id="list_data1" style="text-align: right">{$totalUsage|string_format:"%.2f"}</td>
            </tr>
        {/foreach}
    </table>
</div>
</div>
<input type="button" class="detailButton" onclick="printpage('current_usage');" value="Print" />