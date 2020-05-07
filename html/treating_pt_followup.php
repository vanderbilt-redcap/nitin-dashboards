<?php

global $dash;
$content = "";
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"fields" => [
		'pti_lpt_referral_date',
		'pttk_contact_due_3',
		'enrollment_id',
		'qtk_physical_therapy',
		'pati_study_status',
		'pti_pt_contacted',
		'pati_6',
		'pttk_date_pt_report_sent',
		'randgroup',
		'pti_lead_pt_is_treating_pt',
		'pttk_contact_due_2',
		'pttk_contact_due_1',
		'pttk_lead_pt_contact_needed',
		'3months_arm_1',
		'pttk_ideal_date',
		'pttk_pt_report_completed',
		'pti_release_sent_to_pt',
		'pti_contact_pt',
		'pttk_ideal_date',
		'pttk_pt_report_sent',
		'randgroup',
		'pttk_pt_report_sent',
		'pttk_ideal_date_2',
		'pttk_lower_window',
		'pttk_lower_window_2',
		'enrollment_arm_1',
		'pti_pt_contact_notes',
		'study_id',
		'pttk_lead_pt_contact_made',
		'pttk_ideal_date_2',
		'1month_arm_1',
		'pati_study_status',
		'pti_patient_is_participant',
		'pti_receipt_of_protocol',
		'pti_arm_x',
		'pti_lead_pt_is_treating_pt_x',
		'pti_contact_pt_x',
		'pti_patient_is_participant_x',
		'pti_release_sent_to_pt_x',
		'pti_pt_contacted_x',
		'pttk_pt_rep_sent_x_r',
		'pttk_pt_rep_completed_x_r',
		'pttk_date_pt_rep_sent_x_r',
		'pttk_contact_due_1_x_r',
		'pttk_contact_due_2_x_r',
		'pttk_contact_due_3_x_r',
		'pttk_lpt_cont_needed_x_r',
		'pti_date_discharge_x',
		'pttk_pt_report_sent_notes'
	],
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
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
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
	"headers" => ["Study ID", "DAG", "Site Lead PT Treating PT?", "Date referred to lead PT", "Treating Physical therapist successfully contacted?", "Contact Notes", "Days since referred:"],
	"content" => [],
	"attributes" => [
		"order-col" => 6,
		"order-direction" => "desc"
	]
];
$today = date('Y-m-d');
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	if (
		($edata['pti_patient_is_participant'] == '1' and
		$edata['pti_receipt_of_protocol'] == '') or
		($edata['pti_contact_pt'] == '1' and 
		$edata['pti_pt_contacted'] == '')
	) {
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		
		# manually format this value
		$leadPT = $edata['pti_lead_pt_is_treating_pt'];
		$row[2] = $leadPT == '0' ? 'No (0)' : ($leadPT == '1' ? 'Yes (1)' : $leadPT);
		$row[3] = $edata['pti_lpt_referral_date'];
		$row[4] = $edata['pti_pt_contacted'];
		$row[5] = $edata['pti_pt_contact_notes'];
		$row[6] = 0;
		
		if (!empty($row[3])) {
			$row[6] = date_diff(date_create($row[3]), date_create($today))->format("%a");
			if ($today < $row[3])
				$row[6] *= -1;
		}
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "PT Reports to send",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization Group", "Event", "Ideal Date", "Days since ideal date", "Notes"],
	"content" => [],
	"attributes" => [
		"order-col" => 5,
		"order-direction" => "desc"
	]
];
$day30 = date('Y-m-d', strtotime('+30 days'));	# set target date to be 30 days from now
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$m1data = $record[$dash->m1EID];
	$m3data = $record[$dash->m3EID];
	$m6data = $record[$dash->m6EID];
	$m1bool = (($m1data['pttk_pt_report_sent'] == "") and 
		((($edata['randgroup'] == "2" ) and 
		($m1data['pttk_ideal_date'] <= $day30) and 
		($m1data['pttk_ideal_date'] <> "")) or 
		(($edata['randgroup'] == "1") and 
		($m1data['pttk_ideal_date_2'] <= $day30) and 
		($m1data['pttk_ideal_date_2'] <> ""))));
	$m3bool = (($m3data['pttk_pt_report_sent'] == "") and 
		((($edata['randgroup'] == "2" ) and 
		($m3data['pttk_ideal_date'] <= $day30) and 
		($m3data['pttk_ideal_date'] <> "")) or 
		(($edata['randgroup'] == "1") and 
		($m3data['pttk_ideal_date_2'] <= $day30) and 
		($m3data['pttk_ideal_date_2'] <> ""))));
	$m6bool = (($m6data['pttk_pt_report_sent'] == "") and 
		((($edata['randgroup'] == "2" ) and 
		($m6data['pttk_ideal_date'] <= $day30) and 
		($m6data['pttk_ideal_date'] <> "")) or 
		(($edata['randgroup'] == "1") and 
		($m6data['pttk_ideal_date_2'] <= $day30) and 
		($m6data['pttk_ideal_date_2'] <> ""))) and
		($m1data['pttk_pt_report_sent'] == "0" or
		$m3data['pttk_pt_report_sent'] == "0" or
		$m1data['ptr_a2'] == "0" or
		$m3data['ptr_a2'] == "0"));
	
	if (
		($edata['pati_study_status']<>'0') and ($m1bool or $m3bool or $m6bool)
	) {
		if ($m1bool) {$data = $m1data;}
		elseif ($m3bool) {$data = $m3data;}
		elseif ($m6bool) {$data = $m6data;}
		
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		
		# formatting
		$rgroup = $edata['randgroup'];
		$row[2] = $this->labelizeValue("randgroup", $rgroup);
		
		if ($m1bool) {$row[3] = '1 Month';}
		elseif ($m3bool) {$row[3] = '3 Months';}
		elseif ($m6bool) {$row[3] = '6 Months';}
		
		$row[4] = '';
		$row[5] = 0;
		
		switch ($rgroup) {
			case '1':
				$row[4] = $data['pttk_ideal_date_2'];
				break;
			case '2':
				$row[4] = $data['pttk_ideal_date'];
				break;
		}
		if (!empty($row[4])) {
			$row[5] = date_diff(date_create($today), date_create($row[4]))->format("%a");
			if ($today < $row[4])
				$row[5] *= -1;
		}
		
		$row[6] = $data["pttk_pt_report_sent_notes"];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "PT Reports (follow-up)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event", "Date sent:", "Contact 1 approx. date due:", "Contact 2 approx. date due:", "LPT contact approx. date due:", "LPT contact needed?", "Days passed since contact due:"],
	"content" => [],
	"attributes" => [
		"order-col" => 8,
		"order-direction" => "desc"
	]
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
			$row = array_fill(0, count($table['headers']), "");
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $record[$dash->enrollmentEID]['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = $data['pttk_date_pt_report_sent'];
			$row[4] = $data['pttk_contact_due_1'];
			$row[5] = $data['pttk_contact_due_2'];
			$row[6] = $data['pttk_contact_due_3'];
			$row[7] = $dash->labelizeValue('pttk_lead_pt_contact_needed', $data['pttk_lead_pt_contact_needed']);
			$row[8] = 0;
			
			$compareDate = max($row[4], $row[5], $row[6]);
			if (!empty($compareDate)) {
				$row[8] = date_diff(date_create($compareDate), date_create($today))->format("%a");
				if ($today < $compareDate)
					$row[8] *= -1;
			}
			unset($compareDate);
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);


