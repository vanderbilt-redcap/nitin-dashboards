<?php

global $dash;
$content = "";

// screening project variables
$screening = [
	"project" => new \Project(SCREENING_PID),
	"events" => \Event::getEventsByProject(SCREENING_PID)
];
$screening['dags'] = $screening['project']->getGroups();

# fetch records
$params = [
	"project_id" => SCREENING_PID,
	"events" => [$dash->enrollmentEID],
	"return_format" => 'array',
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);

$today = date('Y-m-d');

# fetch subject db info
// $subjectParams = [
	// "project_id" => SUBJECT_PID,
	// "return_format" => 'array',
	// "events" => [$dash->enrollmentEID],
	// "exportDataAccessGroups" => true
// ];
// $subjectRecords = \REDCap::getData($subjectParams);

// exit("<pre>" . print_r($subjectRecords, true) . "</pre>");

$dagNames = [
	"george_washington" => "George Washington University (Washington, DC)",
	"knoxville_orthoped" => "Knoxville Orthopedic Clinic (Knoxville)",
	"ohio_state_univers" => "Ohio State University (Columbus)",
	"ortho_institute_si" => "Ortho Institute (Sioux Falls)",
	"site_13" => "Site 13",
	"site_14" => "Site 14",
	"site_15" => "Site 15",
	"site_16" => "Site 16",
	"site_17" => "Site 17",
	"site_18" => "Site 18",
	"site_19" => "Site 19",
	"site_20" => "Site 20",
	"university_of_cali" => "University of California (San Francisco)",
	"university_of_colo" => "University of Colorado (Denver)",
	"university_of_iowa" => "University of Iowa (Iowa City)",
	"university_of_kent" => "University of Kentucky (Lexington)",
	"university_of_mich" => "University of Michigan (Ann Arbor)",
	"university_of_penn" => "University of Pennsylvania (Philadelphia)",
	"university_of_texa" => "University of Texas Southwestern (Dallas)",
	"vanderbilt_nashvil" => "Vanderbilt (Nashville)",
	"washington_univers" => "Washington University (St. Louis)"
];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Pending MRIs",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Physician Name", "Appointment Date", "MRI Scheduled Date:", "Date patient scheduled to return to clinic:", "Days since last recorded date:"],
	"content" => [],
	"attributes" => [
		"order-col" => 6,
		"order-direction" => "desc"
	]
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if ($data['slg_d1'] == '2' AND $data['site_name'] == "1") {
			$row = array_fill(0, count($table['headers']), "");
			$row[0] = "<a href = \"" . $dash->screeningRecordHome . $data['study_id'] . "\">" . $data['study_id'] . "</a>";
			$row[1] = $dagNames[$data['redcap_data_access_group']];
			
			$siteName = $data['site_name'];
			$row[2] = $dash->labelizeValue("slg_dr_site_$siteName", $data["slg_dr_site_$siteName"], $dash->screening_labels);
			$row[3] = $data['slg_appointment_date'];
			$row[4] = $data['slg_d3'];
			$row[5] = $data['slg_d5'];
			$row[6] = 0;
			
			$mostRecent = max($row[3], $row[4], $row[5]);
			if (!empty($mostRecent)) {
				$row[6] = date_diff(date_create($mostRecent), date_create($today))->format("%a");
				if ($today < $mostRecent)
					$row[6] *= -1;
			}
			
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Patients who need more time to decide",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Appointment date", "Days since last recorded date:"],
	"content" => [],
	"attributes" => [
		"order-col" => 3,
		"order-direction" => "desc"
	]
];
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['slg_f2a'] == '1' AND $data['site_name'] == "1") {
			$row = array_fill(0, count($table['headers']), "");
			$row[0] = "<a href = \"" . $dash->screeningRecordHome . $data['study_id'] . "\">" . $data['study_id'] . "</a>";
			$row[1] = $dagNames[$data['redcap_data_access_group']];
			$row[2] = $data['slg_appointment_date'];
			$row[3] = 0;
			
			if (!empty($row[2])) {
				$row[3] = date_diff(date_create($mostRecent), date_create($today))->format("%a");
				if ($today < $row[2])
					$row[3] *= -1;
			}
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Outstanding (incomplete) records",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Appointment Date", "Does patient have tear?", "Date MRI scheduled (if MRI Pending)", "Patient need more time to decide?", "Days since last recorded date:"],
	"content" => [],
	"attributes" => [
		"order-col" => 6,
		"order-direction" => "desc"
	]
];
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['screening_log_complete'] <> '2' AND $data['site_name'] == "1") {
			$row = array_fill(0, count($table['headers']), "");
			$row[0] = "<a href = \"" . $dash->screeningRecordHome . $data['study_id'] . "\">" . $data['study_id'] . "</a>";
			$row[1] = $dagNames[$data['redcap_data_access_group']];
			$row[2] = $data['slg_appointment_date'];
			$row[3] = $dash->labelizeValue('slg_d1', $data['slg_d1'], $dash->screening_labels);
			$row[4] = $data['slg_d3'];
			$row[5] = $data['slg_f2a'] == '1' ? 'Yes (1)' : ($data['slg_f2a'] == '0' ? "No (0)" : $data['slg_f2a']);
			$row[6] = 0;
			
			$mostRecent = max($row[2], $row[4]);
			if (!empty($mostRecent)) {
				$row[6] = date_diff(date_create($mostRecent), date_create($today))->format("%a");
				if ($today < $mostRecent)
					$row[6] *= -1;
			}
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

unset($table);