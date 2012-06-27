"Account","Name","Status"
{section name=name loop=$usernames}
"{$usernames[name].username}","{if isset($usernames[name].description)}{$usernames[name].description}{/if}","{$usernames[name].status|capitalize}"
{/section}

