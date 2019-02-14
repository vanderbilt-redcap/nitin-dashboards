<?php

global $dash;
$content = "";

$imaging = [
	"project" => new \Project(IMAGING_PID),
	"events" => \Event::getEventsByProject(IMAGING_PID)
];
$imaging['dags'] = $imaging['project']->getGroups();
$imaging['enrollmentEID'] = array_search("Enrollment", $imaging['events']);
$subjectParams = [
	"project_id" => SUBJECT_PID,
	"return_format" => 'array',
	"events" => [$dash->enrollmentEID],
	"exportDataAccessGroups" => true
];
$imagingParams = [
	"project_id" => IMAGING_PID,
	"return_format" => 'array',
	"events" => [$imaging->enrollmentEID],
	"exportDataAccessGroups" => true
];
$subjectRecords = \REDCap::getData($subjectParams);
$imagingRecords = \REDCap::getData($imagingParams);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Site MRIs/X-rays to be sent to VUMC",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization date", "Date of MRI"],
	"content" => []
];
foreach ($subjectRecords as $i => $s_record) {
	$s_data = reset($s_record);
	if ($s_data['pati_x19'] <> '1' and $s_data['study_id'] <> '') {
		$row = [];
		$row[0] = "<a href = \"" . $dash->imagingRecordHome . "$i\">" . $s_data['enrollment_id'] . "</a> " . $s_data['study_id'];
		$row[1] = $s_data['pati_6'];
		$row[2] = $s_data['date'];
		$row[3] = $s_data['pati_x17'];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "MRIs/x-rays sent to coordinating center (not yet received)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization date", "Date sent to VUMC"],
	"content" => []
];
foreach ($subjectRecords as $i => $s_record) {
	$s_data = reset($s_record);
	if ($s_data['pati_x19'] == '1' and $s_data['pati_x20'] <> '1') {
		$row = [];
		$row[0] = "<a href = \"" . $dash->imagingRecordHome . "$i\">" . $s_data['enrollment_id'] . "</a> " . $s_data['study_id'];
		$row[1] = $s_data['pati_6'];
		$row[2] = $s_data['date'];
		$row[3] = $s_data['pati_x19_1'];
		
		$table['content'][] = $row;
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "MRIs/x-rays to be de-identified  (received, not yet de-identified)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization date", "Date sent to VUMC"],
	"content" => []
];
foreach ($subjectRecords as $i => $s_record) {
	$i_record = $imagingRecords[$i];
	if ($s_record and $i_record) {
		$s_data = reset($s_record);
		$i_data = reset($i_record);
		if (($s_data['pati_x20'] == '1' or $i_data['img_mri_avail'] == '1') and $i_data['img_mri_deidentified'] <> '1') {
			$row = [];
			$row[0] = "<a href = \"" . $dash->imagingRecordHome . "$i\">" . $s_data['enrollment_id'] . "</a> " . $s_data['study_id'];
			$row[1] = $s_data['pati_6'];
			$row[2] = $s_data['date'];
			$row[3] = $s_data['pati_x19_1'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "MRIs/x-rays to be given to radiologist (de-identified, not yet delivered)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "DAG", "Randomization date", "Date sent to VUMC"],
	"content" => []
];
foreach ($subjectRecords as $i => $s_record) {
	$i_record = $imagingRecords[$i];
	if ($s_record and $i_record) {
		$s_data = reset($s_record);
		$i_data = reset($i_record);
		if ($i_data['img_mri_ready_to_review'] == '1' and $i_data['img_disc_given'] <> '1') {
			$row = [];
			$row[0] = "<a href = \"" . $dash->imagingRecordHome . "$i\">" . $s_data['enrollment_id'] . "</a> " . $s_data['study_id'];
			$row[1] = $s_data['pati_6'];
			$row[2] = $s_data['date'];
			$row[3] = $s_data['pati_x19_1'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "MRIs/x-rays to be read by radiologist (delivered, not yet completed)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Randomization date", "Date sent to VUMC"],
	"content" => []
];
foreach ($subjectRecords as $i => $s_record) {
	$i_record = $imagingRecords[$i];
	if ($s_record and $i_record) {
		$s_data = reset($s_record);
		$i_data = reset($i_record);
		if ($i_data['img_disc_given'] == '1' and $i_data['img_radiologist_review'] <> '1') {
			$row = [];
			$row[0] = "<a href = \"" . $dash->imagingRecordHome . "$i\">" . $s_data['enrollment_id'] . "</a> " . $s_data['study_id'];
			$row[1] = $s_data['pati_6'];
			$row[2] = $s_data['date'];
			$row[3] = $s_data['pati_x19_1'];
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

unset($table);