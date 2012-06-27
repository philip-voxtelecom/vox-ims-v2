{assign var='cap' value=$viewobject->data->bundlesize|string_format:"%s"}

<div id="account_topup">
    <div class="titleLabel">Account Topup</div>
    <table class="detailTable">
        <tr>
            <td class="label">Account Bundle Amount</td><td class="detail">{$cap}GB</td>
        </tr>
{if isset($viewobject->data->topupsize) and $viewobject->data->topupsize != "null"}{assign var=currenttopup value=$viewobject->data->topupsize|string_format:"%s" }{else}{assign var=currenttopup value=0 }{/if}
{if $currenttopup <> 0 }
        <tr>
            <td class="label">Current Topup Amount</td><td class="detail">{$currenttopup}GB</td>
        </tr>
        <tr>
    {assign var='total' value=$viewobject->data->topupsize+$viewobject->data->bundlesize|string_format:"%s"}
            <td class="label">Total Bundle Amount</td><td class="detail">{$total}GB</td>
        </tr>
{/if}
    </table>
    <form action="#" name="accountTopupForm" id="accountTopupForm" method="post">
        <div class="detailTable">
            <div class="form-row">
                <div class="field-label"><label for="_topup">Please select amount to be topped up by*</label>:</div>
                <div class="field-widget">
                    <select id="_save_topup" name="_save_topup" class="validate-selection" title="Choose your topup amount"
                            onchange="
                      topup = this.value;
                      xajax.$('pro_topup').innerHTML=topup + 'GB';
                      xajax.$('pro_total').innerHTML=(Number(topup)+Number({$cap})+Number({$currenttopup})) + 'GB';
                      xajax.$('_save_detail_topup').value=Number(topup)+Number({$currenttopup});
                      xajax.$('topup_summary').style.display='block';

                            ">
                        <option value="null">None</option>
                        <option value="1">1GB</option>
                        <option value="2">2GB</option>
                        <option value="5">5GB</option>
                        <option value="10">10GB</option>
                        <option value="25">25GB</option>
                        <option value="50">50GB</option>
                    </select>
                </div>
            </div>
    {assign var='id' value=$viewobject->data->id}
            <input type="hidden" name="id" id="id" value="{$id}"/>
            <input type="hidden" name="offset" id="offset" value="{$offset}"/>
            <input type="hidden" name="search" id="search" value="{$search}"/>
            <input type="hidden" name="limit" id="limit" value="{$limit}"/>
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