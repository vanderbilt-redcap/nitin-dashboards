<?php

# add summary table
$content = "<table class='summaryTable'>
	<thead>
		<th>Follow-Up Status Summary</th>
		<th></th>
	</thead>
	<tbody>";
$rows = [
	["Patients with txt info outstanding:", 8],
	["PT reports to be sent:", 7],
	["PT reports pending:", 5],
	["Questionnaires to be sent:", 10],
	["Questionnaires pending:", 4],
	["PI Calls Needed:", 3],
	["Patients approaching protocol deviation for treatment delay:", 4],
	["Surgery Reports Outstanding:", 5]
];
foreach($rows as $i => $row) {
	$evenOdd = ($i % 2 == 1) ? 'odd' : 'even';
	$content .= "
		<tr class='$evenOdd'>
			<th>" . $row[0] . "</th>
			<td>" . $row[1] . "</td>
		</tr>";
}
unset($evenOdd, $i, $row);

// # add PI Calls Needed table
$content .= "
	</tbody>
</table>";

$table = [
	"title" => "PI Calls Needed",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "Event Name", "Approximate date PI call needed"],
	"content" => [
		["4-3285-119", "3-months", "10/30/18"],
		["10-2284-23", "12-months", "10/30/18"]
	]
];
global $dash;
$content .= $dash->makeDataTable($table);
unset($table);