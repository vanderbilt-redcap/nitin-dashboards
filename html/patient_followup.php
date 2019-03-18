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
			// logic pulled from "Questionnaires to send" report
			($data['qtk_lower_window'] < $day30) AND
			($data['qtk_questionnaire_sent'] == "") AND
			($edata['date'] <> "") AND
			($data['qtk_lower_window'] <> "") AND
			($edata['pati_study_status'] <> "0")
			
			// $data['qtk_lower_window'] < $date1 and
			// $data['qtk_questionnaire_sent'] == '' and
			// $edata['date'] <> '' and
			// $data['qtk_lower_window'] <> '' and
			// $edata['pati_study_status'] <> '0'
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
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
	"title" => "Physical Therapy Diaries to Check/Send",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Diary type", "Event Name"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$m1data = $record[$dash->m1EID];
	$m3data = $record[$dash->m3EID];
	$m6data = $record[$dash->m6EID];
	foreach ($record as $eid => $data) {
		if(
			// logic from "Physical therapy diary checks" report
			// ([enrollment_arm_1][pati_14] = "" OR (([1month_arm_1][pttk_diary_check] <> "1") AND ([1month_arm_1][pttk_pt_report_sent] <> "")) OR (([3months_arm_1][qtk_questionnaire_sent_2] = "") AND ([3months_arm_1][qtk_questionnaire_sent] = "1")) OR (([6months_arm_1][qtk_questionnaire_sent_2] = "") AND ([6months_arm_1][qtk_questionnaire_sent] = "1")))
			// ([pttk_diary_check] = "" OR [qtk_questionnaire_received_2] = "") AND ([qtk_questionnaire_sent] = "1") AND ([patc] <> "1")
			
			($edata['pati_14'] == "" or 
			($m1data['pttk_diary_check'] <> "1") and 
			($m1data['pttk_pt_report_sent'] <> "")) or 
			(($m3data['qtk_questionnaire_sent_2'] == "") and 
			($m3data['qtk_questionnaire_sent'] == "1")) or 
			(($m6data['qtk_questionnaire_sent_2'] == "") and 
			($m6data['qtk_questionnaire_sent'] == "1")))
			
			// old:
			// ($data['pttk_diary_check'] == "" or
			// $data['qtk_questionnaire_received_2'] == "") and
			// ($data['qtk_questionnaire_sent'] == "1") and
			// ($data['patc'] <> "1")
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $edata['pati_6'];
			$row[2] = $dash->labelizeValue('pati_14', $edata['pati_14']);
			$row[3] = $dash->projEvents[$eid];
			
			$table['content'][] = $row;
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
			// logic from "Follow up calls (patient questionnaire)"
			// ([qtk_timepoint] <> "0") AND
			// ([qtk_pi_call] <> "1") AND
			// ([pati_study_status] <> "0") AND
			// ([qtk_questionnaire_sent] = "1") AND
			// ([qtk_questionnaire_received] = "" OR
			// [qtk_questionnaire_received] = "2")
			
			($data['qtk_timepoint'] <> "0") and
			($data['qtk_pi_call'] <> "1") and
			($data['pati_study_status'] <> "0") and
			($data['qtk_questionnaire_sent'] == "1") and
			($data['qtk_questionnaire_received'] == "" or
			$data['qtk_questionnaire_received'] == "2")
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $edata['pati_6'];
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

# diagnostics
// $out = [];
// $out['$datatype'] = gettype($records[14]['repeat_instances'][82]['data_collectionvalidation'][1]);
// $out['baselineEID'] = $dash->baselineEID;
// $out['record14'] = $records[14];
// exit("<pre>" . print_r($out, true) . "</pre>");

foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record['repeat_instances'] as $eid => $eventData) {
		foreach ($eventData['data_collectionvalidation'] as $repeatInstanceIndex => $data) {
			if (
				$data['dval_pat_contact_needed'] == '1' and
				($data['dval_res_pat'] == '2' or $data['dval_res_pat'] == '') and
				$data['pati_study_status'] <> '0'
			) {
				$row = [];
				$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
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
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Physical Therapy Diary Monitoring (Questionnaire received but physical therapy diary still outstanding)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Diary Type", "Event Name", "Date Questionnaire Received:"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$m3data = $record[$dash->m3EID];
	$m6data = $record[$dash->m6EID];
	foreach ($record as $eid => $data) {
		if (
			// logic from "PT diary follow-up"
			(($data['qtk_questionnaire_received'] == "1") and
			($data['qtk_questionnaire_received_2'] == "2") and
			($data['qtk_questionnaire_sent_2'] == "1")) or
			(($data['qtk_questionnaire_received'] == "1") and
			($data['qtk_questionnaire_received_2'] == "2") and
			($data['qtk_questionnaire_sent_2'] == "1")) or
			(($data['mycap_completion'] == "") and
			($data['qtk_questionnaire_sent_2'] == "2")) or
			(($data['mycap_completion'] == "") and
			($data['qtk_questionnaire_sent_2'] == "2"))
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $edata['pati_6'];
			$row[2] = $dash->labelizeValue('pati_14', $edata['pati_14']);
			$row[3] = $dash->projEvents[$eid];
			$row[4] = $data['qtk_date_received'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

unset($table);