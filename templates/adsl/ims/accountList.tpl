<div class="titleLabel">Account List</div>
<table>
    <td>
        <form id="search_f" action="javascript:void(null);" onsubmit="window.prevline = null; searchfilter=xajax.$('account_filter').value; xajax_accountView('listall',{ldelim}offset: 0, limit: {$limit}, search: searchfilter{rdelim});"><input id="account_filter" name="account_filter" style="margin-top:2px; margin-bottom: 2px; border: none; background-color: #EEEEEE; width: 7em;margin-left: 1em;" type="text" value="{if $search == '%'}{else}{$search}{/if}"/><input style="border: 1px solid gray; margin-left: 1em; " type="submit" value="Search"></form>
    </td>
    <td>
        <button class="detailButton" type="button" style="margin: 0px;" onclick="xajax_accountView('listall',{ldelim}offset: 0, limit: {$limit}, search: ''{rdelim});">Display all</button>
    </td>
</table>

{if !empty($accounts)}
    <div style="position: absolute; overflow: scroll; height: 75%; width: 495px;">
        <table id="list_tbl" class="sortable">
            <thead>
                <tr><th id="account" class="noedit text sortfirstasc">Account</th><th id="description" class="noedit text">Description</th><th id="status" class="noedit">Status</th></tr>
            </thead>
            <tbody>

                {section name=name loop=$accounts}
                    <tr id="listrow{$accounts[name].id}"
                        onclick="if ( window.prevline )
                        {ldelim}
                            xajax.$(window.prevline).className = window.prevlineclass;
                        {rdelim};
                            window.prevlineclass = this.className;
                            window.prevline = this.id;
                            this.className = 'selected_row';
                            viewarray={ldelim}id:{$accounts[name].id},search:'{$search}',offset:{$offset},limit:{$limit}{rdelim};
                            xajax_accountView('actions',viewarray);
                            new Effect.Pulsate('right_bar_content', {ldelim} pulses: 1,duration: 0.5,from: 0.4 {rdelim});"
                        >

                        <td style="max-width: 13em; overflow: hidden;">{$accounts[name].username}</td>
                        <td style="width: 100%">{if isset($accounts[name].description)}{$accounts[name].description}{/if}</td>
                        <td>{$accounts[name].status}</td>
                    </tr>
                {/section}

            </tbody>
        </table>
    </div>
{/if}