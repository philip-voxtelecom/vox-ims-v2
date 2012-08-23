<div id="current_usage">
    <div class="titleLabel" >Daily Usage for {$year}-{$month}</div>
    <table>
        {assign var=downloads value=$totals.downloads/1024/1024}
        <tr><td>Downloads</td><td style="text-align: right;">{$downloads|string_format:"%.2f"} MB</td></tr>
        {assign var=uploads value=$totals.uploads/1024/1024}
        <tr><td>Uploads</td><td style="text-align: right;">{$uploads|string_format:"%.2f"} MB</td></tr>
        {assign var=total value=$totals.total/1024/1024}
        <tr><td>Total</td><td style="text-align: right;">{$total|string_format:"%.2f"} MB</td></tr>
    </table>
    <table id="list_tbl" class="">
        <th>Day</th><th>Uploads (MB)</th><th>Downloads (MB)</th><th>Total (MB)</th></tr>
        {foreach from=$viewobject key=k item=v}
            <tr id="listrow{$k}" class="{cycle values="odd,even"}" onclick="xajax_accountView('dailyUsageDetail',{ldelim}id: {$accountId}, year: '{$year}', month: '{$month}', day: '{$k}'{rdelim});">
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