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
		], [
			"title" => "SAE/AE Monitoring",
			"filepath" => "html/sae_ae_monitoring.php"
		]
	];
	
	public function __construct() {
		llog("mem on construct: " . memory_get_usage(true) / 1000000);
		$this->pid = SUBJECT_PID;
		$this->project = new \Project($this->pid);
		
		// get project __SALT__ so we can download edocs
		$this->setProjectSalt();
		
		$this->projEvents = \Event::getEventsByProject($this->pid);
		$this->baselineEID = array_search("Baseline", $this->projEvents);
		$this->enrollmentEID = array_search("Enrollment", $this->projEvents);
		$this->m1EID = array_search("1-Month", $this->projEvents);
		$this->m3EID = array_search("3-Months", $this->projEvents);
		$this->m6EID = array_search("6-Months", $this->projEvents);
		$this->m12EID = array_search("12-Months", $this->projEvents);
		$this->otherEID = array_search("Other", $this->projEvents);
		
		$this->dags = $this->project->getGroups();
		$this->recordHome = SUBJECT_RECORD_URL;
		$this->imagingRecordHome = IMAGING_RECORD_URL;
		$this->screeningRecordHome = SCREENING_RECORD_URL;
		
		llog("after class instance declarations: " . memory_get_usage(true) / 1000000);
		
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
		
		llog("after get labels: " . memory_get_usage(true) / 1000000);
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
	
	function setProjectSalt() {
		global $__SALT__;
		$q = db_query("SELECT __SALT__ from redcap_projects where project_id=" . $this->pid);
		$row = db_fetch_assoc($q);
		$__SALT__ = $row['__SALT__'];
	}
	
	function init() {
		$html = self::getBaseHtml();
		
		llog("after get base HTML: " . memory_get_usage(true) / 1000000);
		
		// insert dashboard header/navbar
		$body = file_get_contents("html/dashboard.html");
		$html = str_replace("{BODY}", $body, $html);
		
		// insert Summary and Notifications screen
		include_once("html/summary_and_notifications.php");
		// include_once("html/treatment_initiation.php");
		$html = str_replace("{CONTENT}", $content, $html);
		
		
		llog("before init return: " . memory_get_usage(true) / 1000000);
		
		return $html;
	}
	
	public function makeDataTable($tableData) {
		# returns (string) HTML table
		# requires $tableData array with properties:
		#	titleClass => string
		#	title => string
		#	headers => 1D array of header values
		#	content => 2D array of the actual tabular data to be inserted
		#	attributes => assoc array
		
		$attr_string = "";
		foreach ($tableData['attributes'] as $attr => $val) {
			$attr_string .= " data-$attr='$val'";
		}
		
		$table = "
			<h2 class='" . $tableData['titleClass'] . "'>" . $tableData['title'] . "</h2>
			<table class='dataTable'$attr_string>
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
				if (strpos($datum, '</a>') !== false) {
					preg_match_all("/>(\d+)<.a>/", $datum, $matches);
					$enrollmentID = $matches[1][0];
					$table .= "
							<td data-sort='$enrollmentID'>$datum</td>";
				} else {
					$table .= "
							<td>$datum</td>";
				}
			}
			$table .= "
					</tr>";
		}
		$table .= "
				</tbody>
			</table>";
		
		return $table;
	}
	
	public function makeDDETable($tableData) {
		# returns (string) HTML table
		# requires $tableData array with properties:
		$table = "
			<h2 class='" . $tableData['titleClass'] . "'>" . $tableData['title'] . "</h2>
			<table class='dataTable'>
				<thead>
					<tr>";
		foreach ($tableData['headers'] as $i => $header) {
			if ($i == 0) {
			$table .= "
						<th>$header</th>";
			} else {
			$table .= "
						<th colspan=\"2\">$header</th>";
			}
		}
		$table .= "
					</tr>
					<tr>";
		foreach ($tableData['headers2'] as $i => $header) {
			$table .= "
						<th>$header</th>";
		}
		$table .= "
					</tr>
				</thead>
				<tbody>";
		foreach ($tableData['content'] as $i => $row) {
			$table .= "
					<tr>";
			foreach ($row as $j => $datum) {
				$classText = "";
				if ($tableData["css"][$i][$j] == 1)
					$classText = "class=\"DDEDouble\"";
				
				if (strpos($datum, '</a>') !== false) {
					preg_match_all("/>(\d+)<.a>/", $datum, $matches);
					$enrollmentID = $matches[1][0];
					$table .= "
							<td $classText data-sort='$enrollmentID'>$datum</td>";
				} else {
					$table .= "
							<td $classText>$datum</td>";
				}
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
	llog("after dash instantiation: " . memory_get_usage(true) / 1000000);
}
if (isset($_GET['screen'])) {
	llog("before get screen: " . memory_get_usage(true) / 1000000);
	echo $dash->getDashboard($_GET['screen']);
	llog("after get screen: " . memory_get_usage(true) / 1000000);
} else {
	echo $dash->init();
}