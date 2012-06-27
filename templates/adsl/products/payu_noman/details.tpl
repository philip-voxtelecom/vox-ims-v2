<div id="account_detail">
    <div class="titleLabel">Account Details</div>
    <table class="detailTable">
        <tr >
            <td class="label" onclick="togglePrint(this.parentNode);">Account Holder</td><td class="detail">{$name}</td>
        </tr>
        <tr class="noprint">
            <td class="label" onclick="togglePrint(this.parentNode);">Reseller Agreement Type</td><td class="detail">{$type|capitalize}</td>
        </tr>
        <tr class="noprint">
            <td class="label" onclick="togglePrint(this.parentNode);">Product Type</td><td class="detail">{$prodtype}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Product Option</td><td class="detail">{if isset($prodname_alt)}{$prodname_alt}{else}{$prodname}{/if}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Bundle Limit</td><td class="detail">{$cap/1000000000}GB</td>
        </tr>
{if !empty($topup) }
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Loaded Topup</td><td class="detail">{$topup/1000000000}GB</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Total Bundle</td><td class="detail">{assign var=totalbundle value=$topup+$cap}{$totalbundle/1000000000}GB</td>
        </tr>
{/if}
{if isset($topup_expire) }
        <tr class="noprint">
            <td class="label" onclick="togglePrint(this.parentNode);">Topup Expiry</td><td class="detail">{$topup_expire}</td>
        </tr>
{/if}
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Total Usage for period</td><td class="detail">{$totalusage/1000000000|string_format:"%.2f"}GB {if ($cap/1000000000)+($topup/1000000000) < ($totalusage/1000000000)}<span style="color: darkred; font-weight: bold;">***Over Used***</span>{/if}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Username</td><td class="detail">{$username}@{$realm}</td>
        </tr>
        <tr >
            <td class="label" onclick="togglePrint(this.parentNode);">Password</td><td class="detail">
                <span style="display: none;" id='pass'>{$password}</span>
                <span id='stars' onclick="xajax.$('stars').style.display='none'; xajax.$('pass').style.display='block';">******</span></td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Cell number</td><td class="detail">{$cellno}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Email</td><td class="detail">{$email}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Status</td><td class="detail">{$status|capitalize}</td>
        </tr>
{if isset($stopdate) }
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Termination Date</td><td class="detail">{$stopdate}</td>
        </tr>
{/if}
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Notification Threshold %</td><td class="detail">{$notify}</td>
        </tr>
        <tr>
            <td class="label" onclick="togglePrint(this.parentNode);">Simultaneous Connections</td><td class="detail">{$simcons}</td>
        </tr>

        <tr class="noprint">
            <td class="label" onclick="togglePrint(this.parentNode);">Comments</td><td class="detail">{$comments}</td>
        </tr>
        <tr class="noprint">
            <td class="label" onclick="togglePrint(this.parentNode);">Reference</td><td class="detail">{$subs}</td>
        </tr>
    </table>
</div>

<input type="button" class="detailButton" onclick="printpage('account_detail');" value="Print" />
