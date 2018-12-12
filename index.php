<?php
/**
 * PLUGIN NAME: Nitin Dashboard
 * DESCRIPTION: Shows reports on ARC trial data
 * VERSION: 1.0
 * AUTHOR: carl.w.reed@vumc.org
 */

// Call the REDCap Connect file in the main "redcap" directory
require_once "../../redcap_connect.php";

use Vanderbilt\Nitin;

class Dashboard {
	private $screens = [
		[
			"title" => "Summary and Notifications",
			"filepath" => "html/summary.php"
		],
		[
			"title" => "Treatment Initiation",
			"filepath" => "html/treatment.php"
		]
	];
	
	function getBaseHtml() {
		# get base HTML and substitute file paths to css and js files
		$html = file_get_contents("html/base.html");
		$html = str_replace("{STYLESHEET}", "css/base.css", $html);
		// $html = str_replace("{JQUERY}", "js/jquery-3.3.1.slim.min.js", $html);
		$html = str_replace("{JQUERY}", "https://code.jquery.com/jquery-3.3.1.min.js", $html);
		$html = str_replace("{JAVASCRIPT}", "js/base.js", $html);
		$html = str_replace("{TITLE}", "ARC Trial - Nitin Dashboards", $html);
		return $html;
	}
	
	function getDashboard($screen) {
		foreach ($this->screens as $i => $info) {
			if ($info['title'] == $screen) {
				include($info['filepath']);
				return $content;
			}
		}
	}
	
	function init() {
		$html = self::getBaseHtml();
		
		// insert dashboard header/navbar
		$body = file_get_contents("html/dashboard.html");
		$html = str_replace("{BODY}", $body, $html);
		
		// insert Summary and Notifications screen
		include("html/summary.php");
		$html = str_replace("{CONTENT}", $content, $html);
		
		return $html;
	}
}

$dash = new Dashboard();
if (isset($_GET['screen'])) {
	echo $dash->getDashboard($_GET['screen']);
} else {
	echo $dash->init();
}