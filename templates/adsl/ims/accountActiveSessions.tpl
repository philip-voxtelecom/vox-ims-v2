<div id="current_usage">
    <div class="titleLabel" >Active Sessions</div>
    <table id="list_tbl" class="">
        <th>Start Time</th><th>Session IP</th><th>ADSL line</th><th>Usage(MB)</th></tr>
        {foreach from=$viewobject key=k item=v}
            <tr id="listrow{$k}" class="{cycle values="odd,even"}">
                <td id="list_data1" style="text-align: left">{$v.starttime}</td>
                <td id="list_data1" style="text-align: right">{$v.ipaddress}</td>
                <td id="list_data1" style="text-align: right">{$v.telephonenumber}</td>
                {assign var=totalUsage value=$v.totalUsage/1024/1024}
                <td id="list_data1" style="text-align: right">{$totalUsage|string_format:"%.2f"}</td>
            </tr>
        {/foreach}
    </table>
</div>
<input type="button" class="detailButtonLeft" onclick="xajax_accountSubmit('disconnectSession',{ldelim}id: '{$accountId}',search:'{$search}',offset:{$offset},limit:{$limit}{rdelim});" value="Disconnect" />
<input type="button" class="detailButton" onclick="printpage('current_usage');" value="Print" />
