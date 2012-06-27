<div style="display:none">
    <div id="account_list">
        <table id="userlist_tbl" class="detailTable" border=1>
            <tr><th>Account</th><th>Reference</th><th>Status</th></tr>
	{section name=name loop=$usernames}
            <tr id="listrow{$usernames[name].id}" class="{cycle values="odd,even"}">
                <td id="userlist_data1">{$usernames[name].username}</td>
                <td id="userlist_data2" style="width: 100%">{if isset($usernames[name].description)}{$usernames[name].description}{/if}</td>
                <td id="userlist_data5" >{$usernames[name].status|capitalize}</td>
            </tr>
	{/section}
        </table>
    </div>
</div>
