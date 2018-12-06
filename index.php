<?php
/**
 * PLUGIN NAME: Nitin Dashboard
 * DESCRIPTION: Shows reports on ARC trial data
 * VERSION: 1.0
 * AUTHOR: carl.w.reed@vumc.org
 */

// Call the REDCap Connect file in the main "redcap" directory
require_once "../../redcap_connect.php";

$html = file_get_contents(dirname(__FILE__) . "\html\base.html");
$html = str_replace("{STYLESHEET}", dirname(__FILE__) . "\css\base.css", $html);
$html = str_replace("{JAVASCRIPT}", dirname(__FILE__) . "\js\base.js", $html);
$html = str_replace("{TITLE}", "Base HTML", $html);
$html = str_replace("{BODY}", "<h3>BASE</h3>", $html);

echo $html;