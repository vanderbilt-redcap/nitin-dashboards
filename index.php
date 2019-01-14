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

# Practice ARC Subject Database on redcap PID: 73340
$pid = 63383;

$record = \REDCap::getData($pid, 'array', 1);
exit("<pre>" . print_r($record, true) . "</pre>");

class Dashboard {
	private $screens = [
		[
			"title" => "Summary and Notifications",
			"filepath" => "html/summary_and_notifications.php"
		], [
			"title" => "Treatment Initiation",
			"filepath" => "html/treatment_initiation.php"
		], [
			"title" => "Treating PT Follow-Up",
			"filepath" => "html/treating_pt_followup.php"
		], [
			"title" => "Patient Follow-Up",
			"filepath" => "html/patient_followup.php"
		], [
			"title" => "Financial",
			"filepath" => "html/financial.php"
		], [
			"title" => "Data Management",
			"filepath" => "html/data_management.php"
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
		include_once("html/summary_and_notifications.php");
		$html = str_replace("{CONTENT}", $content, $html);
		
		return $html;
	}
	
	public function makeDataTable($tableData) {
		# returns (string) HTML table
		# requires $tableData array with properties:
		#	titleClass => string
		#	title => string
		#	headers => 1D array of header values
		#	content => 2D array of the actual tabular data to be inserted
		$table = "
			<h2 class='" . $tableData['titleClass'] . "'>" . $tableData['title'] . "</h2>
			<table class='dataTable'>
				<thead>
					<tr>";
		foreach ($tableData['headers'] as $header) {
			$table .= "
						<th>$header</th>";
		}
		$table .= "
					</tr>
				</thead>
				<tbody>";
		foreach ($tableData['content'] as $row) {
			$table .= "
					<tr>";
			foreach ($row as $datum) {
				$table .= "
							<td>$datum</td>";
			}
			$table .= "
					</tr>";
		}
		$table .= "
				</tbody>
			</table>";
		
		return $table;
	}
	
	public function test($x) {
		return "<pre>$x\n</pre>";
	}
}

if (!$dash) {
	$dash = new Dashboard();
}
if (isset($_GET['screen'])) {
	echo $dash->getDashboard($_GET['screen']);
} else {
	echo $dash->init();
}