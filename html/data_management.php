<?php

global $dash;
$content = "";
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
$today = date("Y-m-d");
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Enrollment Document Collection",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization Group", "Randomization date:", "These documents MUST be uploaded BY:", "Days since date due:"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	if ($edata['sdoc_initial_due'] <> '' and $edata['sdoc_vumc_cert'] <> '1') {
		$row = [];
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->labelizeValue('randgroup', $edata['randgroup']);
		$row[3] = $edata['date'];
		$row[4] = $edata['sdoc_initial_due'];
		$row[5] = date_diff(date_create($row[4]), date_create($today))->format("%a");
		if ((int) $row[5] <= 0)
			$row[5] = "N/A";
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Surgical Document Collection",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization date", "Actual Surgery Date:", "These documents MUST be uploaded BY:", "Days since date due:"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	if ($edata['sdoc_vumc_cert_2'] <> '1' and $edata['randgroup'] == '1' and $edata['pati_x15'] <> '') {
		$row = [];
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $edata['date'];
		$row[3] = $edata['pati_x15'];
		$row[4] = $edata['sdoc_surgical_due'];
		$row[5] = date_diff(date_create($row[4]), date_create($today))->format("%a");
		if ((int) $row[5] <= 0)
			$row[5] = "N/A";
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Data Validation (Sites/Personnel)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Event", "Instance", "Information to be validated", "Resolution notes", "Date issue(s) discovered", "Contact 1 Date", "Contact 2 Date", "Contact 3 Date", "Contact 4 Date", "Contact 5 Date", "Days since last contact attempt:"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record['repeat_instances'][$dash->baselineEID]['data_collectionvalidation'] as $repeatInstance => $data) {
		// logic from "Data Validation (site)"
		// ([dval_action_needed] = "1") AND ([dval_res_ra] = "2")
		// if ($data['dval_action_needed'] == '1' or $record[$dash->baselineEID]['dval_res_ra'] == '2') exit('2');
		if ($data['dval_action_needed'] == '1' and $data['dval_res_ra'] == '2') {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $edata['pati_6'];
			$row[2] = $dash->projEvents[$dash->baselineEID];
			$row[3] = $repeatInstance;
			$row[4] = $data['dval_specify_research'];
			$row[5] = $data['dval_res_notes'];
			$row[6] = $data['dval_issue_disc_date'];
			$row[7] = $data['dval_contact_date_1'];
			$row[8] = $data['dval_contact_date_2'];
			$row[9] = $data['dval_contact_date_3'];
			$row[10] = $data['dval_contact_date_4'];
			$row[11] = $data['dval_contact_date_5'];
			
			if (empty($row[7]) and empty($row[8]) and empty($row[9]) and empty($row[10]) and empty($row[11])) {
				$row[12] = "N/A";
			} else {
				$mostRecent = max($row[7], $row[8], $row[9], $row[10], $row[11]);
				$row[12] = date_diff(date_create($mostRecent), date_create($today))->format("%a");
			}
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
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
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
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->labelizeValue('randgroup', $edata['randgroup']);
		$row[3] = $edata['qtk_pdv_effective_date'];
		$row[4] = $edata['pati_surgical_sched_notes'];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Double Data Entry",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "Eligibility", "PEX A", "PEX B", "Baseline Q", "3m Q", "PT Diary at 3m", "6M Q", "PT diary at 6m", "12 m Q", "Surgery Report"],
	"headers2" => [""],
	"css" => [],
	"content" => []
];
for ($i = count($table["headers"]) - 1; $i > 0; $i--) {
	$table["headers2"][] = "Primary";
	$table["headers2"][] = "Double";
}
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$bdata = $record[$dash->baselineEID];
	$m3data = $record[$dash->m3EID];
	$m6data = $record[$dash->m6EID];
	$m12data = $record[$dash->m12EID];
	
	$row = [];
	$css = [];
	$row[0] = $edata['study_id'];
	$row[1] = $edata["de_comp_initials_crf00"];
	$row[2] = $edata["de_2_comp_initials_crf00"];
	if (!empty($row[1]) and empty($row[2])) {
		$row[2] = $edata["date"];
		$css[2] = 1;
	}
	$row[3] = $edata["de_comp_initials_crf02a"];
	$row[4] = $edata["de_2_comp_initials_crf02a"];
	if (!empty($row[3]) and empty($row[4])) {
		$row[4] = $edata["date"];
		$css[4] = 1;
	}
	$row[5] = $edata["de_comp_initials_crf02b"];
	$row[6] = $edata["de_2_comp_initials_crf02b"];
	if (!empty($row[5]) and empty($row[6])) {
		$row[6] = $edata["date"];
		$css[6] = 1;
	}
	$row[7] = $edata["de_comp_initials_crf03_bl"];
	$row[8] = $edata["de_2_comp_initials_crf03_bl"];
	if (!empty($row[7]) and empty($row[8])) {
		$row[8] = $edata["date"];
		$css[8] = 1;
	}
	$row[9] = $edata["de_comp_initials_crf03_3m"];
	$row[10] = $edata["de_2_comp_initials_crf03_3m"];
	if (!empty($row[9]) and empty($row[10])) {
		$row[10] = $m3data["qtk_date_received"];
		$css[10] = 1;
	}
	$row[11] = $edata["de_comp_initials_crf04_a"];
	$row[12] = $edata["de_2_comp_initials_crf04_a"];
	if (!empty($row[11]) and empty($row[12])) {
		$row[12] = $m3data["qtk_date_received_2"];
		$css[12] = 1;
	}
	$row[13] = $edata["de_comp_initials_crf03_6m"];
	$row[14] = $edata["de_2_comp_initials_crf03_6m"];
	if (!empty($row[13]) and empty($row[14])) {
		$row[14] = $m6data["qtk_date_received"];
		$css[14] = 1;
	}
	$row[15] = $edata["de_comp_initials_crf04_b"];
	$row[16] = $edata["de_2_comp_initials_crf04_b"];
	if (!empty($row[15]) and empty($row[16])) {
		$row[16] = $m6data["qtk_date_received_2"];
		$css[16] = 1;
	}
	$row[17] = $edata["de_comp_initials_crf03_12m"];
	$row[18] = $edata["de_2_comp_initials_crf03_12m"];
	if (!empty($row[17]) and empty($row[18])) {
		$row[18] = $m12data["qtk_date_received"];
		$css[18] = 1;
	}
	$row[19] = $edata["de_comp_initials_crf05"];
	$row[20] = $edata["de_2_comp_initials_crf05"];
	if (!empty($row[19]) and empty($row[20])) {
		$row[20] = $edata["pati_x15"];
		$css[20] = 1;
	}
	
	$table["css"][] = $css;
	$table['content'][] = $row;
}
$content .= $dash->makeDDETable($table);


unset($table);