{assign var='cap' value=$viewobject->data->cap|string_format:"%s"}

<div id="account_topup">
    <div class="titleLabel">Account Topup</div>
    <table class="detailTable">
        <tr>
            <td class="label">Account Bundle Amount</td><td class="detail">{$cap/1000000000}GB</td>
        </tr>
{if isset($viewobject->data->topup) and $viewobject->data->topup != "null"}{assign var=currenttopup value=$viewobject->data->topup|string_format:"%s" }{else}{assign var=currenttopup value=0 }{/if}
{assign var=thisdate value=$smarty.now|date_format:"%Y/%m"}
{if $currenttopup <> 0 and $thisdate == $viewobject->data->topup_expire}
        <tr>
            <td class="label">Current Topup Amount</td><td class="detail">{$currenttopup/1000000000}GB</td>
        </tr>
        <tr>
    {assign var='total' value=$viewobject->data->total|string_format:"%s"}
            <td class="label">Total Bundle Amount</td><td class="detail">{$total/1000000000}GB</td>
        </tr>
        <tr>
            <td class="label">Topup Validity</td><td class="detail">{$viewobject->data->topup_expire}</td>
        </tr>
{/if}
    </table>
    <form action="#" name="accountTopupForm" id="accountTopupForm" method="post">
        <div class="detailTable">
            <div class="form-row">
                <div class="field-label"><label for="_topup">Please select amount to be topped up by*</label>:</div>
                <div class="field-widget">
                    <select id="_topup" name="_topup" class="validate-selection" title="Choose your topup amount"
                            onchange="
                      topup = this.value;
                      xajax.$('pro_topup').innerHTML=topup/1000000000 + 'GB';
                      xajax.$('pro_total').innerHTML=(Number(topup)+Number({$cap})+Number({$currenttopup}))/1000000000 + 'GB';
                      xajax.$('_save_detail_topup').value=Number(topup)+Number({$currenttopup});
                      xajax.$('topup_summary').style.display='block';

                            ">
                        <option value="null">None</option>
                        <option value="1000000000">1GB</option>
                        <option value="2000000000">2GB</option>
                        <option value="5000000000">5GB</option>
                        <option value="10000000000">10GB</option>
                        <option value="25000000000">25GB</option>
                        <option value="50000000000">50GB</option>
                    </select>
                </div>
            </div>
    {assign var='id' value=$viewobject->data->id}
            <input type="hidden" name="id" id="id" value="{$id}"/>
            <input type="hidden" name="_save_detail_topup" id="_save_detail_topup" value="0"/>
        </div>
        <button type="button" class="detailButton"
                onclick="var valid = new Validation('accountTopupForm');
             if (!valid.validate()) return false;
             xajax_accountTopupSubmit(xajax.getFormValues('accountTopupForm'));">
            Topup
        </button>
    </form>
    <div id="topup_summary" style="display: none; margin-top: 40px;">
        <div class="titleLabel">Proposed Topup Summary</div>
        <table class="detailTable">
            <tr>
                <td class="label">Account Bundle Amount</td><td class="detail">{$cap/1000000000}GB</td>
            </tr>
            <tr>
                <td class="label">Current Topup Amount</td><td class="detail">{$currenttopup/1000000000}GB</td>
            </tr>
            <tr>
                <td class="label">New Topup Amount</td><td class="detail" id="pro_topup"></td>
            </tr>
            <tr>
                <td class="label">New Total Bundle Amount</td><td class="detail" id="pro_total"></td>
            </tr>
            <tr>
                <td class="label">Topup Validity</td><td class="detail">{$thisdate}</td>
            </tr>
        </table>

    </div>

    <div id="topup history">
    </div>
</div>