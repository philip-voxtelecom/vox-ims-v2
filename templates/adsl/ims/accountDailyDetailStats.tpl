<div id="current_usage">
    <div class="titleLabel" >Sessions for {$year}-{$month}-{$day}</div>
    <table id="list_tbl" class="">
        <th>&nbsp;</th><th>Start Time</th><th>Session IP</th><th>ADSL line</th><th>Usage(MB)</th></tr>
        {foreach from=$viewobject key=k item=v}
            <tr id="listrow{$k}" class="{cycle values="odd,even"}">
                <td onmouseout="return nd();" onclick="return overlib('<table><tr><td>Session ID:</td><td>{$v.sessionId}</td></tr><tr><td>Start Time:</td><td>{$v.startTime}</td></tr><tr><td>End Time:</td><td>{$v.stopTime}</td></tr><tr><td>Session IP:</td><td>{$v.ipAddress}</td></tr><tr><td>ADSL Line:</td><td>{$v.telephoneNumber}</td></tr><tr><td>Uploads:</td><td>{$v.uploads} Bytes</td></tr><tr><td>Downloads:</td><td>{$v.downloads} Bytes</td></tr></table>', STICKY, CAPTION,'Details',WIDTH, 250,CLOSECLICK);">
                     <img src="/css/images/info_icon.png" height="15px" width="15px"/>
                </td>
                <td id="list_data1" style="text-align: left">{$v.startTime}</td>
                <td id="list_data1" style="text-align: right">{$v.ipAddress}</td>
                <td id="list_data1" style="text-align: right">{$v.telephoneNumber}</td>
                {assign var=totalUsage value=`$v.totalUsage/1024/1024`}
                <td id="list_data1" style="text-align: right">{$totalUsage|string_format:"%.2f"}</td>
            </tr>
        {/foreach}
    </table>
</div>
</div>
<input type="button" class="detailButton" onclick="printpage('current_usage');" value="Print" />