<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>{$title|default:"no title"}</title>

        <link rel = 'stylesheet' href = '/css/themes/{$theme}/default.css' type = 'text/css' />
        <link rel = 'stylesheet' href = '/css/validation.css' type = 'text/css' />
        <link rel = 'stylesheet' href = '/css/formwizard.css' type = 'text/css' />
        <link rel = 'stylesheet' href = '/css/jquery-ui.css' type = 'text/css' />


        <script type="text/javascript" src="/libs/smartmenus/c_config.js"></script>
        <script type="text/javascript" src="/libs/smartmenus/c_smartmenus.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/prototype/1.7.1/prototype.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/scriptaculous/1.8.2/effects.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="/libs/validation/fabtabulous.js"></script>
        <script type="text/javascript" src="/libs/validation/validation.js"></script>
        <script type="text/javascript" src="/libs/formwizard/formwizard.js"></script>
        <script type="text/javascript" src="/libs/tablekit.js"></script>
        <script type="text/javascript" src="/libs/overlib/overlib.js"></script>
        <script type="text/javascript" src="/libs/common.js"></script>

  {if isset($xajax_javascript)}
    {$xajax_javascript}
  {/if}

    </head>
    <body onLoad="initLoading();">
