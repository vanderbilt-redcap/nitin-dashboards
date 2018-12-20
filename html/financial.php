<?php

global $dash;
$content = "";

// # add Site Recruitment Compensation table
$table = [
	"title" => "Site Recruitment Compensation",
	"titleClass" => "blueHeader",
	"headers" => ["Enrolling Site", "Study ID", "Event Name", "Site submit invoice?", "Date invoice received", "Date submitted", "Has the site sucessfully been paid?"],
	"content" => [
		["Daisy Duck", "4-3285-119", "Baseline", "Piped from qtk", "Piped from qtk", "Piped from qtk", "Piped from qtk"],
		["Daffy Duck", "10-2284-23", "6-months", "9/10/18", "9/17/18", "Yes", "9/31/18"]
	]
];
$content .= $dash->makeDataTable($table);

// # add Check Requests Due table
$table = [
	"title" => "Check Requests Due",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "Event Name", "Today's Date", "Data entry method"],
	"content" => [
		["4-3285-119", "", "", ""],
		["10-2284-23", "", "", ""]
	]
];
$content .= $dash->makeDataTable($table);

// # add Check Request Reconciliation table
$table = [
	"title" => "Check Request Reconciliation",
	"titleClass" => "blueHeader",
	"headers" => ["Patient Name", "Study ID", "Event Name", "Check Request #", "Check Request Submission date", "Patient successfully paid?", "Date payment processed (accounting)", "Date check cleared"],
	"content" => [
		["Daisy Duck", "4-3285-119", "Baseline", "Piped from qtk", "Piped from qtk", "Piped from qtk", "Piped from qtk", "Piped from qtk"],
		["Daffy Duck", "10-2284-23", "6-months", "9/10/18", "9/17/18", "Yes", "9/31/18", "9/19/18"]
	]
];
$content .= $dash->makeDataTable($table);


unset($table);