<div id="report_summary">
    <div class="titleLabel">Summary for Current Month</div>
    <table class="summaryTable">
        <tr >
            <td class="label" onclick="togglePrint(this.parentNode);">Total Accounts</td><td class="detail" style="text-align: right">{$viewobject.count}</td>
        </tr>
        <tr>
            {assign var=uploads value=`$viewobject.systemTotal.uploads/1073741824`}
            <td class="label" onclick="togglePrint(this.parentNode);">Total Uploads</td><td class="detail" style="text-align: right">{$uploads|string_format:"%.2f"} GB</td>
        </tr>
        <tr>
            {assign var=downloads value=`$viewobject.systemTotal.downloads/1073741824`}
            <td class="label" onclick="togglePrint(this.parentNode);">Total Downloads</td><td class="detail" style="text-align: right">{$downloads|string_format:"%.2f"} GB</td>
        </tr>
        <tr>
            {assign var=total value=`$viewobject.systemTotal.totalUsage/1073741824`}
            <td class="label" onclick="togglePrint(this.parentNode);">Total Usage</td><td class="detail" style="text-align: right">{$total|string_format:"%.2f"} GB</td>
        </tr>
    </table>
</div>

<input type="button" class="detailButton" onclick="printpage('report_summary');" value="Print" />
