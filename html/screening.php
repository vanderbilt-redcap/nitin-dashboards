<?php

global $dash;
$content = "";

$screening = [
	"project" => new \Project(SCREENING_PID),
	"events" => \Event::getEventsByProject(SCREENING_PID)
];

$screening['dags'] = $screening['project']->getGroups();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Pending MRIs",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Physician Name", "MRI Scheduled Date:", "Date patient scheduled to return to clinic:"],
	"content" => []
];
$params = [
	"project_id" => SCREENING_PID,
	"return_format" => 'array',
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
// exit("<pre>" . print_r($records, true) . "</pre>");
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['slg_d1'] == '2') {
			$row = [];
			$row[0] = $data['study_id'];
			$row[1] = $data['redcap_data_access_group'];
			$row[2] = $data['slg_dr_site_12'];
			$row[3] = $data['slg_d3'];
			$row[4] = $data['slg_d5'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Patients who need more time to decide",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Appointment date"],
	"content" => []
];
$params = [
	"project_id" => SCREENING_PID,
	"return_format" => 'array',
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['slg_f2a'] == '1') {
			$row = [];
			$row[0] = $data['study_id'];
			$row[1] = $data['redcap_data_access_group'];
			$row[2] = $data['slg_appointment_date'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Outstanding (incomplete) records",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Date of appointment", "Does patient have tear?", "Date MRI scheduled (if MRI Pending)", "Patient need more time to decide?"],
	"content" => []
];
$params = [
	"project_id" => SCREENING_PID,
	"return_format" => 'array',
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['screening_log_complete'] <> '2') {
			$row = [];
			$row[0] = $data['study_id'];
			$row[1] = $data['redcap_data_access_group'];
			$row[2] = $data['slg_appointment_date'];
			$row[3] = $data['slg_d1'];
			$row[4] = $data['slg_d3'];
			$row[5] = $data['slg_f2a'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

unset($table);