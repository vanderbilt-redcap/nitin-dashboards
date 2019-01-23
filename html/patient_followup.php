<?php

global $dash;
$content = "";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Questionnaires To Send table
$table = [
	"title" => "Questionnaires To Send",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Lower Window", "Ideal Date", "Upper Window"],
	"content" => [
		// ["4-3285-119", "3-months", "Piped from baseline qtk", "Piped from baseline qtk", "Piped from baseline qtk"],
		// ["10-2284-23", "6-months", "9/10/18", "9/17/18", "9/24/18"]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	// "filterLogic" => "([qtk_lower_window] < '2019-01-31') AND ([qtk_questionnaire_sent] = '') AND ([enrollment_arm_1][date] <> '') AND ([qtk_lower_window] <> '') AND ([enrollment_arm_1][pati_study_status] <> '0')",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
$date1 = '2019-01-31';
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['qtk_lower_window'] < $date1 and $data['qtk_questionnaire_sent'] == '' and $record[$dash->enrollmentEID]['date'] <> '' and $data['qtk_lower_window'] <> '' and $record[$dash->enrollmentEID]['pati_study_status'] <> '0') {
			$row = [];
			$row[0] = $record[$dash->enrollmentEID]['study_id'];
			$row[1] = $record[$dash->enrollmentEID]['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = $data['qtk_lower_window'];
			$row[4] = $data['qtk_ideal_date'];
			$row[5] = $data['qtk_upper_window'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Physical Therapy Diaries to Send/Check table
// UNVERIFIED : not done yet, waiting for Kimberly
$table = [
	"title" => "Physical Therapy Diaries to Send/Check",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Diary type", "Event Name"],
	"content" => [
		// ["4-3285-119", "", ""]
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
		if ($data['qtk_timepoint'] <> '0' and
			$data['qtk_pi_call'] <> '1' and
			$data['pati_study_status'] <> '0' and
			$data['qtk_questionnaire_sent'] = '1' and
			($data['qtk_questionnaire_received'] == '' or $data['qtk_questionnaire_received'] == '2')) {
			// $row = [];
			// $row[0] = $record[$dash->enrollmentEID]['study_id'];
			// $row[1] = $record[$dash->enrollmentEID]['pati_6'];
			// $row[2] = $dash->projEvents[$eid];
			// $row[3] = $data['qtk_lower_window'];
			// $row[4] = $data['qtk_ideal_date'];
			// $row[5] = $data['qtk_upper_window'];
			
			// $table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Follow-up Calls (Outstanding Questionnaires) table
$table = [
	"title" => "Follow-up Calls (Outstanding Questionnaires)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Contact 1 approx. date due:", "Call 2 approx. date due", "Call 3 approx. date due", "PI referral approx. date due"],
	"content" => [
		// ["4-3285-119", "3-month", "Pipe from qtk", "Pipe from qtk", "Pipe from qtk", "Pipe from qtk"]
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
		if ($data['qtk_timepoint'] <> '0' and
			$data['qtk_pi_call'] <> '1' and
			$data['pati_study_status'] <> '0' and
			$data['qtk_questionnaire_sent'] == '1' and
			($data['qtk_questionnaire_received'] == '' or $data['qtk_questionnaire_received'] == '2')) {
			$row = [];
			$row[0] = $record[$dash->enrollmentEID]['study_id'];
			$row[1] = $record[$dash->enrollmentEID]['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = $data['qtk_call_due'];
			$row[4] = $data['qtk_call_due_2'];
			$row[5] = $data['qtk_call_due_3'];
			$row[6] = $data['qtk_call_due_4'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Follow-up Calls (Data Validation and/or Paper PT Diary) table
$table = [
	"title" => "Follow-up Calls (Data Validation and/or Missing Data Collection)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event", "Instance:", "Information to be validated", "Resolution notes", "Date issue(s) discovered", "Contact 1 Date", "Contact 2 Date", "Contact 3 Date", "Contact 4 Date", "Contact 5 Date"],
	"content" => [
		// ["4-3285-119", "", "1) ASES Qs 1-9 potentially reversed<br />2) Patient reports surgery @ site other than enrolling site", "", "", "", ""],
		// ["10-2284-23", "", "PT diary not rec'd with questionnaire or w/in 2 wks of E completion + SPADI Q 6 missing", "9/10/18", "9/17/18", "9/24/18", "9/31/18"]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	// "filterLogic" => "([dval_pat_contact_needed] = '1') AND ([dval_res_pat] = '2' OR [dval_res_pat] = '') AND ([pati_study_status] <> '0')",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['dval_pat_contact_needed'] == '1' and
			($data['dval_res_pat'] == '2' or $data['dval_res_pat'] == '') and
			$record[$dash->enrollmentEID]['pati_study_status'] <> '0') {
			$row = [];
			$row[0] = $record[$dash->enrollmentEID]['study_id'];
			$row[1] = $record[$dash->enrollmentEID]['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = ''; // how to get redcap_repeat_instance??
			$row[4] = $data['dval_specify_patient'];
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


unset($table);