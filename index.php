<?php
/**
 * PLUGIN NAME: Nitin Dashboard
 * DESCRIPTION: Shows reports on ARC trial data
 * VERSION: 1.0
 * AUTHOR: carl.w.reed@vumc.org
 */

// Call the REDCap Connect file in the main "redcap" directory
require_once "../../redcap_connect.php";
require_once "config.php";



use Vanderbilt\Nitin;

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
		], [
			"title" => "Screening",
			"filepath" => "html/screening.php"
		], [
			"title" => "Imaging",
			"filepath" => "html/imaging.php"
		]
	];
	
	public function __construct() {
		$this->pid = SUBJECT_PID;
		$this->project = new \Project($this->pid);
		$this->projEvents = \Event::getEventsByProject($this->pid);
		$this->baselineEID = array_search("Baseline", $this->projEvents);
		$this->enrollmentEID = array_search("Enrollment", $this->projEvents);
		$this->m1EID = array_search("1-Month", $this->projEvents);
		$this->m3EID = array_search("3-Months", $this->projEvents);
		$this->m6EID = array_search("6-Months", $this->projEvents);
		$this->m12EID = array_search("12-Months", $this->projEvents);
		$this->dags = $this->project->getGroups();
		$this->recordHome = substr(APP_PATH_WEBROOT_FULL, 0, -8) . APP_PATH_WEBROOT . "DataEntry/record_home.php?pid=" . SUBJECT_PID . "&id=";
		$this->imagingRecordHome = substr(APP_PATH_WEBROOT_FULL, 0, -8) . APP_PATH_WEBROOT . "DataEntry/record_home.php?pid=" . IMAGING_PID . "&id=";
		$this->screeningRecordHome = substr(APP_PATH_WEBROOT_FULL, 0, -8) . APP_PATH_WEBROOT . "DataEntry/index.php?pid=" . SCREENING_PID . "&page=screening_log&id=";
		
		// http://localhost/redcap/redcap_v8.10.2/DataEntry/index.php?pid=22&id=32841-792&page=screening_log
		
		// get labels for subject project
		$q = db_query("SELECT field_name, element_enum FROM redcap_metadata WHERE project_id=" . SUBJECT_PID);
		$this->subject_labels = [];
		while ($row = db_fetch_assoc($q)) {
			$this->subject_labels[$row['field_name']] = $row['element_enum'];
		}
		// get labels for screening project
		$q = db_query("SELECT field_name, element_enum FROM redcap_metadata WHERE project_id=" . SCREENING_PID);
		$this->screening_labels = [];
		while ($row = db_fetch_assoc($q)) {
			$this->screening_labels[$row['field_name']] = $row['element_enum'];
		}
		// get labels for imaging project
		$q = db_query("SELECT field_name, element_enum FROM redcap_metadata WHERE project_id=" . IMAGING_PID);
		$this->imaging_labels = [];
		while ($row = db_fetch_assoc($q)) {
			$this->imaging_labels[$row['field_name']] = $row['element_enum'];
		}
	}
	
	function labelizeValue($fieldName, $fieldValue, $labels = '') {
		if ($labels == '') $labels = $this->subject_labels;
		if ($fieldValue == '') return $fieldValue;
		if (isset($labels[$fieldName])) {
			$enum = $labels[$fieldName];
			preg_match_all("/$fieldValue, ([^\\\]+)/", $enum, $matches);
			if (isset($matches[1][0])) {
				# could return something like "Operative (2)"
				return $matches[1][0] . " ($fieldValue)";
			}
		}
		return $fieldValue;
	}
	
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
		// include_once("html/treatment_initiation.php");
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