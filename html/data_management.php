<?php

global $dash;

$content = "";
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"fields" => [
		'sdoc_initial_due',
		'sdoc_vumc_cert',
		'enrollment_id',
		'study_id',
		'pati_6',
		'randgroup',
		'date',
		'sdoc_vumc_cert_2',
		'surgery_report_form_crf05_complete',
		'pati_x15',
		'sdoc_surgical_due',
		'recruit_md_name',
		'sdoc_sx_note',
		'sdoc_sxr',
		'dem_crf_05',
		'dval_action_needed',
		'dval_res_ra',
		'dval_specify_research',
		'dval_res_notes',
		'dval_issue_disc_date',
		'dval_contact_date_1',
		'dval_contact_date_2',
		'dval_contact_date_3',
		'dval_contact_date_4',
		'dval_contact_date_5',
		'qtk_pdv_effective_date',
		'qtk_physical_therapy',
		'pati_surgical_sched_notes',
		'de_comp_crf00',
		'de_comp_crf02a',
		'de_comp_crf02b',
		'de_comp_crf03_bl',
		'de_comp_crf05',
		'de_comp_crf03_3m',
		'de_comp_crf04_a',
		'de_comp_crf03_6m',
		'de_comp_crf04_b',
		'de_comp_crf03_12m',
		'de_2_comp_crf00',
		'de_2_comp_crf02a',
		'de_2_comp_crf02b',
		'de_2_comp_crf03_bl',
		'de_2_comp_crf05',
		'de_2_comp_crf03_3m',
		'de_2_comp_crf04_a',
		'de_2_comp_crf03_6m',
		'de_2_comp_crf04_b',
		'de_2_comp_crf03_12m',
		'de_comp_initials_crf00',
		'de_comp_initials_crf02a',
		'de_comp_initials_crf02b',
		'de_comp_initials_crf03_bl',
		'de_comp_initials_crf05',
		'de_comp_initials_crf03_3m',
		'de_comp_initials_crf04_a',
		'de_comp_initials_crf03_6m',
		'de_comp_initials_crf04_b',
		'de_comp_initials_crf03_12m',
		'de_2_comp_initials_crf00',
		'de_2_comp_initials_crf02a',
		'de_2_comp_initials_crf02b',
		'de_2_comp_initials_crf03_bl',
		'de_2_comp_initials_crf05',
		'de_2_comp_initials_crf03_3m',
		'de_2_comp_initials_crf04_a',
		'de_2_comp_initials_crf03_6m',
		'de_2_comp_initials_crf04_b',
		'de_2_comp_initials_crf03_12m',
		'qtk_date_received',
		'qtk_date_received_2',
		'elg_sign4',
		'sdoc_icf',
		'sdoc_pt_release',
		'sdoc_csf',
		'sdoc_baseline',
		'dem_crf03_1',
		'q_patient_contact_info_crf03_section_1_complete',
		'sdoc_pex_form',
		'dem_crf02',
		'physical_examination_form_part_a_crf021_complete',
		'sdoc_pex_form_2',
		'dem_crf02_2',
		'physical_examination_form_part_b_crf022_complete'
	],
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
$today = date("Y-m-d");
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Enrollment Document Collection",
	"titleClass" => "blueHeader",
	"headers" => [
		"Study ID",
		"DAG",
		"Randomization Group",
		"Randomization date:",
		"These documents MUST be uploaded BY:",
		"Days since date due:",
		"Recruiting Physician",
		"Consent Form",
		"PT Medical Info Release Form",
		"Clinic Screening Form",
		"Baseline Questionnaire",
		"Data Entry Method",
		"Complete?",
		"Physical Exam Form A",
		"Data Entry Method",
		"Complete?",
		"Physical Exam Form b",
		"Data Entry Method",
		"Complete?"
	],
	"content" => [],
	"attributes" => [
		"order-col" => 5,
		"order-direction" => "desc"
	]
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$bdata = $record[$dash->baselineEID];
	if (
		$edata['sdoc_initial_due'] <> '' and $edata['sdoc_vumc_cert'] <> '1' or $bdata['physical_examination_form_part_a_crf021_complete'] == "0"
	) {
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->labelizeValue('randgroup', $edata['randgroup']);
		$row[3] = $edata['date'];
		$row[4] = $edata['sdoc_initial_due'];
		$row[5] = 0;
		if (!empty($row[4])) {
			$row[5] = date_diff(date_create($row[4]), date_create($today))->format("%a");
			if ($today < $row[4])
				$row[5] *= -1;
		}
		
		$row[6] = $dash->labelizeValue('elg_sign4', $edata['elg_sign4']);
		
		// edoc section
		$row[7] = "";
		if (!empty($edata['sdoc_icf'])) {
			$hash = \Files::docIdHash($edata['sdoc_icf']);
			$url = APP_PATH_WEBROOT . "DataEntry/file_download.php?pid=" . $dash->pid . "&record=$i&event_id=" . $dash->enrollmentEID . "&instance=1&field_name=sdoc_icf&id=" . $edata['sdoc_icf'] . "&doc_id_hash=$hash";
			$row[7] = "<button onclick='window.open(\"$url\",\"_blank\");'>Download</button>";
		}
		$row[8] = "";
		if (!empty($edata['sdoc_pt_release'])) {
			$hash = \Files::docIdHash($edata['sdoc_pt_release']);
			$url = APP_PATH_WEBROOT . "DataEntry/file_download.php?pid=" . $dash->pid . "&record=$i&event_id=" . $dash->enrollmentEID . "&instance=1&field_name=sdoc_pt_release&id=" . $edata['sdoc_pt_release'] . "&doc_id_hash=$hash";
			$row[8] = "<button onclick='window.open(\"$url\",\"_blank\");'>Download</button>";
		}
		$row[9] = "";
		if (!empty($edata['sdoc_csf'])) {
			$hash = \Files::docIdHash($edata['sdoc_csf']);
			$url = APP_PATH_WEBROOT . "DataEntry/file_download.php?pid=" . $dash->pid . "&record=$i&event_id=" . $dash->enrollmentEID . "&instance=1&field_name=sdoc_csf&id=" . $edata['sdoc_csf'] . "&doc_id_hash=$hash";
			$row[9] = "<button onclick='window.open(\"$url\",\"_blank\");'>Download</button>";
		}
		$row[10] = "";
		if (!empty($edata['sdoc_baseline'])) {
			$hash = \Files::docIdHash($edata['sdoc_baseline']);
			$url = APP_PATH_WEBROOT . "DataEntry/file_download.php?pid=" . $dash->pid . "&record=$i&event_id=" . $dash->enrollmentEID . "&instance=1&field_name=sdoc_baseline&id=" . $edata['sdoc_baseline'] . "&doc_id_hash=$hash";
			$row[10] = "<button onclick='window.open(\"$url\",\"_blank\");'>Download</button>";
		}
		
		$row[11] = $dash->labelizeValue('dem_crf03_1', $bdata['dem_crf03_1']);
		$row[12] = $dash->labelizeValue('q_patient_contact_info_crf03_section_1_complete', $bdata['q_patient_contact_info_crf03_section_1_complete']);
		
		// another edoc form
		$row[13] = "";
		if (!empty($edata['sdoc_pex_form'])) {
			$hash = \Files::docIdHash($edata['sdoc_pex_form']);
			$url = APP_PATH_WEBROOT . "DataEntry/file_download.php?pid=" . $dash->pid . "&record=$i&event_id=" . $dash->enrollmentEID . "&instance=1&field_name=sdoc_pex_form&id=" . $edata['sdoc_pex_form'] . "&doc_id_hash=$hash";
			$row[13] = "<button onclick='window.open(\"$url\",\"_blank\");'>Download</button>";
		}
		
		$row[14] = $dash->labelizeValue('dem_crf02', $bdata['dem_crf02']);
		$row[15] = $dash->labelizeValue('physical_examination_form_part_a_crf021_complete', $bdata['physical_examination_form_part_a_crf021_complete']);
		
		// another edoc form
		$row[16] = "";
		if (!empty($edata['sdoc_pex_form_2'])) {
			$hash = \Files::docIdHash($edata['sdoc_pex_form_2']);
			$url = APP_PATH_WEBROOT . "DataEntry/file_download.php?pid=" . $dash->pid . "&record=$i&event_id=" . $dash->enrollmentEID . "&instance=1&field_name=sdoc_pex_form_2&id=" . $edata['sdoc_pex_form_2'] . "&doc_id_hash=$hash";
			$row[16] = "<button onclick='window.open(\"$url\",\"_blank\");'>Download</button>";
		}
		
		$row[17] = $dash->labelizeValue('dem_crf02_2', $bdata['dem_crf02_2']);
		$row[18] = $dash->labelizeValue('physical_examination_form_part_b_crf022_complete', $bdata['physical_examination_form_part_b_crf022_complete']);
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Surgical Document Collection",
	"titleClass" => "redHeader",
	"headers" => [
		"Study ID",
		"DAG",
		"Randomization date",
		"Actual Surgery Date:",
		"These documents MUST be uploaded BY:",
		"Recruiting Physician",
		"Surgical Note (uploaded for ALL patients w/surgery",
		"Surgery Report Form (if paper)",
		"Surgery Report Form Data Entry Method",
		"Surgery Report Form Completion Status (if direct data entry)",
		"Days since date due:"
	],
	"content" => [],
	"attributes" => [
		"order-col" => 10,
		"order-direction" => "desc"
	]
];

foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$otherdata = $record[$dash->otherEID];
	
	if (
		($edata['sdoc_vumc_cert_2'] <> '1' or
		$otherdata['surgery_report_form_crf05_complete'] <> '2') and 
		$edata['pati_x15'] <> ''
	) {
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $edata['date'];
		$row[3] = $edata['pati_x15'];
		$row[4] = $edata['sdoc_surgical_due'];
		$row[5] = $edata['recruit_md_name'];
		
		// surg doc note field
		$row[6] = "";
		if (!empty($edata['sdoc_sx_note'])) {
			$hash = \Files::docIdHash($edata['sdoc_sx_note']);
			$url = APP_PATH_WEBROOT . "DataEntry/file_download.php?pid=" . $dash->pid . "&record=$i&event_id=" . $dash->enrollmentEID . "&instance=1&field_name=sdoc_sx_note&id=" . $edata['sdoc_sx_note'] . "&doc_id_hash=$hash";
			$row[6] = "<button onclick='window.open(\"$url\",\"_blank\");'>Download</button>";
		}
		
		$row[7] = $dash->labelizeValue('sdoc_sxr', $otherdata['sdoc_sxr']);
		$row[8] = $dash->labelizeValue('dem_crf_05', $otherdata['dem_crf_05']);
		$row[9] = $dash->labelizeValue('surgery_report_form_crf05_complete', $otherdata['surgery_report_form_crf05_complete']);
		$row[10] = 0;
		
		if (!empty($row[4])) {
			$row[10] = date_diff(date_create($row[4]), date_create($today))->format("%a");
			if ($today < $row[4])
				$row[10] *= -1;
		}
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Data Validation (Sites/Personnel)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Event", "Instance", "Information to be validated", "Resolution notes", "Date issue(s) discovered", "Contact 1 Date", "Contact 2 Date", "Contact 3 Date", "Contact 4 Date", "Contact 5 Date", "Days since last contact attempt:"],
	"content" => [],
	"attributes" => [
		"order-col" => 12,
		"order-direction" => "desc"
	]
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record['repeat_instances'][$dash->baselineEID]['data_collectionvalidation'] as $repeatInstance => $data) {
		// logic from "Data Validation (site)"
		// ([dval_action_needed] = "1") AND ([dval_res_ra] = "2")
		// if ($data['dval_action_needed'] == '1' or $record[$dash->baselineEID]['dval_res_ra'] == '2') exit('2');
		if ($data['dval_action_needed'] == '1' and $data['dval_res_ra'] == '2') {
			$row = array_fill(0, count($table['headers']), "");
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
			$row[12] = 0;
			
			$mostRecent = max($row[7], $row[8], $row[9], $row[10], $row[11]);
			if (!empty($mostRecent)) {
				$row[12] = date_diff(date_create($mostRecent), date_create($today))->format("%a");
				if ($today < $mostRecent)
					$row[12] *= -1;
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
	"content" => [],
	"attributes" => [
		"order-col" => 3,
		"order-direction" => "asc"
	]
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
		$row = array_fill(0, count($table['headers']), "");
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
	"content" => [],
	"attributes" => [
		"order-col" => 3,
		"order-direction" => "asc"
	]
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
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->labelizeValue('randgroup', $edata['randgroup']);
		$row[3] = $edata['qtk_pdv_effective_date'];
		$row[4] = $edata['pati_surgical_sched_notes'];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
	$otherdata = $record[$dash->otherEID];
	
	if (
		($otherdata['de_comp_crf00'] == "1" AND
		$otherdata['de_2_comp_crf00'] == "") OR
		($otherdata['de_comp_crf02a'] == "1" AND
		$otherdata['de_2_comp_crf02a'] == "") OR
		($otherdata['de_comp_crf02b'] == "1" AND
		$otherdata['de_2_comp_crf02b'] == "") OR
		($otherdata['de_comp_crf03_bl'] == "1" AND
		$otherdata['de_2_comp_crf03_bl'] == "") OR
		($otherdata['de_comp_crf05'] == "1" AND
		$otherdata['de_2_comp_crf05'] == "") OR
		($otherdata['de_comp_crf03_3m'] == "1" AND
		$otherdata['de_2_comp_crf03_3m'] == "") OR
		($otherdata['de_comp_crf04_a'] == "1" AND
		$otherdata['de_2_comp_crf04_a'] == "") OR
		($otherdata['de_comp_crf03_6m'] == "1" AND
		$otherdata['de_2_comp_crf03_6m'] == "") OR
		($otherdata['de_comp_crf04_b'] == "1" AND
		$otherdata['de_2_comp_crf04_b'] == "") OR
		($otherdata['de_comp_crf03_12m'] == "1" AND
		$otherdata['de_2_comp_crf03_12m'] == "")
	) {
		$row = array_fill(0, count($table['headers']), "");
		$css = [];
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $otherdata["de_comp_initials_crf00"];
		$row[2] = $otherdata["de_2_comp_initials_crf00"];
		if (!empty($row[1]) and empty($row[2])) {
			$row[2] = $edata["date"];
			$css[2] = 1;
		}
		$row[3] = $otherdata["de_comp_initials_crf02a"];
		$row[4] = $otherdata["de_2_comp_initials_crf02a"];
		if (!empty($row[3]) and empty($row[4])) {
			$row[4] = $edata["date"];
			$css[4] = 1;
		}
		$row[5] = $otherdata["de_comp_initials_crf02b"];
		$row[6] = $otherdata["de_2_comp_initials_crf02b"];
		if (!empty($row[5]) and empty($row[6])) {
			$row[6] = $edata["date"];
			$css[6] = 1;
		}
		$row[7] = $otherdata["de_comp_initials_crf03_bl"];
		$row[8] = $otherdata["de_2_comp_initials_crf03_bl"];
		if (!empty($row[7]) and empty($row[8])) {
			$row[8] = $edata["date"];
			$css[8] = 1;
		}
		$row[9] = $otherdata["de_comp_initials_crf03_3m"];
		$row[10] = $otherdata["de_2_comp_initials_crf03_3m"];
		if (!empty($row[9]) and empty($row[10])) {
			$row[10] = $m3data["qtk_date_received"];
			$css[10] = 1;
		}
		$row[11] = $otherdata["de_comp_initials_crf04_a"];
		$row[12] = $otherdata["de_2_comp_initials_crf04_a"];
		if (!empty($row[11]) and empty($row[12])) {
			$row[12] = $m3data["qtk_date_received_2"];
			$css[12] = 1;
		}
		$row[13] = $otherdata["de_comp_initials_crf03_6m"];
		$row[14] = $otherdata["de_2_comp_initials_crf03_6m"];
		if (!empty($row[13]) and empty($row[14])) {
			$row[14] = $m6data["qtk_date_received"];
			$css[14] = 1;
		}
		$row[15] = $otherdata["de_comp_initials_crf04_b"];
		$row[16] = $otherdata["de_2_comp_initials_crf04_b"];
		if (!empty($row[15]) and empty($row[16])) {
			$row[16] = $m6data["qtk_date_received_2"];
			$css[16] = 1;
		}
		$row[17] = $otherdata["de_comp_initials_crf03_12m"];
		$row[18] = $otherdata["de_2_comp_initials_crf03_12m"];
		if (!empty($row[17]) and empty($row[18])) {
			$row[18] = $m12data["qtk_date_received"];
			$css[18] = 1;
		}
		$row[19] = $otherdata["de_comp_initials_crf05"];
		$row[20] = $otherdata["de_2_comp_initials_crf05"];
		if (!empty($row[19]) and empty($row[20])) {
			$row[20] = $edata["pati_x15"];
			$css[20] = 1;
		}
		$table["css"][] = $css;
		$table['content'][] = $row;
	}
	
}
$content .= $dash->makeDDETable($table);


unset($table);