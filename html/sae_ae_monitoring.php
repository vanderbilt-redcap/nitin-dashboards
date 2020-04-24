<?php

global $dash;
$content = "";

$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"fields" => [
		"evt_report_to_sponsor",
		"enrollment_id",
		"evt_irb_report",
		"evt_irb_submitted",
		"study_id",
		"evt_report_date",
		"evt_event_type",
		"evt_severity",
		"evt_unanticipated",
		"evt_related_to",
		"evt_risk",
		"evt_outcome_status",
		"evt_notify_study_coord",
		"events_saeae_complete",
		"evt_event_name",
		"evt_coord_review",
		"evt_event_class",
		"redcap_event_name",
		"pati_6",
		"evt_irb_cr_report"
	],
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);

$today = date('Y-m-d');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "Timely SAE report evaluation in progress (all stages)",
	"titleClass" => "redHeader",
	"headers" => [
		"Study ID",
		"Report Date",
		"Event Type",
		"Was the event SERIOUS?",
		"Was the event UNFORTUNATE?",
		"Was the event RELATED TO the research procedures?",
		"Did the event involve RISK?",
		"Outcome/Status",
		"Event ready for final action?",
		"Event meets criteria for expedited IRB reporting?",
		"Expedited report submitted to IRB?"
	],
	"content" => [],
	"attributes" => [
		"order-col" => 1,
		"order-direction" => "asc"
	]
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record['repeat_instances'][$dash->otherEID]['events_saeae'] as $repeatInstance => $data) {
		if (
			($data['evt_report_to_sponsor'] == "1") and 
			($data['evt_irb_report'] <> "0") and 
			($data['evt_irb_submitted'] <> "1")
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $data['evt_report_date'];
			$row[2] = $this->labelizeValue("evt_event_type", $data['evt_event_type']);
			$row[3] = $this->labelizeValue("evt_severity", $data['evt_severity']);
			$row[4] = $this->labelizeValue("evt_unanticipated", $data['evt_unanticipated']);
			$row[5] = $this->labelizeValue("evt_related_to", $data['evt_related_to']);
			$row[6] = $this->labelizeValue("evt_risk", $data['evt_risk']);
			$row[7] = $this->labelizeValue("evt_outcome_status", $data['evt_outcome_status']);
			$row[8] = $this->labelizeValue("evt_notify_study_coord", $data['evt_notify_study_coord']);
			$row[9] = $this->labelizeValue("evt_irb_report", $data['evt_irb_report']);
			$row[10] = $this->labelizeValue("evt_irb_submitted", $data['evt_irb_submitted']);
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "AE reports started by sites but not submitted",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Event Name", "Event Type"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record['repeat_instances'][$dash->otherEID]['events_saeae'] as $repeatInstance => $data) {
		if (
			($data['events_saeae_complete'] == "0" OR
			$data['events_saeae_complete'] == "1" OR
			$data['events_saeae_complete'] == "2") AND
			($data['evt_report_to_sponsor'] == "")
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $this->labelizeValue("evt_event_name", $data['evt_event_name']);
			$row[2] = $this->labelizeValue("evt_event_type", $data['evt_event_type']);
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "AE reports for initial review by coordinator",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "Event Type", "Does this event need to be reported to the Study Primary Investigator in an expedited manner (per protocol)?"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record['repeat_instances'][$dash->otherEID]['events_saeae'] as $repeatInstance => $data) {
		if (
			($data['evt_report_to_sponsor'] == "0" OR
			$data['evt_report_to_sponsor'] == "2") AND
			($data['evt_coord_review'] <> "1")
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $this->labelizeValue("evt_event_type", $data['evt_event_type']);
			$row[2] = $this->labelizeValue("evt_report_to_sponsor", $data['evt_report_to_sponsor']);
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "AE reports for final review by coordinator",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Event Type"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record['repeat_instances'][$dash->otherEID]['events_saeae'] as $repeatInstance => $data) {
		if (($data['evt_notify_study_coord'] == "1") AND ($data['evt_irb_report'] == "")) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $this->labelizeValue("evt_event_type", $data['evt_event_type']);
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "AEs to be reported at CR (PI + coord reviews complete)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "Data Access Group", "Event Class (based on Event Type)", "Event Type", "Has this event been reported to the central IRB at annual continuing review?"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record['repeat_instances'][$dash->otherEID]['events_saeae'] as $repeatInstance => $data) {
		if (
			($data['evt_irb_report'] == "0")
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $this->labelizeValue("pati_6", $edata['pati_6']);
			$row[2] = $this->labelizeValue("evt_event_class", $data['evt_event_class']);
			$row[3] = $this->labelizeValue("evt_event_type", $data['evt_event_type']);
			$row[4] = $this->labelizeValue("evt_irb_cr_report", $data['evt_irb_cr_report']);
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "AEs to be reviewed by PI prior to continuing review",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Data Access Group", "Event Type"],
	"content" => []
];
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record['repeat_instances'][$dash->otherEID]['events_saeae'] as $repeatInstance => $data) {
		if (
			($data['evt_notify_study_coord'] <> "1") and
			($data['evt_report_to_sponsor'] == "0")
		) {
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a> " . $edata['study_id'];
			$row[1] = $this->labelizeValue("pati_6", $edata['pati_6']);
			$row[2] = $this->labelizeValue("evt_event_type", $data['evt_event_type']);
			
			$table['content'][] = $row;
		}
	}
}
$content .= $dash->makeDataTable($table);