unset($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// add section divider here
$content .= "<h2 class='blueDivider'>Crossover PT Reports</h2>";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Initial Treating PT Referral/Notification Needed for Crossover",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Patient Info Release sent to Treating PT?", "Is Site Lead PT the treating PT?"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$otherdata = $record[$dash->otherEID];
	if (
		$otherdata['pti_arm_x'] <> "" and 
		((($otherdata['pti_lead_pt_is_treating_pt_x'] == "0" AND 
		$otherdata['pti_contact_pt_x'] <> "1") OR 
		($otherdata['pti_lead_pt_is_treating_pt_x'] == "1" AND 
		$otherdata['pti_patient_is_participant_x'] <> "1")) OR 
		($otherdata['pti_lead_pt_is_treating_pt_x'] == "")) AND
		$otherdata['pti_pt_contacted_x'] == ''
	) {
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->labelizeValue('pti_release_sent_to_pt_x', $otherdata['pti_release_sent_to_pt_x']);
		$row[3] = "";
		if ($otherdata['pti_lead_pt_is_treating_pt_x'] == '1')
			$row[3] = "Yes (1)";
		if ($otherdata['pti_lead_pt_is_treating_pt_x'] == '0')
			$row[3] = "No (0)";
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Outstanding Lead PT Calls/Verification for Crossover",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Is Site Lead PT the treating PT?", "Date referred to Site Lead PT:", "Has treating PT been successfully contacted?", "Contact Notes (if any)", "Days since referred:"],
	"content" => [],
	"attributes" => [
		"order-col" => 5,
		"order-direction" => "desc"
	]
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$otherdata = $record[$dash->otherEID];
	if (
		(($otherdata['pti_patient_is_participant_x'] == "1") AND 
		($otherdata['pti_pt_contacted_x'] == "")) OR 
		(($otherdata['pti_contact_pt_x'] == "1") AND 
		(($otherdata['pti_pt_contacted_x'] == "") OR 
		($otherdata['pti_pt_contacted_x'] == "2")))
	) {
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $dash->labelizeValue("pti_lead_pt_is_treating_pt", $otherdata["pti_lead_pt_is_treating_pt"]);
		$row[2] = $dash->labelizeValue("pti_lpt_referral_date", $otherdata["pti_lpt_referral_date"]);
		$row[3] = $dash->labelizeValue("pti_pt_contacted", $otherdata["pti_pt_contacted"]);
		$row[4] = $dash->labelizeValue("pti_pt_contact_notes", $otherdata["pti_pt_contact_notes"]);
		$row[5] = 0;
		
		if (!empty($row[2])) {
			$row[5] = date_diff(date_create($row[2]), date_create($today))->format("%a");
			if ($today < $row[2])
				$row[5] *= -1;
		}
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Crossover PT reports to be sent",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "Date patient discharged (or expected to be discharged)", "Days since report due to be sent"],
	"content" => [],
	"attributes" => [
		"order-col" => 2,
		"order-direction" => "desc"
	]
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$otherdata = $record[$dash->otherEID];
	if (
		($otherdata['pttk_pt_rep_sent_x_r'] <> "1") AND
		($otherdata['pti_pt_contacted_x'] == "1")
	) {
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $dash->labelizeValue("pti_date_discharge_x", $otherdata["pti_date_discharge_x"]);
		$row[2] = 0;
		
		if (!empty($row[1])) {
			$row[2] = date_diff(date_create($row[1]), date_create($today))->format("%a");
			if ($today < $row[1])
				$row[2] *= -1;
		}
		
		$table['content'][] = $row;
	}

}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Outstanding Crossover PT Reports",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Event", "DAG", "Date PT Report Sent", "RA Contact 1 approx date due:", "RA Contact 2 approx date due:", "Lead PT contact approx date due:", "Lead PT Contact Needed? (If PT Report not completed one (1) week after RA Contact 2)", "Days since contact due"],
	"content" => [],
	"attributes" => [
		"order-col" => 8,
		"order-direction" => "desc"
	]
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$otherdata = $record[$dash->otherEID];
	if (
		($otherdata['pttk_pt_rep_sent_x_r'] == "1") AND 
		($otherdata['pttk_pt_rep_completed_x_r'] == "2" OR 
		$otherdata['pttk_pt_rep_completed_x_r'] == "")
	) {
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $dash->projEvents[$eid];
		$row[2] = $record[$dash->enrollmentEID]['pati_6'];
		$row[3] = $dash->labelizeValue('pttk_date_pt_rep_sent_x_r', $otherdata['pttk_date_pt_rep_sent_x_r']);
		$row[4] = $otherdata['pttk_contact_due_1_x_r'];
		$row[5] = $otherdata['pttk_contact_due_2_x_r'];
		$row[6] = $otherdata['pttk_contact_due_3_x_r'];
		$row[7] = $dash->labelizeValue('pttk_lpt_cont_needed_x_r', $otherdata['pttk_lpt_cont_needed_x_r']);
		$row[8] = 0;
		
		$compareDate = max($row[4], $row[5], $row[6]);
		if (!empty($compareDate)) {
			$row[8] = date_diff(date_create($compareDate), date_create($today))->format("%a");
			if ($today < $compareDate)
				$row[8] *= -1;
		}
		unset($compareDate);
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

unset($table);



