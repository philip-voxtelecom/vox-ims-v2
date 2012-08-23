{assign var=account value=$data.account}
{assign var=usage value=$data.usage}
<html>
    <body>
        Usage report for {$account.username} for {$data.year}/{$data.month}<br/>
        <br/>
        Downloads: {$usage.downloads}<br/>
        Uploads: {$usage.uploads}<br/>
        Total: {$usage.total}<br/>
    </body>
</html>