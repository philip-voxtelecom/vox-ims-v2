<div id="account_detail">
    <div class="titleLabel">Account Details</div>
    <table class="detailTable">
        <tr >
            <td class="label" onclick="togglePrint(this.parentNode);">Account Reference</td><td class="detail">{$viewobject->data->account->description}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Product Option</td><td class="detail">{$viewobject->data->product->name}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Bundle Limit</td><td class="detail">{$viewobject->data->account->bundlesize}GB</td>
        </tr>
{if $viewobject->data->account->topupsize > 0 }
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Loaded Topup</td><td class="detail">{$viewobject->data->account->topupsize}GB</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Total Bundle</td><td class="detail">{$viewobject->data->account->bundlesize+$viewobject->data->account->topupsize}GB</td>
        </tr>
{/if}
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Username</td><td class="detail">{$viewobject->data->account->username}</td>
        </tr>
        <!--
        <tr >
            <td class="label" onclick="togglePrint(this.parentNode);">Password</td><td class="detail">
                <span style="display: none;" id='pass'>{$viewobject->data->account->password}</span>
                <span id='stars' onclick="xajax.$('stars').style.display='none'; xajax.$('pass').style.display='block';">******</span></td>
        </tr>
        -->
        <!--
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Cell number</td><td class="detail">{$viewobject->data->account->notifycell}</td>
        </tr>
        -->
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Email</td><td class="detail">{$viewobject->data->account->notifyemail}</td>
        </tr>        
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">ADSL Line</td><td class="detail">{$viewobject->data->account->callingstation}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Mail Report</td><td class="detail">{$viewobject->data->account->mailreport|capitalize}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Status</td><td class="detail">{$viewobject->data->account->status|capitalize}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">System Reference</td><td class="detail">{$viewobject->data->account->systemReference}</td>
        </tr>
        <!--
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Comments</td><td class="detail">{$viewobject->data->account->note}</td>
        </tr>
        -->
    </table>
</div>

<input type="button" class="detailButton" onclick="printpage('account_detail');" value="Print" />
