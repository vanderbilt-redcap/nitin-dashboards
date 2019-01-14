<?php

global $dash;
$content = "";

# get PT Scheduling table data from db
$params = [
	"project_id" => 63383,
	"return_format" => 'array',
	"events" => ["enrollment_arm_1", "baseline_arm_1"],
	"filterLogic" => "([baseline_arm_1][qtk_physical_therapy] <> '1') AND ([baseline_arm_1][qtk_physical_therapy] <> '0') AND ([enrollment_arm_1][pati_study_status]<>'0') AND (([enrollment_arm_1][randgroup]='2') OR (([enrollment_arm_1][randgroup]='1') AND ([enrollment_arm_1][pati_x15] <> '')))",
	"exportDataAccessGroups" => true
];

$records = \REDCap::getData($params);

die("<pre>" . print_r($records, true) . "</pre>");

// # add Physical Therapy Scheduling table
$table = [
	"title" => "Physical Therapy Scheduling",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Randomization/Surgery Date", "Call 1", "Call 2", "Call 3", "Site referral"],
	"content" => [
		["4-3285-119", "8/30/18", "9/10/18", "9/17/18", "9/21/18", "9/25/18"],
		["10-2284-23", "8/26/18", "9/09/18", "9/21/18", "9/25/18", "9/31/18"]
	]
];
$content .= $dash->makeDataTable($table);

// # add Surgery Scheduling table
$table = [
	"title" => "Surgery Scheduling",
	"titleClass" => "redHeader",
	"headers" => ["Site", "Study ID", "Surgery Scheduled Date", "Surgery Scheduling Notes"],
	"content" => [
		["Knoxville Orthopedic Clinic", "4-3285-119", "9/31/18", "Piped from patient study info overview"],
		["UTSW", "10-2284-23", "11/14/18", "Piped from patient study info overview"]
	]
];
$content .= $dash->makeDataTable($table);

// # add Delay of treatment deviations (upcoming) table
$table = [
	"title" => "Delay of treatment deviations (upcoming)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Treatment arm", "Delay of treatment effective date", "Surgery Scheduled Date (if applicable)"],
	"content" => [
		["4-3285-119", "operative", "10/30/18", "10/22/18"],
		["10-2284-23", "non-operative", "10/30/18", ""]
	]
];
$content .= $dash->makeDataTable($table);

// # add Delay of treatment deviations (need recorded) table
$table = [
	"title" => "Delay of treatment deviations (need recorded)",
	"titleClass" => "redHeader",
	"headers" => [],
	"content" => []
];
$content .= $dash->makeDataTable($table);


unset($table);