{include file="header.tpl" title="Internet Management System"}

<div id="overDiv" style="position:absolute; visibility:hidden; z-index:2000;"></div>

<div id="header" class="logo"></div>

<div id="menu_bar" class="clearFix">
    <div id="loading" style="background-color: darkred; color: white; font-weight: bold; padding: 2px 4px; position: absolute; display: none;left: 50%; width: 5em;">Loading...</div>
    <div style="width: 90%">{if isset($menu_bar)}{$menu_bar}{/if}</div>
    <span style="float: right; color: lightgray; margin-right: 15px; margin-top: 3px;">{if isset($VERSION)}v{$VERSION}{/if}</span>
</div>

<div id="breadcrumbs" class="">{if isset($breadcrumbs)}{$breadcrumbs}{/if}</div>

<div id="error_bar"   class="">{if isset($error_bar)}{$error_bar}{/if}</div>

<div id="left_bar"    class="wrapper">
  {if isset($left_bar)}{$left_bar}{/if}
</div>

<div id="content"     class="wrapper">{if isset($content)}{$content}{/if}</div>

<div id="right_bar"   class="wrapper">
    <div id="right_bar_content">{if isset($right_bar_content)}{$right_bar_content}{/if}</div>
</div>

<div id="status_bar">{if isset($status_bar)}{$status_bar}{/if}</div>

<div id="popup"></div>

<div id="footer" class="footer"></div>

{include file="footer.tpl"}
