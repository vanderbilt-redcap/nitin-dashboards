<?php

global $dash;
$content = "";
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"fields" => [
		'enrollment_id',
		'patc_a2',
		'mritk_invoice_recd',
		'pati_6',
		'qtk_date_received',
		'date',
		'patc_a3',
		'mritk_invoice_vumc_paid',
		'dem_crf03_1',
		'qtk_questionnaire_received',
		'mritk_recruitment_invoice',
		'mritk_invoice_vumc',
		'randgroup',
		'mritk_invoice_submitted_date',
		'mritk_invoice_vumc_paid',
		'qtk_check_request_notes',
		'qtk_patient_paid',
		'study_id',
		'qtk_date_check_cleared',
		'qtk_date_payment_processed',
		'qtk_check_request_date',
		'qtk_check_request_number',
		'qtk_date_received',
		'mritk_recruitment_invoice_number',
		'patc_a1',
		'qtk_check_request_submitted',
		'mritk_recruitment_invoice'
	],
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Site Recruitment Fees Pending",
	"titleClass" => "blueHeader",
	// "headers" => ["Study ID", "DAG", "Event Name", "Site submit invoice?", "Date invoice received", "Date submitted", "Site successfully paid?"],
	"headers" => ["Study ID", "DAG", "Event Name", "Randomization Date", "Site submit invoice?", "Invoice Number", "Date invoice received", "Invoice submitted to finance?", "Date submitted", "Site successfully paid?"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	$rgroup = $edata['randgroup'];
	if ($rgroup <> '' AND
		$edata["mritk_recruitment_invoice"] == "1" AND
		$edata["mritk_invoice_vumc_paid"] <> "1") {
		$row = array_fill(0, count($table['headers']), "");
		$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
		$row[1] = $edata['pati_6'];
		$row[2] = $dash->projEvents[$dash->enrollmentEID];
		$row[3] = $edata["date"];
		$row[4] = $dash->labelizeValue('mritk_recruitment_invoice', $edata['mritk_recruitment_invoice']);
		$row[5] = $edata['mritk_recruitment_invoice_number'];
		$row[6] = $edata['mritk_invoice_recd'];
		$row[7] = $dash->labelizeValue('mritk_invoice_vumc', $edata['mritk_invoice_vumc']);
		$row[8] = $edata['mritk_invoice_submitted_date'];
		$row[9] = $dash->labelizeValue('mritk_invoice_vumc_paid', $edata['mritk_invoice_vumc_paid']);
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Check Requests Due",
	"titleClass" => "redHeader",
	// "headers" => ["Study ID", "DAG", "Event Name", "Today's Date", "Data entry method"],
	"headers" => ["Study ID", "DAG", "Event Name", "Today's Date", "Date Questionnaire Received", "Data entry method", "Check Request Notes (if any)"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if (
			$data['qtk_check_request_submitted'] == '' and
			$data['qtk_questionnaire_received'] == '1'
		) {
			$row = array_fill(0, count($table['headers']), "");
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $edata['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = $data['patc_a1'];
			$row[4] = $data["qtk_date_received"];
			$row[5] = $dash->labelizeValue('dem_crf03_1', $data['dem_crf03_1']);
			$row[6] = $data["qtk_check_request_notes"];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

// ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// $table = [
	// "title" => "Check Request Reconciliation",
	// "titleClass" => "blueHeader",
	// "headers" => ["Study ID", "DAG", "First Name", "Last Name", "Event Name", "Date Questionnaire Received", "Check Request #", "Check request submitted?", "Check Request Submission date", "Patient successfully paid?", "Date payment processed (accounting)", "Date check cleared"],
	// "content" => []
// ];
// foreach ($records as $i => $record) {
	// $edata = $record[$dash->enrollmentEID];
	// foreach ($record as $eid => $data) {
		// if ($data['qtk_questionnaire_received'] == '1') {
			// $row = array_fill(0, count($table['headers']), "");
			// $row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			// $row[1] = $edata['pati_6'];
			// $row[2] = $data['patc_a2'];
			// $row[3] = $data['patc_a3'];
			// $row[4] = $dash->projEvents[$eid];
			// $row[5] = $data['qtk_date_received'];
			// $row[6] = $data['qtk_check_request_number'];
			// $row[7] = $dash->labelizeValue('qtk_check_request_submitted', $data['qtk_check_request_submitted']);
			// $row[8] = $data['qtk_check_request_date'];
			// $row[9] = $dash->labelizeValue('qtk_patient_paid', $data['qtk_patient_paid']);
			// $row[10] = $data['qtk_date_payment_processed'];
			// $row[11] = $data['qtk_date_check_cleared'];
			
			// $table['content'][] = $row;
		// }
	// }
// }
// $content .= $dash->makeDataTable($table);


unset($table);