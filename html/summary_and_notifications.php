<?php

llog("top of summary_and_notifications.php: " . memory_get_usage(true) / 1000000);

global $dash;
$today = date('Y-m-d');
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"fields" => [
		"qtk_pi_call",
		"qtk_pi_call_complete",
		"pati_study_status",
		"enrollment_id",
		"study_id",
		"pati_6",
		"qtk_call_due_4",
		"qtk_questionnaire_received",
	],
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);

llog("after get records s_a_n: " . memory_get_usage(true) / 1000000);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "PI Calls Needed",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Approximate date PI call needed", "Days since due"],
	"content" => [],
	"attributes" => [
		"order-col" => 4,
		"order-direction" => "desc"
	]
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if (
			$data['qtk_pi_call'] == '1' and
			$data['qtk_pi_call_complete'] == '' and
			($edata["pati_study_status"] <> "0") and
			$data['qtk_questionnaire_received'] <> "1"
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $edata['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = $data["qtk_call_due_4"];
			$row[4] = "0";
			
			if ($row[3] < $today) {
				$row[4] = date_diff(date_create($today), date_create($row[3]))->format("%a");
			}
			
			$table['content'][] = $row;
		}
	}
}

llog("after adding data to \$table: " . memory_get_usage(true) / 1000000);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# add Follow-Up Status Summary table

// calculate some values needed
$records = \REDCap::getData([
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"fields" => [
		"enrollment_id",
		"study_id",
		"randgroup",
		"pati_x15",
		"pati_study_status",
		"qtk_physical_therapy",
		"pttk_pt_report_sent",
		"pttk_ideal_date",
		"pttk_ideal_date_2",
		"pttk_pt_report_completed",
		"pttk_lead_pt_contact_made",
		"pttk_date_pt_report_sent",
		"pttk_contact_due_1",
		"pttk_contact_due_2",
		"pttk_contact_due_3",
		"pttk_lead_pt_contact_needed",
		"sdoc_vumc_cert_2",
		"qtk_lower_window",
		"qtk_questionnaire_sent",
		"date",
		"qtk_timepoint",
		"qtk_pi_call",
		"qtk_questionnaire_received",
		"qtk_call_due",
		"qtk_call_due_2",
		"qtk_call_due_3",
		"qtk_call_due_4",
		"pati_14",
		"pttk_diary_check",
		"qtk_questionnaire_sent_2",
		"qtk_check_request_submitted",
		"evt_report_to_sponsor",
		"evt_irb_report",
		"evt_irb_submitted",
		"ptr_a2",
		"pttk_pt_rep_sent_x_r",
		"pti_date_discharge_x",
		"pti_pt_contacted_x",
		"pttk_pt_rep_completed_x_r",
		"pttk_contact_due_1_x_r",
		"pttk_contact_due_2_x_r",
		"pttk_contact_due_3_x_r",
		"mycap_completion",
		"qtk_questionnaire_received_2",
		"qtk_date_received",
		"evt_notify_study_coord",
		"evt_event_type",
		"pati_6"
	],
	"exportDataAccessGroups" => true
]);

llog("after get records (2nd): " . memory_get_usage(true) / 1000000);

$tableData = [
	["Timely SAE reports in process:", 0],
	["AE reports for initial review by study coordinator:", 0],
	["Patients with treatment info outstanding:", 0],
	["Physical therapy reports to be sent:", 0],
	["Crossover PT reports to be sent:", 0],
	["Physical therapy report follow-up calls due:", 0],
	["Questionnaires to be sent:", 0],
	["Questionnaire Follow-Up Calls Due:", 0],
	["Physical therapy diaries to be checked/sent:", 0],
	["Physical therapy diaries overdue:", 0],
	["Check requests due:", 0]
];

