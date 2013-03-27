{assign var='cap' value=$viewobject->data->bundlesize|string_format:"%s"}

<div id="account_topup">
    <div class="titleLabel">Account Topup</div>
    <table class="detailTable">
        {foreach from=$account.quotaWheels item=quotawheel key=uid}
            {if $quotawheel.istopupable <> 0}
                <tr>
                    <td>{$quotawheel.name} remaining topup</td><td>{$quotawheel.remainingTopUp}</td>
                </tr>
            {/if}
        {/foreach}
    </table>
    <form action="#" name="accountTopupForm" id="accountTopupForm" method="post">
        <div class="detailTable">
            <div class="form-row">
                <div class="field-label"><label for="_topup">Please quota to topup*</label>:</div>
                <div class="field-widget">
                    <select id="quotawheel" name="quotawheel" class="validate-selection" title="Please Select quota to topup">
                        <option value="null">Please Select</option>
                        {foreach from=$account.quotaWheels item=quotawheel key=uid}
                            {if $quotawheel.istopupable <> 0}
                                <option id="{$uid}" value="{$uid}">{$quotawheel.name}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="field-label"><label for="_topup">Topup amount*</label>:</div>
                <div class="field-widget">
                    <select id="topupvalue" name="topupvalue" class="validate-selection" title="Please select amount to topup with">
                        <option value="null">Please Select</option>
                        <option value="1">1GB</option>
                        <option value="2">2GB</option>
                        <option value="3">3GB</option>
                        <option value="4">4GB</option>
                        <option value="5">5GB</option>
                        <option value="10">10GB</option>
                        <option value="15">15GB</option>
                        <option value="20">20GB</option>
                        <option value="25">25GB</option>
                        <option value="50">50GB</option>
                        <option value="75">75GB</option>
                        <option value="100">100GB</option>
                    </select>
                </div>
            </div>
            {assign var='id' value=$viewobject->data->id}
            <input type="hidden" name="id" id="id" value="{$id}"/>
            <input type="hidden" name="_save_detail_topup" id="_save_detail_topup" value="0"/>
        </div>
        <button type="button" class="detailButton"
                onclick="var valid = new Validation('accountTopupForm');
                    if (!valid.validate()) return false; this.style.visibility='hidden';
                    xajax_accountSubmit('update',xajax.getFormValues('accountTopupForm'));">
            Topup
        </button>
    </form>
    <div id="topup_summary" style="display: none; margin-top: 40px;">
        <div class="titleLabel">Proposed Topup Summary</div>
        <table class="detailTable">
            <tr>
                <td class="label">Account Bundle Amount</td><td class="detail">{$cap}GB</td>
            </tr>
            <tr>
                <td class="label">Current Topup Amount</td><td class="detail">{$currenttopup}GB</td>
            </tr>
            <tr>
                <td class="label">New Topup Amount</td><td class="detail" id="pro_topup"></td>
            </tr>
            <tr>
                <td class="label">New Total Bundle Amount</td><td class="detail" id="pro_total"></td>
            </tr>
        </table>

    </div>

    <div id="topup history">
    </div>
</div>