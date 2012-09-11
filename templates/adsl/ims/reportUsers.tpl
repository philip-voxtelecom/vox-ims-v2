<div class="titleLabel">User Usage</div>

<!--
<form action="#" name="ownerReportForm" id="ownerReportForm" method="post">
    <table>
        <tr>
            <td>Start <input class="inputDateTypeUnrestricted" name="startdate" size=12/></td>
            <td>End <input class="inputDateTypeUnrestricted" name="enddate" size=12/></td>
            <td><button class="detailButton" type="button" style="margin: 0px;" onclick="xajax_reportView('user',{ldelim}startdate: xajax.$('startdate'), enddate: xajax.$('enddate'){rdelim});">Go</button></td>
        </tr>
    </table>
</form>
-->

<div id="reportContent" name="reportContent">
    <table id="list_tbl" class="sortable">
        <thead>
            <tr><th id="username">Username</th><th id="uploads">Uploads GB</th><th id="downloads">Downloads GB</th><th id="total" class="sortfirstdesc">Total GB</th></tr>
        </thead>
        {foreach from=$usage key=k item=v}
            <tr
                {if $v.status == 'CANCELLED'}
                onclick="alert('This account has been cancelled. No details are available');"
                {else}
                onclick="
                    viewarray={ldelim}id:{$k},search:'0',offset:0,limit:0,return: false{rdelim};
                    xajax_accountView('actions',viewarray);
                    new Effect.Pulsate('right_bar_content', {ldelim} pulses: 1,duration: 0.5,from: 0.4 {rdelim});"
                {/if}
            >
                <td class="noedit">{$v.username}</td>
                {assign var=uploads value=`$v.uploads/1073741824`}
                <td style="text-align: right" class="noedit">{$uploads|string_format:"%.2f"}</td>
                {assign var=downloads value=`$v.downloads/1073741824`}
                <td style="text-align: right" class="noedit">{$downloads|string_format:"%.2f"}</td>
                {assign var=total value=`$v.total/1073741824`}
                <td style="text-align: right" class="noedit">{$total|string_format:"%.2f"}</td>
            </tr>
        {/foreach}
    </table>
</div>

<input type="button" class="detailButton" onclick="printpage('reportContent');" value="Print" />