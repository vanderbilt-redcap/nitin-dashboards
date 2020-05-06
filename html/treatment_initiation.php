<?php

global $dash;
$content = "";

$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"fields" => [
		"qtk_call_due_pt",
		'pati_x15',
		"qtk_call_due_4",
		'enrollment_id',
		'qtk_physical_therapy',
		"qtk_call_due_3",
		'pati_study_status',
		'pati_6',
		'randgroup',
		'pati_x16',
		"pati_surgical_sched_notes",
		"qtk_call_due_2",
		'3months_arm_1',
		"sxu_b1",
		"qtk_call_due_pt_2",
		'tx_a7_fu',
		'6months_arm_1',
		'randgroup',
		'pati_16',
		"pati_x15",
		'enrollment_arm_1',
		'pati_x16',
		'tx_a7_fu',
		'pati_x15',
		'sxu_b1',
		'12months_arm_1',
		"pati_16",
		"tx_a7_fu",
		"date",
		'pati_16',
		'sxu_b1',
		'study_id'
	],
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Physical Therapy Scheduling",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization (if Non-Op)/Surgery(if Op) Date", "Randomization/Surgery Date", "Call 1 approx. date due", "Call 2 approx. date due", "Call 3 approx. date due", "Site referral", "Days passed since contact due:"],
	"content" => []
];
$today = date('Y-m-d');
foreach ($records as $i => $record) {
	$edata = $enrollment = $record[$dash->enrollmentEID];
	$baseline = $record[$dash->baselineEID];
	if ($baseline['qtk_physical_therapy'] <> '1' and
	$baseline['qtk_physical_therapy'] <> '0' and
	$enrollment['pati_study_status'] <> '0' and
	($enrollment['randgroup'] == '2' or ($enrollment['randgroup'] == '1' and $enrollment['pati_x15'] <> ''))) {
		$rgroup = $record[$dash->enrollmentEID]['randgroup'];
		$row = [];
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $record[$dash->enrollmentEID]['pati_6'];
		# print with label where possible, if not, print actual value
		// $row[2] = $rgroup == '1' ? 'Operative (1)' : ($rgroup == 2 ? 'Non-operative (2)' : $rgroup);
		$row[2] = $this->labelizeValue("randgroup", $rgroup);
		$row[3] = $rgroup == '1' ? $record[$dash->enrollmentEID]["pati_x15"] : ($rgroup == 2 ? $record[$dash->enrollmentEID]["date"] : '');
		$row[4] = $rgroup == '1' ? $record[$dash->baselineEID]["qtk_call_due_pt_2"] : ($rgroup == 2 ? $record[$dash->baselineEID]["qtk_call_due_pt"] : '');
		$row[5] = $record[$dash->baselineEID]["qtk_call_due_2"];
		$row[6] = $record[$dash->baselineEID]["qtk_call_due_3"];
		$row[7] = $record[$dash->baselineEID]["qtk_call_due_4"];
		$row[8] = 0;
		
		$mostRecent = max($row[3], $row[4], $row[5], $row[6]);
		if (!empty($mostRecent)) {
			$row[8] = date_diff(date_create($today), date_create($mostRecent))->format("%a");
			if ($today < $mostRecent)
				$row[8] *= -1;
		}
		unset($mostRecent);
		
		$table['content'][] = $row;
	}
}

