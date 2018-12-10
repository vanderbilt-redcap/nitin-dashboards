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
$html = file_get_contents("html/base.html");
$html = str_replace("{STYLESHEET}", "css/base.css", $html);
$html = str_replace("{JQUERY}", "js/jquery-3.3.1.slim.min.js", $html);
$html = str_replace("{POPPER}", "js/popper.min.js", $html);
$html = str_replace("{BOOTSTRAP}", "js/bootstrap.min.js", $html);
$html = str_replace("{JAVASCRIPT}", "js/base.js", $html);

$html = str_replace("{TITLE}", "ARC Trial - Nitin Dashboards", $html);

$body = file_get_contents("html/one.html");
$html = str_replace("{BODY}", $body, $html);

echo $html;