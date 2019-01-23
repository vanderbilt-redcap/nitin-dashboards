<?php

global $dash;
$content = "";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Enrollment Documents table
$table = [
	"title" => "Enrollment Documents",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization Group", "Randomization date:", "These documents MUST be uploaded BY:"],
	"content" => [
		// ["4-3285-119", "3-months", "Piped from baseline qtk"],
		// ["10-2284-23", "6-months", "9/10/18"]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"events" => [$dash->enrollmentEID],
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['sdoc_initial_due'] <> '' and $data['sdoc_vumc_cert'] <> '1') {
			$row = [];
			$row[0] = $data['study_id'];
			$row[1] = $data['pati_6'];
			$row[2] = $data['randgroup'];
			$row[3] = $data['date'];
			$row[4] = $data['sdoc_initial_due'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Surgical Documents table
$table = [
	"title" => "Surgical Documents",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization date", "Actual Surgery Date:", "These documents MUST be uploaded BY:"],
	"content" => [
		// ["4-3285-119", "", ""],
		// ["10-2284-23", "", ""]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"events" => [$dash->enrollmentEID],
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['sdoc_vumc_cert_2'] <> '1' and $data['randgroup'] == '1' and $data['pati_x15'] <> '') {
			$row = [];
			$row[0] = $data['study_id'];
			$row[1] = $data['pati_6'];
			$row[2] = $data['date'];
			$row[3] = $data['pati_x15'];
			$row[4] = $data['sdoc_surgical_due'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Data Validation table
$table = [
	"title" => "Data Validation (Sites/Personnel)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Event", "Instance", "Information to be validated", "Resolution notes", "Date issue(s) discovered", "Contact 1 Date", "Contact 2 Date", "Contact 3 Date", "Contact 4 Date", "Contact 5 Date"],
	"content" => [
		// ["4-3285-119", "", "", "", "", ""],
		// ["10-2284-23", "", "", "9/10/18", "9/17/18", "9/24/18"]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['dval_action_needed'] == '1' and $data['dval_res_ra'] == '2') {
			$row = [];
			$row[0] = $record[$dash->enrollmentEID]['study_id'];
			$row[1] = $record[$dash->enrollmentEID]['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = '';
			$row[4] = $data['dval_specify_research'];
			$row[5] = $data['dval_res_notes'];
			$row[6] = $data['dval_issue_disc_date'];
			$row[7] = $data['dval_contact_date_1'];
			$row[8] = $data['dval_contact_date_2'];
			$row[9] = $data['dval_contact_date_3'];
			$row[10] = $data['dval_contact_date_4'];
			$row[11] = $data['dval_contact_date_5'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Delay of Treatment Protocol Deviations (to be recorded)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization group", "Date delay of treatment effective:", "Surgery scheduling status/notes: (if applicable:"],
	"content" => []
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['qtk_questionnaire_received'] == '1') {
			$row = [];
			$row[0] = $record[$dash->enrollmentEID]['study_id'];
			$row[1] = $record[$dash->enrollmentEID]['pati_6'];
			$row[2] = $data['patc_a2'];
			$row[3] = $data['patc_a3'];
			$row[4] = $dash->projEvents[$eid];
			$row[5] = $data['patc_timepoint'];
			$row[6] = $data['qtk_check_request_number'];
			$row[7] = $data['qtk_date_received'];
			$row[8] = $data['qtk_check_request_submitted'];
			$row[9] = $data['qtk_check_request_date'];
			$row[10] = $data['qtk_patient_paid'];
			$row[11] = $data['qtk_date_check_cleared'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Double Data Entry",
	"titleClass" => "blueHeader",
	"headers" => [],
	"content" => []
];
$content .= $dash->makeDataTable($table);


unset($table);