<?php

global $dash;
$content = "";

// # add Treating Physical Therapist Initial Info table
$table = [
	"title" => "Treating Physical Therapist Initial Info",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Medical release received?", "Site lead PT treating PT?", "Lead PT ready to be contacted?", "Treating PT ready to be contacted?"],
	"content" => [
		["4-3285-119", "", "", "", ""],
		["10-2284-23", "", "", "", ""]
	]
];
$content .= $dash->makeDataTable($table);

// # add Outstanding Lead PT Calls table
$table = [
	"title" => "Outstanding Lead PT Calls",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "Site Lead PT Treating PT?", "Treating Physical therapist successfully contacted?"],
	"content" => [
		["4-3285-119", "", ""],
		["10-2284-23", "", ""]
	]
];
$content .= $dash->makeDataTable($table);

// # add PT Reports to send table
$table = [
	"title" => "PT Reports to send",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Time point", "Lower Window", "Ideal Date"],
	"content" => [
		["4-3285-119", "1-month", "10/30/18", "10/22/18"],
		["10-2284-23", "3-month", "10/30/18", ""]
	]
];
$content .= $dash->makeDataTable($table);

// # add PT Reports (follow-up) table
$table = [
	"title" => "PT Reports (follow-up)",
	"titleClass" => "redHeader",
	"headers" => [],
	"content" => []
];
$content .= $dash->makeDataTable($table);


unset($table);