<?php

global $dash;
$content = "";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// UNVERIFIED
// # add Treating Physical Therapist Initial Info table
$table = [
	"title" => "Initial Treating PT Referral/Notification Needed",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Patient Info Release Sent?", "Site lead PT treating PT?"],
	"content" => [
		// ["4-3285-119", "", "", "", ""],
		// ["10-2284-23", "", "", "", ""]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"filterLogic" => "([baseline_arm_1][qtk_physical_therapy] = '1') AND ((([enrollment_arm_1][pti_lead_pt_is_treating_pt]= '0' AND [enrollment_arm_1][pti_contact_pt] <> '1') OR ([enrollment_arm_1][pti_lead_pt_is_treating_pt]= '1' AND [enrollment_arm_1][pti_patient_is_participant] <> '1')) OR ([enrollment_arm_1][pti_lead_pt_is_treating_pt]= ''))",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
// foreach ($records as $i => $record) {
	// foreach ($record as $eid => $data) {
		// if ($record[$baseline]) {
			// $row = [];
			// $row[0] = $record[$dash->enrollmentEID]['study_id'];
			// $row[1] = $record[$dash->enrollmentEID]['pati_6'];
			
			// # manually format this value
			// $leadPT = $record[$dash->enrollmentEID]['pti_lead_pt_is_treating_pt'];
			// $row[2] = $leadPT == '0' ? 'No (0)' : ($leadPT == '1' ? 'Yes (1)' : $leadPT);
			
			// $row[3] = $record[$dash->enrollmentEID]['pti_lpt_referral_date'];
			// $row[4] = $record[$dash->enrollmentEID]['pti_pt_contacted'];
			
			// $table['content'][] = $row;
		// }
	// }
// }
foreach ($records as $i => $record) {
	$row = [];
	$row[0] = $record[$dash->enrollmentEID]['study_id'];
	$row[1] = $record[$dash->enrollmentEID]['pati_6'];
	$row[2] = $record[$dash->enrollmentEID]['pti_release_sent_to_pt'];
	$row[3] = $record[$dash->enrollmentEID]['pti_lead_pt_is_treating_pt'];
	
	$table['content'][] = $row;
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Outstanding Lead PT Calls table
$table = [
	"title" => "Outstanding Lead PT Initial Calls/Verification",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Site Lead PT Treating PT?", "Date referred to lead PT", "Treating Physical therapist successfully contacted?"],
	"content" => [
		// ["4-3285-119", "", ""],
		// ["10-2284-23", "", ""]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	// "filterLogic" => "(([enrollment_arm_1][pti_patient_is_participant]='1') AND ([enrollment_arm_1][pti_receipt_of_protocol] = '')) OR (([enrollment_arm_1][pti_contact_pt] = '1') AND ([enrollment_arm_1][pti_pt_contacted] = ''))",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if (($data['pti_patient_is_participant'] == '1' and $data['pti_receipt_of_protocol'] == '') or ($data['pti_contact_pt'] == '1' and $data['pti_pt_contacted'] == '')) {
			$row = [];
			$row[0] = $record[$dash->enrollmentEID]['study_id'];
			$row[1] = $record[$dash->enrollmentEID]['pati_6'];
			
			# manually format this value
			$leadPT = $record[$dash->enrollmentEID]['pti_lead_pt_is_treating_pt'];
			$row[2] = $leadPT == '0' ? 'No (0)' : ($leadPT == '1' ? 'Yes (1)' : $leadPT);
			
			$row[3] = $record[$dash->enrollmentEID]['pti_lpt_referral_date'];
			$row[4] = $record[$dash->enrollmentEID]['pti_pt_contacted'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

// UNVERIFIED
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add PT Reports to send table
$table = [
	"title" => "PT Reports to send",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization Group", "Event", "Lower Window", "Ideal Date"],
	"content" => [
		// ["4-3285-119", "1-month", "10/30/18", "10/22/18"],
		// ["10-2284-23", "3-month", "10/30/18", ""]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"filterLogic" => "((([1month_arm_1][pttk_pt_report_sent] = '' OR [3months_arm_1][pttk_pt_report_sent] = '') AND ([enrollment_arm_1][pati_study_status]<>'0') AND ([1month_arm_1][pttk_pt_report_sent] = '' OR [3months_arm_1][pttk_pt_report_sent] = '')) AND ((([1month_arm_1][pttk_ideal_date_2] <= '2019-01-31' OR [3months_arm_1][pttk_ideal_date_2] <= '2019-01-31') AND ([enrollment_arm_1][randgroup] = '1') AND ([enrollment_arm_1][pati_x15] <> '')) OR (([1month_arm_1][pttk_ideal_date] <= '2019-01-31' OR [3months_arm_1][pttk_ideal_date] <= '2019-01-31') AND ([enrollment_arm_1][randgroup]= '2'))))",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		$row = [];
		$row[0] = $record[$dash->enrollmentEID]['study_id'];
		$row[1] = $record[$dash->enrollmentEID]['pati_6'];
		
		# formatting
		$rgroup = $record[$dash->enrollmentEID]['randgroup'];
		$row[2] = $rgroup == '1' ? 'Operative (1)' : ($rgroup == 2 ? 'Non-operative (2)' : $rgroup);
		
		$row[3] = $dash->projEvents[$eid];
		$row[4] = $rgroup == '1' ? $data['pttk_lower_window_2'] : ($rgroup == 2 ? $data['pttk_lower_window'] : '');
		$row[5] = $rgroup == '1' ? $data['pttk_ideal_date_2'] : ($rgroup == 2 ? $data['pttk_ideal_date'] : '');
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

// UNVERIFIED - event level issues
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add PT Reports (follow-up) table
$table = [
	"title" => "PT Reports (follow-up)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event", "Date sent:", "Contact 1 approx. date due:", "Contact 2 approx. date due:", "LPT contact approx. date due:", "LPT contact needed?"],
	"content" => []
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"filterLogic" => "([pttk_pt_report_completed] = '' OR [pttk_pt_report_completed] = '2') AND ([pttk_pt_report_sent] = '1') AND ([pttk_lead_pt_contact_made] = '') AND ([enrollment_arm_1][pati_study_status] <> '0')",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		$row = [];
		$row[0] = $record[$dash->enrollmentEID]['study_id'];
		$row[1] = $record[$dash->enrollmentEID]['pati_6'];
		$row[2] = $dash->projEvents[$eid];
		$row[3] = $data['pttk_date_pt_report_sent'];
		$row[4] = $data['pttk_contact_due_1'];
		$row[5] = $data['pttk_contact_due_2'];
		$row[6] = $data['pttk_contact_due_3'];
		$row[7] = $data['pttk_lead_pt_contact_needed'];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);


unset($table);