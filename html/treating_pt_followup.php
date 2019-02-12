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
	"title" => "Initial Treating PT Referral/Notification Needed",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Patient Info Release Sent?", "Site lead PT treating PT?"],
	"content" => []
];
foreach ($records as $i => $record) {
	$bdata = $record[$dash->baselineEID];
	$edata = $record[$dash->enrollmentEID];
	if(
	$bdata['qtk_physical_therapy'] == '1' and
	((($edata['pti_lead_pt_is_treating_pt'] == '0' and
	$edata['pti_contact_pt'] <> '1') or
	($edata['pti_lead_pt_is_treating_pt'] == '1' and
	$edata['pti_patient_is_participant'] <> '1')) or
	$edata['pti_lead_pt_is_treating_pt'] == '')
	) {
		$row = [];
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a>-" . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->labelizeValue('pti_release_sent_to_pt', $edata['pti_release_sent_to_pt']);
		
		$val = $edata['pti_lead_pt_is_treating_pt'];
		$row[3] = $val == '1' ? "Yes (1)" : ($val == '0' ? "No (0)" : $val);
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Outstanding Lead PT Initial Calls/Verification",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Site Lead PT Treating PT?", "Date referred to lead PT", "Treating Physical therapist successfully contacted?", "Contact Notes"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	if (
		($edata['pti_patient_is_participant'] == '1' and
		$edata['pti_receipt_of_protocol'] == '') or
		($edata['pti_contact_pt'] == '1' and 
		$edata['pti_pt_contacted'] == '')
	) {
		$row = [];
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a>-" . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		
		# manually format this value
		$leadPT = $edata['pti_lead_pt_is_treating_pt'];
		$row[2] = $leadPT == '0' ? 'No (0)' : ($leadPT == '1' ? 'Yes (1)' : $leadPT);
		
		$row[3] = $edata['pti_lpt_referral_date'];
		$row[4] = $edata['pti_pt_contacted'];
		$row[5] = $edata['pti_pt_contact_notes'];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "PT Reports to send",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization Group", "Event", "Lower Window", "Ideal Date"],
	"content" => []
];
$day30 = date('Y-m-d', strtotime('+30 days'));	# set target date to be 30 days from now
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$m1data = $record[$dash->m1EID];
	$m3data = $record[$dash->m3EID];
	foreach ($record as $eid => $data) {
		if (
			// logic from report "PT reports to send (combined-revised post dashboard draft)"
			// (([enrollment_arm_1][pati_study_status]<>'0') AND (([1month_arm_1][pttk_pt_report_sent] = "") AND ((([enrollment_arm_1][randgroup] = "2" ) AND ([1month_arm_1][pttk_ideal_date] <= "2019-02-28") AND ([1month_arm_1][pttk_ideal_date] <> "")) OR (([enrollment_arm_1][randgroup] = "1") AND  ([1month_arm_1][pttk_ideal_date_2] <= "2019-02-28") AND ([1month_arm_1][pttk_ideal_date_2] <> "")))) OR (([3months_arm_1][pttk_pt_report_sent] = "") AND ((([enrollment_arm_1][randgroup] = "2" ) AND ([3months_arm_1][pttk_ideal_date] <= "2019-02-28") AND ([3months_arm_1][pttk_ideal_date] <> "")) OR (([enrollment_arm_1][randgroup] = "1") AND  ([3months_arm_1][pttk_ideal_date_2] <= "2019-02-28") AND ([3months_arm_1][pttk_ideal_date_2] <> "")))))
			
			(($edata['pati_study_status']<>'0') and (($m1data['pttk_pt_report_sent'] == "") and ((($edata['randgroup'] == "2" ) and ($m1data['pttk_ideal_date'] <= $day30) and ($m1data['pttk_ideal_date'] <> "")) or (($edata['randgroup'] == "1") and  ($m1data['pttk_ideal_date_2'] <= $day30) and ($m1data['pttk_ideal_date_2'] <> "")))) or (($m3data['pttk_pt_report_sent'] == "") and ((($edata['randgroup'] == "2" ) and ($m3data['pttk_ideal_date'] <= $day30) and ($m3data['pttk_ideal_date'] <> "")) or (($edata['randgroup'] == "1") and  ($m3data['pttk_ideal_date_2'] <= $day30) and ($m3data['pttk_ideal_date_2'] <> ""))))) and ($eid == $dash->m1EID or $eid == $dash->m3EID)
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a>-" . $edata['study_id'];
			$row[1] = $edata['pati_6'];
			
			# formatting
			$rgroup = $edata['randgroup'];
			// $row[2] = $rgroup == '1' ? 'Operative (1)' : ($rgroup == 2 ? 'Non-operative (2)' : $rgroup);
			$row[2] = $this->labelizeValue("randgroup", $rgroup);
			
			$row[3] = $dash->projEvents[$eid];
			$row[4] = $rgroup == '1' ? $data['pttk_lower_window_2'] : ($rgroup == '2' ? $data['pttk_lower_window'] : '');
			$row[5] = $rgroup == '1' ? $data['pttk_ideal_date_2'] : ($rgroup == '2' ? $data['pttk_ideal_date'] : '');
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "PT Reports (follow-up)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event", "Date sent:", "Contact 1 approx. date due:", "Contact 2 approx. date due:", "LPT contact approx. date due:", "LPT contact needed?"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if (
		($data['pttk_pt_report_completed'] == '' or
		$data['pttk_pt_report_completed'] == '2') and
		$data['pttk_pt_report_sent'] == '1' and
		$data['pttk_lead_pt_contact_made'] == '' and
		$edata['pati_study_status'] <> '0'
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a>-" . $edata['study_id'];
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
}
$content .= $dash->makeDataTable($table);


unset($table);