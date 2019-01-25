<?php

global $dash;
$content = "";

$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	// "exportAsLabels" => true,
	// "records" => ["4", "5"],
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# add Physical Therapy Scheduling table
$table = [
	"title" => "Physical Therapy Scheduling",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization Group", "Randomization/Surgery Date", "Call 1 approx. date due", "Call 2 approx. date due", "Call 3 approx. date due", "Site referral"],
	"content" => []
];
foreach ($records as $i => $record) {
	$enrollment = $record[$dash->enrollmentEID];
	$baseline = $record[$dash->baselineEID];
	if ($baseline['qtk_physical_therapy'] <> '1' and
	$baseline['qtk_physical_therapy'] <> '0' and
	$enrollment['pati_study_status'] <> '0' and
	($enrollment['randgroup'] == '2' or ($enrollment['randgroup'] == '1' and $enrollment['pati_x15'] <> ''))) {
		$rgroup = $record[$dash->enrollmentEID]['randgroup'];
		$row = [];
		$row[0] = $record[$dash->enrollmentEID]['study_id'];
		$row[1] = $record[$dash->enrollmentEID]['pati_6'];
		# print with label where possible, if not, print actual value
		// $row[2] = $rgroup == '1' ? 'Operative (1)' : ($rgroup == 2 ? 'Non-operative (2)' : $rgroup);
		$row[2] = $this->labelizeValue("randgroup", $rgroup);
		$row[3] = $rgroup == '1' ? $record[$dash->enrollmentEID]["pati_x15"] : ($rgroup == 2 ? $record[$dash->enrollmentEID]["date"] : '');
		$row[4] = $rgroup == '1' ? $record[$dash->baselineEID]["qtk_call_due_pt_2"] : ($rgroup == 2 ? $record[$dash->baselineEID]["qtk_call_due_pt"] : '');
		$row[5] = $record[$dash->baselineEID]["qtk_call_due_2"];
		$row[6] = $record[$dash->baselineEID]["qtk_call_due_3"];
		$row[7] = $record[$dash->baselineEID]["qtk_call_due_4"];
		
		$table['content'][] = $row;
	}
}

# add table data to $content ($content will get output to user)
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# add Surgery Scheduling table
$table = [
	"title" => "Surgery Scheduling",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization date:", "Surgery Scheduled Date", "Surgery Scheduling Notes"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	if ($edata['randgroup'] == '1' and $edata['pati_x15'] == '' and $edata['pati_study_status'] <> '0') {
		$row = [];
		$row[0] = $record[$dash->enrollmentEID]['study_id'];
		$row[1] = $record[$dash->enrollmentEID]['pati_6'];
		$row[2] = $record[$dash->enrollmentEID]["date"];
		$row[3] = $record[$dash->enrollmentEID]["pati_16"];
		$row[4] = $record[$dash->enrollmentEID]["pati_surgical_sched_notes"];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Operative to Non-operative Potential Crossovers",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Actual Surgery Date", "3m PT tx response", "6m PT tx response", "12m PT tx response"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$m3data = $record[$dash->m3EID];
	$m6data = $record[$dash->m6EID];
	$m12data = $record[$dash->m12EID];
	if (
	($m3data['sxu_b1'] <> '1') and
	($m6data['sxu_b1'] <> '1') and
	($m12data['sxu_b1'] <> '1') and
	($edata['pati_x15'] == '') and
	($edata['randgroup'] == '1')
	) {
		$row = [];
		$row[0] = $record[$dash->enrollmentEID]['study_id'];
		$row[1] = $record[$dash->enrollmentEID]['pati_6'];
		$row[2] = $record[$dash->enrollmentEID]["date"];
		
		$m3 = $record[$dash->m3EID]["tx_a7_fu"];
		$m6 = $record[$dash->m6EID]["tx_a7_fu"];
		$m12 = $record[$dash->m12EID]["tx_a7_fu"];
		
		# if $m3 is empty, print empty, otherwise print label and value "Label (val)"
		$row[3] = $m3 == '1' ? "Yes (1)" : ($m3 == '0' ? "No (0)" : $m3);
		$row[4] = $m6 == '1' ? "Yes (1)" : ($m6 == '0' ? "No (0)" : $m6);
		$row[5] = $m12 == '1' ? "Yes (1)" : ($m12 == '0' ? "No (0)" : $m12);
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Non-operative to Operative Potential Crossovers",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Actual Surgery Date", "3m sxu response", "6m sxu response", "12m sxu response"],
	"content" => []
];
// $params = [
	// "project_id" => $dash->pid,
	// "return_format" => 'array',
	// "filterLogic" => "([3months_arm_1][sxu_b1] = '1' OR [6months_arm_1][sxu_b1] = '1' OR [12months_arm_1][sxu_b1] = '1' OR [enrollment_arm_1][pati_x15] <> '' OR [enrollment_arm_1][pati_16] <> '') AND ([enrollment_arm_1][randgroup] = '2')",
	// "exportDataAccessGroups" => true
// ];
// $records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	$m3data = $record[$dash->m3EID];
	$m6data = $record[$dash->m6EID];
	$m12data = $record[$dash->m12EID];
	$edata = $record[$dash->enrollmentEID];
	if (
	$m3data['sxu_b1'] == '1' or
	$m6data['sxu_b1'] == '1' or
	$m12data['sxu_b1'] == '1' or
	$edata['pati_x15'] <> '' or
	$edata['pati_16'] <> '' and
	$edata['randgroup'] == '2'
	) {
		$row = [];
		$row[0] = $record[$dash->enrollmentEID]['study_id'];
		$row[1] = $record[$dash->enrollmentEID]['pati_6'];
		$row[2] = $record[$dash->enrollmentEID]['pati_x15'];
		
		$m3 = $record[$dash->m3EID]["sxu_b1"];
		$m6 = $record[$dash->m6EID]["sxu_b1"];
		$m12 = $record[$dash->m12EID]["sxu_b1"];
		
		# if $m3 is empty, print empty, otherwise print label and value "Label (val)"
		$row[3] = $m3 == '1' ? "Yes (1)" : ($m3 == '0' ? "No (0)" : $m3);
		$row[4] = $m6 == '1' ? "Yes (1)" : ($m6 == '0' ? "No (0)" : $m6);
		$row[5] = $m12 == '1' ? "Yes (1)" : ($m12 == '0' ? "No (0)" : $m12);
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);
$content .= "<br />";
$content .= "<br />";
$content .= "<br />";