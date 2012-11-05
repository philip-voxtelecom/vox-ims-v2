Username,Uploads GB,Downloads GB,Total GB,
        {foreach from=$usage key=k item=v}
{$v.username},{assign var=uploads value=`$v.uploads/1073741824`}{$uploads|string_format:"%.2f"},{assign var=downloads value=`$v.downloads/1073741824`}{$downloads|string_format:"%.2f"},{assign var=total value=`$v.total/1073741824`}{$total|string_format:"%.2f"}
        {/foreach}



