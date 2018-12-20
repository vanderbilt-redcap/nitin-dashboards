<?php

global $dash;
$content = "";

// # add Questionnaires To Send table
$table = [
	"title" => "Questionnaires To Send",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Time point", "Lower Window", "Ideal Date", "Upper Window"],
	"content" => [
		["4-3285-119", "3-months", "Piped from baseline qtk", "Piped from baseline qtk", "Piped from baseline qtk"],
		["10-2284-23", "6-months", "9/10/18", "9/17/18", "9/24/18"]
	]
];
$content .= $dash->makeDataTable($table);

// # add Physical Therapy Diaries to Send/Check table
$table = [
	"title" => "Physical Therapy Diaries to Send/Check",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "Diary type", "Time point"],
	"content" => [
		["4-3285-119", "", ""]
	]
];
$content .= $dash->makeDataTable($table);

// # add Follow-up Calls (Outstanding Questionnaires) table
$table = [
	"title" => "Follow-up Calls (Outstanding Questionnaires)",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Event Name", "Contact 1 approx. date due:", "Call 2 approx. date due", "Call 3 approx. date due", "PI referral approx. date due"],
	"content" => [
		["4-3285-119", "3-month", "Pipe from qtk", "Pipe from qtk", "Pipe from qtk", "Pipe from qtk"]
	]
];
$content .= $dash->makeDataTable($table);

// # add Follow-up Calls (Data Validation and/or Paper PT Diary) table
$table = [
	"title" => "Follow-up Calls (Data Validation and/or Paper PT Diary)",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "Event Name:", "Information to be validated/collected from patient: (pipe from dval)", "Call 1", "Call 2 (if needed)", "Call 3 (if needed)", "Site contact (if needed)"],
	"content" => [
		["4-3285-119", "", "1) ASES Qs 1-9 potentially reversed<br />2) Patient reports surgery @ site other than enrolling site", "", "", "", ""],
		["10-2284-23", "", "PT diary not rec'd with questionnaire or w/in 2 wks of E completion + SPADI Q 6 missing", "9/10/18", "9/17/18", "9/24/18", "9/31/18"]
	]
];
$content .= $dash->makeDataTable($table);


unset($table);