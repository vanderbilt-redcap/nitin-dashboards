<?php

global $dash;
$content = "";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Site Recruitment Compensation table
$table = [
	"title" => "Site Recruitment Compensation",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Site submit invoice?", "Date invoice received", "Date submitted", "Site successfully paid?"],
	"content" => [
		// ["Daisy Duck", "4-3285-119", "Baseline", "Piped from qtk", "Piped from qtk", "Piped from qtk", "Piped from qtk"],
		// ["Daffy Duck", "10-2284-23", "6-months", "9/10/18", "9/17/18", "Yes", "9/31/18"]
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
		$rgroup = $record[$dash->enrollmentEID]['randgroup'];
		if ($rgroup <> '') {
			$row = [];
			$row[0] = $record[$dash->enrollmentEID]['study_id'];
			$row[1] = $record[$dash->enrollmentEID]['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			if ($data['mritk_recruitment_invoice'] == '3') {
				$row[3] = 'pending (3)';
			} elseif ($data['mritk_recruitment_invoice'] == '2') {
				$row[3] = 'N/A (2)';
			} elseif ($data['mritk_recruitment_invoice'] == '1') {
				$row[3] = 'yes (1)';
			} else {
				$row[3] = $data['mritk_recruitment_invoice'];
			}
			$row[4] = $data['mritk_invoice_recd'];
			$row[5] = $data['mritk_invoice_submitted_date'];
			if ($data['mritk_invoice_vumc_paid'] == '1') {
				$row[6] = 'yes (1)';
			} else {
				$row[6] = $data['mritk_invoice_vumc_paid'];
			}
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Check Requests Due table
$table = [
	"title" => "Check Requests Due",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Today's Date", "Data entry method"],
	"content" => [
		// ["4-3285-119", "", "", ""],
		// ["10-2284-23", "", "", ""]
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
		if ($data['qtk_check_request_submitted'] == '' and $data['qtk_questionnaire_received'] == '1') {
			$row = [];
			$row[0] = $record[$dash->enrollmentEID]['study_id'];
			$row[1] = $record[$dash->enrollmentEID]['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = $data['patc_timepoint'];
			$row[4] = $data['patc_a1'];
			$row[5] = $data['dem_crf03_1'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Check Request Reconciliation table
$table = [
	"title" => "Check Request Reconciliation",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "First Name", "Last Name", "Event Name", "Date Questionnaire Received", "Check Request #", "Check request submitted?", "Check Request Submission date", "Patient successfully paid?", "Date payment processed (accounting)", "Date check cleared"],
	"content" => [
		// ["Daisy Duck", "4-3285-119", "Baseline", "Piped from qtk", "Piped from qtk", "Piped from qtk", "Piped from qtk", "Piped from qtk"],
		// ["Daffy Duck", "10-2284-23", "6-months", "9/10/18", "9/17/18", "Yes", "9/31/18", "9/19/18"]
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


unset($table);