<?php

global $dash;
$content = "";

// # add Enrollment Documents table
$table = [
	"title" => "Enrollment Documents",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Event Name", "These documents MUST be uploaded BY:"],
	"content" => [
		["4-3285-119", "3-months", "Piped from baseline qtk"],
		["10-2284-23", "6-months", "9/10/18"]
	]
];
$content .= $dash->makeDataTable($table);

// # add Surgical Documents table
$table = [
	"title" => "Surgical Documents",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "Actual Surgery Date:", "Actual Surgery Date (crossover)"],
	"content" => [
		["4-3285-119", "", ""],
		["10-2284-23", "", ""]
	]
];
$content .= $dash->makeDataTable($table);

// # add Data Validation table
$table = [
	"title" => "Data Validation",
	"titleClass" => "blueHeader",
	"headers" => ["Study ID", "Event Name", "Information to be validated/ collected from site:", "Call 1", "Call 2", "Call 3 (if needed)"],
	"content" => [
		["4-3285-119", "", "", "", "", ""],
		["10-2284-23", "", "", "9/10/18", "9/17/18", "9/24/18"]
	]
];
$content .= $dash->makeDataTable($table);

// # add Treatment Deviation Monitoring (Crossovers/No Treatment) table
$table = [
	"title" => "Treatment Deviation Monitoring (Crossovers/No Treatment)",
	"titleClass" => "redHeader",
	"headers" => [],
	"content" => []
];
$content .= $dash->makeDataTable($table);

// # add Double Data Entry table
$table = [
	"title" => "Double Data Entry",
	"titleClass" => "blueHeader",
	"headers" => [],
	"content" => []
];
$content .= $dash->makeDataTable($table);


unset($table);