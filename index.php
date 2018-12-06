<?php
/**
 * PLUGIN NAME: Nitin Dashboard
 * DESCRIPTION: Shows reports on ARC trial data
 * VERSION: 1.0
 * AUTHOR: carl.w.reed@vumc.org
 */

// Call the REDCap Connect file in the main "redcap" directory
require_once "../../redcap_connect.php";

# get base HTML and substitute file paths to css and js files
$html = file_get_contents(dirname(__FILE__) . "\html\base.html");
$html = str_replace("{STYLESHEET}", dirname(__FILE__) . "\css\base.css", $html);
$html = str_replace("{JQUERY}", dirname(__FILE__) . "\js\jquery-3.3.1.min.js", $html);
$html = str_replace("{POPPER}", dirname(__FILE__) . "\js\popper.min.js", $html);
$html = str_replace("{BOOTSTRAP}", dirname(__FILE__) . "\js\bootstrap.min.js", $html);
$html = str_replace("{JAVASCRIPT}", dirname(__FILE__) . "\js\base.js", $html);

$html = str_replace("{TITLE}", "Plugin Template", $html);
$html = str_replace("{BODY}", "<h3>TEMPLATE</h3>", $html);

echo $html;