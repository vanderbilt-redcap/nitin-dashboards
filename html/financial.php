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
	"title" => "Site Recruitment Compensation",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Site submit invoice?", "Date invoice received", "Date submitted", "Site successfully paid?"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$rgroup = $edata['randgroup'];
	if ($rgroup <> '') {
		$row = [];
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->projEvents[$dash->enrollmentEID];
		$row[3] = $dash->labelizeValue('mritk_recruitment_invoice', $edata['mritk_recruitment_invoice']);
		$row[4] = $edata['mritk_invoice_recd'];
		$row[5] = $edata['mritk_invoice_submitted_date'];
		$row[6] = $dash->labelizeValue('mritk_invoice_vumc_paid', $edata['mritk_invoice_vumc_paid']);
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Check Requests Due",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Today's Date", "Data entry method"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if (
		($data['qtk_check_request_submitted'] == '' or 
		$data['qtk_check_request_submitted'] == '2') and
		$data['qtk_questionnaire_received'] == '1'
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $edata['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = $data['patc_a1'];
			$row[4] = $dash->labelizeValue('dem_crf03_1', $data['dem_crf03_1']);
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Check Request Reconciliation",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "First Name", "Last Name", "Event Name", "Date Questionnaire Received", "Check Request #", "Check request submitted?", "Check Request Submission date", "Patient successfully paid?", "Date payment processed (accounting)", "Date check cleared"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if ($data['qtk_questionnaire_received'] == '1') {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $edata['pati_6'];
			$row[2] = $data['patc_a2'];
			$row[3] = $data['patc_a3'];
			$row[4] = $dash->projEvents[$eid];
			$row[5] = $data['qtk_date_received'];
			$row[6] = $data['qtk_check_request_number'];
			$row[7] = $dash->labelizeValue('qtk_check_request_submitted', $data['qtk_check_request_submitted']);
			$row[8] = $data['qtk_check_request_date'];
			$row[9] = $dash->labelizeValue('qtk_patient_paid', $data['qtk_patient_paid']);
			$row[10] = $data['qtk_date_payment_processed'];
			$row[11] = $data['qtk_date_check_cleared'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);


unset($table);