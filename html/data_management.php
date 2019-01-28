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
	"title" => "Enrollment Documents",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization Group", "Randomization date:", "These documents MUST be uploaded BY:"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	if ($edata['sdoc_initial_due'] <> '' and $edata['sdoc_vumc_cert'] <> '1') {
		$row = [];
		$row[0] = $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->labelizeValue('randgroup', $edata['randgroup']);
		$row[3] = $edata['date'];
		$row[4] = $edata['sdoc_initial_due'];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Surgical Documents",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization date", "Actual Surgery Date:", "These documents MUST be uploaded BY:"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	if ($edata['sdoc_vumc_cert_2'] <> '1' and $edata['randgroup'] == '1' and $edata['pati_x15'] <> '') {
		$row = [];
		$row[0] = $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $edata['date'];
		$row[3] = $edata['pati_x15'];
		$row[4] = $edata['sdoc_surgical_due'];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Data Validation (Sites/Personnel)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Event", "Instance", "Information to be validated", "Resolution notes", "Date issue(s) discovered", "Contact 1 Date", "Contact 2 Date", "Contact 3 Date", "Contact 4 Date", "Contact 5 Date"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if ($data['dval_action_needed'] == '1' and $data['dval_res_ra'] == '2') {
			$row = [];
			$row[0] = $edata['study_id'];
			$row[1] = $edata['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = '';
			$row[4] = $data['dval_specify_research'];
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Delay of Treatment Protocol Deviations (to be recorded)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization group", "Date delay of treatment effective:", "Surgery scheduling status/notes: (if applicable:"],
	"content" => []
];
$day0 = date('Y-m-d');	# set target date to be 30 days from now
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$bdata = $record[$dash->baselineEID];
	if (
	$edata['qtk_pdv_effective_date'] <= $day0 and
	$edata['pati_x15'] == '' and
	$bdata['qtk_physical_therapy'] <> '1'
	) {
		$row = [];
		$row[0] = $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->labelizeValue('randgroup', $edata['randgroup']);
		$row[3] = $edata['qtk_pdv_effective_date'];
		$row[4] = $edata['pati_surgical_sched_notes'];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Delay of Treatment Protocol Deviations (approaching)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization Group", "Date delay of treatment effective:", "Surgery scheduling status/notes: (if applicable:"],
	"content" => []
];
$day0 = date('Y-m-d');
$day30 = date('Y-m-d', strtotime('+30 days'));	# set target date to be 30 days from now
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$bdata = $record[$dash->baselineEID];
	if (
	$edata['qtk_pdv_effective_date'] >= $day0 and
	$edata['qtk_pdv_effective_date'] <= $day30 and
	$edata['pati_x15'] == '' and
	$bdata['qtk_physical_therapy'] <> '1'
	) {
		$row = [];
		$row[0] = $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->labelizeValue('randgroup', $edata['randgroup']);
		$row[3] = $edata['qtk_pdv_effective_date'];
		$row[4] = $edata['pati_surgical_sched_notes'];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);


unset($table);