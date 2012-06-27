<div id="report">

    <div class="titleLabel">Account Usage Detail</div>

    <table class="detailTable">
        <tr >
            <td class="label">Total Usage</td><td class="detail">{$viewobject->data->totals->total_h} ({$viewobject->data->totals->total}b)</td>
        </tr>
        <tr >
            <td class="label">Downloads</td><td class="detail">{$viewobject->data->totals->downloads_h} ({$viewobject->data->totals->downloads}b)</td>
        </tr>
        <tr >
            <td class="label">Uploads</td><td class="detail">{$viewobject->data->totals->uploads_h} ({$viewobject->data->totals->uploads}b)</td>
        </tr>
        <tr >
            <td class="label">Total Connected Time</td><td class="detail">{$viewobject->data->totals->totaltime_h}</td>
        </tr>
        <tr >
            <td class="label">Number of connections</td><td class="detail">{$viewobject->data->totals->totalconns}</td>
        </tr>
        <tr >
            <td class="label">Report Start Date</td><td class="detail">{$viewobject->data->startdate}</td>
        </tr>
        <tr >
            <td class="label">Report End Date</td><td class="detail">{$viewobject->data->enddate}</td>
        </tr>
    </table>
    <br/>

    <table class="detailTable" style="background-color: #EBECE4;">
        <tr>
            <th>Account</th>
            <th>Status</th>
            <!--
            <th>Total Uploads</th>
            <th>Total Downloads</th>
            -->
            <th>Total Usage</th>
            <th>Bundle</th>
            <th>Excess</th>
        </tr>
{foreach from=$viewobject->xpath('//data/record') key=node item=data}
        <tr>
            <td style="text-align: left;">{$data->user}</td>
            <td style="text-align: left;">{$data->status}</td>
            <!--
            <td style="text-align: right;">{$data->uploads_h}</td>
            <td style="text-align: right;">{$data->downloads_h}</td>
            -->
            <td style="text-align: right;">{$data->total_h}</td>
            <td style="text-align: right;">{$data->bundle_h}</td>
            <td style="text-align: right;">{$data->excess_h}</td>
        </tr>
{/foreach}
    </table>

</div>
<form action="#" name='ownerExportForm' id='ownerExportForm'>
    <input type='hidden' name='startdate' value='{$viewobject->data->startdate}'/>
    <input type='hidden' name='enddate' value='{$viewobject->data->enddate}'/>
    <input type='hidden' name='export' value='csv'/>
    <input type='hidden' name='reportname' value='accountUsage'/>
</form>
<input type='button' class='detailButton' onclick="xajax_ownerReportGet(xajax.getFormValues('ownerExportForm'));" value="Export"/>
<input type="button" class="detailButton" onclick="printpage('report');" value="Print" />