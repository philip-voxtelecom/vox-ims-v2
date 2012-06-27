<?php

if (!isset($_GET['file'])) {
    return;
}

$GLOBALS['documentroot'] = dirname(__FILE__);

$filename = $GLOBALS['documentroot'] . '/cache/' . $_GET['file'];
$blnDel = $_GET['rm'];

header('Content-type: plain/text');
header('Content-Length: ' . filesize($filename));
header('Content-Disposition: attachment; filename="' . $_GET['file'] . '"');

readfile($filename);

if (isset($blnDel)) {
    unlink($filename);
}
?>
