<?php

global $dash;
$content = "";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# add Physical Therapy Scheduling table
$table = [
	"title" => "Physical Therapy Scheduling",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization Group", "Randomization/Surgery Date", "Call 1", "Call 2", "Call 3", "Site referral"],
	"content" => [
		# mockup data for visuals
		// ["4-3285-119", "", "", "8/30/18", "9/10/18", "9/17/18", "9/21/18", "9/25/18"],
		// ["10-2284-23", "", "", "8/26/18", "9/09/18", "9/21/18", "9/25/18", "9/31/18"]
	]
];

# get PT Scheduling table data from db
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"filterLogic" => "([baseline_arm_1][qtk_physical_therapy] <> '1') AND ([baseline_arm_1][qtk_physical_therapy] <> '0') AND ([enrollment_arm_1][pati_study_status]<>'0') AND (([enrollment_arm_1][randgroup]='2') OR (([enrollment_arm_1][randgroup]='1') AND ([enrollment_arm_1][pati_x15] <> '')))",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	$rgroup = $record[$dash->enrollmentEID]['randgroup'];
	$row = [];
	$row[0] = $record[$dash->enrollmentEID]['study_id'];
	$row[1] = $record[$dash->enrollmentEID]['pati_6'];
	# print with label where possible, if not, print actual value
	$row[2] = $rgroup == '1' ? 'Operative (1)' : ($rgroup == 2 ? 'Non-operative (2)' : $rgroup);
	$row[3] = $rgroup == '1' ? $record[$dash->enrollmentEID]["pati_x15"] : ($rgroup == 2 ? $record[$dash->enrollmentEID]["date"] : '');
	$row[4] = $rgroup == '1' ? $record[$dash->baselineEID]["qtk_call_due_pt_2"] : ($rgroup == 2 ? $record[$dash->baselineEID]["qtk_call_due_pt"] : '');
	$row[5] = $record[$dash->baselineEID]["qtk_call_due_2"];
	$row[6] = $record[$dash->baselineEID]["qtk_call_due_3"];
	$row[7] = $record[$dash->baselineEID]["qtk_call_due_4"];
	
	$table['content'][] = $row;
}

# add table data to $content ($content will get output to user)
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# add Surgery Scheduling table
$table = [
	"title" => "Surgery Scheduling",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization Date", "Surgery Scheduled Date", "Surgery Scheduling Notes"],
	"content" => [
		// # mockup data
		// ["Knoxville Orthopedic Clinic", "4-3285-119", "9/31/18", "Piped from patient study info overview"],
		// ["UTSW", "10-2284-23", "11/14/18", "Piped from patient study info overview"]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"filterLogic" => "([enrollment_arm_1][randgroup] = '1') AND ([enrollment_arm_1][pati_x15] = '') AND ([enrollment_arm_1][pati_study_status] != '0')",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	$row = [];
	# study id
	$row[0] = $record[$dash->enrollmentEID]['study_id'];
	# dag
	$row[1] = $record[$dash->enrollmentEID]['pati_6'];
	# randomization date
	$row[2] = $record[$dash->enrollmentEID]["date"];
	# surgery scheduled date
	$row[3] = $record[$dash->enrollmentEID]["pati_x15"];
	# surgery scheduling notes
	$row[4] = $record[$dash->enrollmentEID]["pati_surgical_sched_notes"];
	
	$table['content'][] = $row;
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Delay of treatment deviations (upcoming) table
$table = [
	// "title" => "Delay of treatment deviations (upcoming)",
	"title" => "Operative to Non-operative Potential Crossovers",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Actual Surgery Date", "3m PT response", "6m PT response", "12m PT response"],
	"content" => [
		// ["4-3285-119", "operative", "10/30/18", "10/22/18"],
		// ["10-2284-23", "non-operative", "10/30/18", ""]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"filterLogic" => "([3months_arm_1][sxu_b1] <> '1') AND ([6months_arm_1][sxu_b1] <> '1') AND ([12months_arm_1][sxu_b1] <> '1') AND ([enrollment_arm_1][pati_x15] = '') AND ([enrollment_arm_1][randgroup] = '1') AND ([3months_arm_1][tx_a7_fu] = '1' OR [6months_arm_1][tx_a7_fu] = '1' OR [12months_arm_1][tx_a7_fu] = '1' OR [enrollment_arm_1][pati_x16] <> '')",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	$row = [];
	$row[0] = $record[$dash->enrollmentEID]['study_id'];
	$row[1] = $record[$dash->enrollmentEID]['pati_6'];
	$row[2] = $record[$dash->enrollmentEID]["date"];
	
	$m3 = $record[81]["tx_a7_fu"];
	$m6 = $record[82]["tx_a7_fu"];
	$m12 = $record[83]["tx_a7_fu"];
	
	# if $m3 is empty, print empty, otherwise print label and value "Label (val)"
	$row[3] = $m3 == '1' ? "Yes (1)" : ($m3 == '0' ? "No (0)" : $m3);
	$row[4] = $m6 == '1' ? "Yes (1)" : ($m6 == '0' ? "No (0)" : $m6);
	$row[5] = $m12 == '1' ? "Yes (1)" : ($m12 == '0' ? "No (0)" : $m12);
	$table['content'][] = $row;
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # add Delay of treatment deviations (need recorded) table
$table = [
	// "title" => "Delay of treatment deviations (need recorded)",
	"title" => "Non-operative to Operative Potential Crossovers",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Actual Surgery Date", "3m sxu response", "6m sxu response", "12m sxu response"],
	"content" => []
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"filterLogic" => "([3months_arm_1][sxu_b1] = '1' OR [6months_arm_1][sxu_b1] = '1' OR [12months_arm_1][sxu_b1] = '1' OR [enrollment_arm_1][pati_x15] <> '' OR [enrollment_arm_1][pati_16] <> '') AND ([enrollment_arm_1][randgroup] = '2')",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
foreach ($records as $i => $record) {
	$row = [];
	$row[0] = $record[$dash->enrollmentEID]['study_id'];
	$row[1] = $record[$dash->enrollmentEID]['pati_6'];
	$row[2] = $record[$dash->enrollmentEID]['pati_x15'];
	
	$m3 = $record[81]["sxu_b1"];
	$m6 = $record[82]["sxu_b1"];
	$m12 = $record[83]["sxu_b1"];
	
	# if $m3 is empty, print empty, otherwise print label and value "Label (val)"
	$row[3] = $m3 == '1' ? "Yes (1)" : ($m3 == '0' ? "No (0)" : $m3);
	$row[4] = $m6 == '1' ? "Yes (1)" : ($m6 == '0' ? "No (0)" : $m6);
	$row[5] = $m12 == '1' ? "Yes (1)" : ($m12 == '0' ? "No (0)" : $m12);
	$table['content'][] = $row;
}
$content .= $dash->makeDataTable($table);
$content .= "<br />";
$content .= "<br />";
$content .= "<br />";