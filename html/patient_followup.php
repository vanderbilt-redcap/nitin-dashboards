<?php

global $dash;
$content = "";
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Questionnaires To Send",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Lower Window", "Ideal Date", "Upper Window"],
	"content" => []
];
$day30 = date('Y-m-d', strtotime('+30 days'));	# set target date to be 30 days from now
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if (
		$data['qtk_lower_window'] < $date1 and
		$data['qtk_questionnaire_sent'] == '' and
		$edata['date'] <> '' and
		$data['qtk_lower_window'] <> '' and
		$edata['pati_study_status'] <> '0'
		) {
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
$table = [
	"title" => "Physical Therapy Diaries to Send/Check",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Diary type", "Event Name"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if(true) {
			# table on hold til kimberly done
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Follow-up Calls (Outstanding Questionnaires)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Contact 1 approx. date due:", "Call 2 approx. date due", "Call 3 approx. date due", "PI referral approx. date due"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if (
		$data['qtk_timepoint'] <> '0' and
		$data['qtk_pi_call'] <> '1' and
		$data['pati_study_status'] <> '0' and
		$data['qtk_questionnaire_sent'] == '1' and
		($data['qtk_questionnaire_received'] == '' or $data['qtk_questionnaire_received'] == '2')
		) {
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
$table = [
	"title" => "Follow-up Calls (Data Validation and/or Missing Data Collection)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event", "Instance:", "Information to be validated", "Resolution notes", "Date issue(s) discovered", "Contact 1 Date", "Contact 2 Date", "Contact 3 Date", "Contact 4 Date", "Contact 5 Date"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if (
		$data['dval_pat_contact_needed'] == '1' and
		($data['dval_res_pat'] == '2' or $data['dval_res_pat'] == '') and
		$edata['pati_study_status'] <> '0'
		) {
			$row = [];
			$row[0] = $edata['study_id'];
			$row[1] = $edata['pati_6'];
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