# add table data to $content ($content will get output to user)
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Surgery Scheduling",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization date:", "Surgery Scheduled Date", "Surgery Scheduling Notes", "Days since Randomization/Surgery scheduled date"],
	"content" => [],
	"attributes" => [
		"order-col" => 5,
		"order-direction" => "desc"
	]
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	if ($edata['randgroup'] == '1' and $edata['pati_x15'] == '' and $edata['pati_study_status'] <> '0') {
		$row = [];
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $edata["date"];
		$row[3] = $edata["pati_16"];
		$row[4] = $edata["pati_surgical_sched_notes"];
		$row[5] = 0;
		
		$mostRecent = max($row[2], $row[3]);
		if (!empty($mostRecent)) {
			$row[5] = date_diff(date_create($today), date_create($mostRecent))->format("%a");
			if ($today < $mostRecent)
				$row[5] *= -1;
		}
		unset($mostRecent);
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Operative to Non-operative Potential Crossovers",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Scheduled Surgery Date", "Actual Surgery Date", "Patient 3mQ PT response", "Patient 6mQ PT response", "Patient 12mQ PT response", "Crossover Status"],
	"content" => [],
	"attributes" => [
		"order-col" => 3,
		"order-direction" => "asc"
	]
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$m3data = $record[$dash->m3EID];
	$m6data = $record[$dash->m6EID];
	$m12data = $record[$dash->m12EID];
	$other = $record[$dash->otherEID];
	if (
		// from "Potential Crossovers: Surgery To PT (Complete)" report logic
		// ([3months_arm_1][sxu_b1] <> "1") AND
		// ([6months_arm_1][sxu_b1] <> "1") AND
		// ([12months_arm_1][sxu_b1] <> "1") AND
		// ([enrollment_arm_1][pati_x15] = "") AND
		// ([enrollment_arm_1][randgroup] = "1") AND
		// ([3months_arm_1][tx_a7_fu] = "1" OR [6months_arm_1][tx_a7_fu] = "1" OR [12months_arm_1][tx_a7_fu] = "1" OR [enrollment_arm_1][pati_x16] <> "")
		$m3data['sxu_b1'] <> '1' and
		$m6data['sxu_b1'] <> '1' and
		$m12data['sxu_b1'] <> '1' and
		$edata['pati_x15'] == '' and
		$edata['randgroup'] == '1' and
		($m3data['tx_a7_fu'] == '1' or $m6data['tx_a7_fu'] == '1' or $m12data['tx_a7_fu'] == '1' or $edata['pati_x16'] <> '')
	) {
		$row = [];
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $record[$dash->enrollmentEID]['pati_6'];
		$row[2] = $edata['pati_x15'];
		
		// some local defs
		$m3 = $m3data["tx_a7_fu"];
		$m6 = $m6data["tx_a7_fu"];
		$m12 = $m12data["tx_a7_fu"];
		
		# if $m3 is empty, print empty, otherwise print label and value "Label (val)"
		$row[4] = $m3 == '1' ? "Yes (1)" : ($m3 == '0' ? "No (0)" : $m3);
		$row[5] = $m6 == '1' ? "Yes (1)" : ($m6 == '0' ? "No (0)" : $m6);
		$row[6] = $m12 == '1' ? "Yes (1)" : ($m12 == '0' ? "No (0)" : $m12);
		
		$row[7] = $this->labelizeValue('pti_arm_x', $other['pti_arm_x']);
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Non-operative to Operative Potential Crossovers",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Scheduled Surgery Data", "Actual Surgery Date", "Patient 3mQ surgery update", "Patient 6mQ surgery update", "Patient 12mQ surgery update", "Crossover Status"],
	"content" => [],
	"attributes" => [
		"order-col" => 3,
		"order-direction" => "asc"
	]
];
foreach ($records as $i => $record) {
	$m3data = $record[$dash->m3EID];
	$m6data = $record[$dash->m6EID];
	$m12data = $record[$dash->m12EID];
	$edata = $record[$dash->enrollmentEID];
	$other = $record[$dash->otherEID];
	if (
		($m3data['sxu_b1'] == '1' or
		$m6data['sxu_b1'] == '1' or
		$m12data['sxu_b1'] == '1' or
		$edata['pati_x15'] <> '' or
		$edata['pati_16'] <> '') and
		$edata['randgroup'] == '2'
	) {
		$row = [];
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $record[$dash->enrollmentEID]['pati_6'];
		$row[2] = $edata["pati_16"];
		$row[3] = $edata['pati_x15'];
		
		$m3 = $record[$dash->m3EID]["sxu_b1"];
		$m6 = $record[$dash->m6EID]["sxu_b1"];
		$m12 = $record[$dash->m12EID]["sxu_b1"];
		
		# if $m3 is empty, print empty, otherwise print label and value "Label (val)"
		$row[4] = $m3 == '1' ? "Yes (1)" : ($m3 == '0' ? "No (0)" : $m3);
		$row[5] = $m6 == '1' ? "Yes (1)" : ($m6 == '0' ? "No (0)" : $m6);
		$row[6] = $m12 == '1' ? "Yes (1)" : ($m12 == '0' ? "No (0)" : $m12);
		
		$row[7] = $this->labelizeValue('pti_arm_x', $other['pti_arm_x']);
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);