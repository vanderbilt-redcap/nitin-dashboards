<?php

global $dash;
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "PI Calls Needed",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Approximate date PI call needed"],
	"content" => []
];
$piCallsNeeded = 0;
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if ($data['qtk_pi_call'] == '1' and $data['qtk_pi_call_complete'] == '' AND ($edata["pati_study_status"] != "0")) {
			$piCallsNeeded++;
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $edata['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = $data["qtk_call_due_4"];
			
			$table['content'][] = $row;
		}
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# add Follow-Up Status Summary table

// calculate some values needed
$records = \REDCap::getData([
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"exportDataAccessGroups" => true
]);
$tableData = [
	["Patients with treatment info outstanding:", 0],
	["Physical therapy reports to be sent:", 0],
	["Physical therapy reports pending:", 0],
	["Surgery reports outstanding:", 0],
	["Questionnaires to be sent:", 0],
	["Questionnaires pending:", 0],
	// ["PI Calls Needed:", $piCallsNeeded],
	["Physical therapy diaries to be checked/sent:", 0],
	["Outstanding physical therapy diaries:", 0],
	["Check requests due:", 0]
];

$day30 = date('Y-m-d', strtotime('+30 days'));	# set target date to be 30 days from now
$today = date('Y-m-d');
foreach ($records as $i => $record) {
	// "Patients with treatment info outstanding:" 0
	$edata = $enrollment = $record[$dash->enrollmentEID];
	$baseline = $record[$dash->baselineEID];
	
	if ($baseline['qtk_physical_therapy'] <> '1' and
	$baseline['qtk_physical_therapy'] <> '0' and
	$enrollment['pati_study_status'] <> '0' and
	($enrollment['randgroup'] == '2' or ($enrollment['randgroup'] == '1' and $enrollment['pati_x15'] <> ''))) {
		$tableData[0][1]++;
	}
	
	// "Physical therapy reports to be sent:" 1
	$m1data = $record[$dash->m1EID];
	$m3data = $record[$dash->m3EID];
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
	
	if ($m1bool) {
		if ($edata['randgroup'] == 1) {
			$compareDate = $m1data['pttk_ideal_date_2'];
		} else {
			$compareDate = $m1data['pttk_ideal_date'];
		}
	} elseif ($m3bool) {
		if ($edata['randgroup'] == 1) {
			$compareDate = $m1data['pttk_ideal_date_2'];
		} else {
			$compareDate = $m1data['pttk_ideal_date'];
		}
	}
	// $compareDate = $edata['randgroup'] == 1 ? $m1data[
	if (($edata['pati_study_status']<>'0') and ($compareDate >= $today) and ($m1bool or $m3bool)) {
		$tableData[1][1]++;
	}
	
	// "Physical therapy reports pending:" 2
	
	// "Surgery reports outstanding:" 3
	if ($edata['sdoc_vumc_cert_2'] <> '1' and $edata['randgroup'] == '1' and $edata['pati_x15'] <> '')
		$tableData[3][1]++;
	
	// "Questionnaires to be sent:" 4
	foreach ($record as $eid => $data) {
		if (
			($data['qtk_lower_window'] < $day30) AND
			($data['qtk_questionnaire_sent'] == "") AND
			($edata['date'] <> "") AND
			($data['qtk_lower_window'] <> "") AND
			($edata['pati_study_status'] <> "0") AND
			($data['qtk_lower_window'] >= $today)
		) {
			$tableData[4][1]++;
		}
	}
	
	// "Questionnaires pending:" 5
	
	// "Physical therapy diaries to be checked/sent:" 6
	$m6data = $record[$dash->m6EID];
	if (
		$edata['pati_14'] == "" or 
		($m1data['pttk_diary_check'] <> "1" and $m1data['pttk_pt_report_sent'] <> "") or 
		($m3data['qtk_questionnaire_sent_2'] == "" and $m3data['qtk_questionnaire_sent'] == "1") or 
		($m6data['qtk_questionnaire_sent_2'] == "" and $m6data['qtk_questionnaire_sent'] == "1")
	) {
		$tableData[6][1]++;
	}
	
	// "Outstanding physical therapy diaries:" 7
	
	// "Check requests due:" 8
	
}

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

unset($table);