$day30 = date('Y-m-d', strtotime('+30 days'));	# set target date to be 30 days from now
foreach ($records as $i => $record) {
	$edata = $enrollment = $record[$dash->enrollmentEID];
	$m1data = $record[$dash->m1EID];
	$m3data = $record[$dash->m3EID];
	$m6data = $record[$dash->m6EID];
	$baseline = $record[$dash->baselineEID];
	$other = $record[$dash->otherEID];
	
	// iterate through records, setting summary row values as applicable
	// 0 "Timely SAE reports in process:"
	if (
		($other['evt_report_to_sponsor'] == "1") and 
		($other['evt_irb_report'] <> "0") and 
		($other['evt_irb_submitted'] <> "1")
	) {
		$tableData[0][1]++;
	}
	
	// 1 "AE reports for initial review by study coordinator:"
	if (
		($other['evt_report_to_sponsor'] == "0" OR
		$other['evt_report_to_sponsor'] == "2") AND
		($other['evt_coord_review'] <> "1")
	) {
		$tableData[1][1]++;
	}
	
	// 2 "Patients with treatment info outstanding:"
	if (
		($edata['randgroup'] == '1' and
		$edata['pati_x15'] == '' and
		$edata['pati_study_status'] <> '0') or
		($baseline['qtk_physical_therapy'] <> '1' and
		$baseline['qtk_physical_therapy'] <> '0' and
		$enrollment['pati_study_status'] <> '0' and
		($enrollment['randgroup'] == '2' or
		($enrollment['randgroup'] == '1' and
		$enrollment['pati_x15'] <> '')))
	) {
		$tableData[2][1]++;
	}
	
	// 3 "Physical therapy reports to be sent:"
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
	$last_date = false;
	if ($m1bool) {$data = $m1data;}
	elseif ($m3bool) {$data = $m3data;}
	elseif ($m6bool) {$data = $m6data;}
	switch ($edata['randgroup']) {
		case '1':
			$last_date = $data['pttk_ideal_date_2'];
			break;
		case '2':
			$last_date = $data['pttk_ideal_date'];
			break;
	}
	if ($last_date !== false) {
		$days_since = date_diff(date_create($last_date), date_create($today))->format("%a");
	}
	if (
		($edata['pati_study_status'] <> '0') and ($m1bool or $m3bool or $m6bool) and ((int) $days_since > 0)
	) {
		$tableData[3][1]++;
	}
	
	// 4 "Crossover PT reports to be sent:"
	if (
		($other['pttk_pt_rep_sent_x_r'] <> "1") AND
		($other['pti_pt_contacted_x'] == "1") AND
		$other['pti_date_discharge_x'] > $today
	) {
		$tableData[4][1]++;
	}
	
	// 5 "Physical therapy report follow-up calls due:"
	foreach ($record as $eid => $data) {
		if (
			($data['pttk_pt_report_completed'] == '' or
			$data['pttk_pt_report_completed'] == '2') and
			$data['pttk_pt_report_sent'] == '1' and
			$data['pttk_lead_pt_contact_made'] == '' and
			$edata['pati_study_status'] <> '0'
		) {
			$d1 = $data['pttk_contact_due_1'];
			$d2 = $data['pttk_contact_due_2'];
			$d3 = $data['pttk_contact_due_3'];
			
			$compareDate = max($row[4], $row[5], $row[6]);
			if (empty($compareDate) or $compareDate >= $today) {
				
			} else {
				$tableData[5][1]++;
			}
		}
		
		// crossovers due
		if (
			($data['pttk_pt_rep_sent_x_r'] == "1") AND 
			($data['pttk_pt_rep_completed_x_r'] == "2" OR 
			$data['pttk_pt_rep_completed_x_r'] == "")
		) {
			$d1 = $data['pttk_contact_due_1_x_r'];
			$d2 = $data['pttk_contact_due_2_x_r'];
			$d3 = $data['pttk_contact_due_3_x_r'];
			if (max($d1, $d2, $d3) <= $today) {
				$tableData[5][1]++;
			}
		}
	}

	
	// 6 "Questionnaires to be sent:"
	foreach ($record as $eid => $data) {
		if (
			// logic pulled from "Questionnaires to send" report
			($data['qtk_lower_window'] < $day30) AND
			($data['qtk_questionnaire_sent'] == "") AND
			($edata['date'] <> "") AND
			($data['qtk_lower_window'] <> "") AND
			($edata['pati_study_status'] <> "0")
		) {
			if ($today > $data['qtk_lower_window']) {
				$tableData[6][1]++;
			}
		}
	}
	
	// 7 "Questionnaire Follow-Up Calls Due:"
	foreach ($record as $eid => $data) {
		if (
			($data['qtk_timepoint'] <> "0") and
			($data['qtk_pi_call'] <> "1") and
			($data['pati_study_status'] <> "0") and
			($data['qtk_questionnaire_sent'] == "1") and
			($data['qtk_questionnaire_received'] == "" or
			$data['qtk_questionnaire_received'] == "2") and
			(max($data['qtk_call_due'], $data['qtk_call_due_2'], $data['qtk_call_due_3'], $data['qtk_call_due_4']) < $today)
		) {
			$tableData[7][1]++;
		}
	}
	
	// 8 "Physical therapy diaries to be checked/sent:"
	if (
		$edata['pati_14'] == "" or 
		($m1data['pttk_diary_check'] <> "1" and $m1data['pttk_pt_report_sent'] <> "") or 
		($m3data['qtk_questionnaire_sent_2'] == "" and $m3data['qtk_questionnaire_sent'] == "1") or 
		($m6data['qtk_questionnaire_sent_2'] == "" and $m6data['qtk_questionnaire_sent'] == "1")
	) {
		$tableData[8][1]++;
	}
	
	// 9 "Physical therapy diaries overdue:"
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
			if (!empty($data['qtk_date_received'])) {
				$days_difference = date_diff(date_create($data['qtk_date_received']), date_create($today))->format("%a");
			}
			if ($days_difference > 14) {
				$tableData[9][1]++;
			}
		}
	}
	
	// 10 "Check requests due:"
	foreach ($record as $eid => $data) {
		if (
			$data['qtk_check_request_submitted'] == '' and
			$data['qtk_questionnaire_received'] == '1'
		) {
			$tableData[10][1]++;
		}
	}
}

llog("after adding 2nd table: " . memory_get_usage(true) / 1000000);

$content = "<table class='summaryTable'>
	<thead>
		<th>Follow-Up Status Summary</th>
		<th></th>
	</thead>
	<tbody>";
foreach($tableData as $i => $row) {
	$evenOdd = ($i % 2 == 1) ? 'odd' : 'even';
	$content .= "
		<tr class='$evenOdd'>
			<th>" . $row[0] . "</th>
			<td>" . $row[1] . "</td>
		</tr>";
}
$content .= "
	</tbody>
</table>";
unset($evenOdd, $i, $row);

# add PI Calls Needed table
$content .= $dash->makeDataTable($table);

llog("after adding \$content: " . memory_get_usage(true) / 1000000);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "AEs to be reviewed by PI prior to continuing review",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Data Access Group", "Event Type"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record['repeat_instances'][$dash->otherEID]['events_saeae'] as $repeatInstance => $data) {
		if (
			($data['evt_notify_study_coord'] <> "1") AND
			($data['evt_report_to_sponsor'] == "0")
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $this->labelizeValue("pati_6", $edata['pati_6']);
			$row[2] = $this->labelizeValue("evt_event_type", $data['evt_event_type']);
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

unset($table);