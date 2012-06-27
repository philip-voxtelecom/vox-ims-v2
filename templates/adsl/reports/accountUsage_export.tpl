Account,Status,Total Usage,Bundle,Excess
{foreach from=$viewobject->xpath('//data/record') key=node item=data}
{$data->user},{$data->status},{$data->total},{$data->bundle},{$data->excess}
{/foreach}
