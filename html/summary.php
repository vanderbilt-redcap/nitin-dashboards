<?php

# make summary table
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

# add PI Calls Needed table
$content .= "
	</tbody>
</table>
<h2 class='redHeader'>PI Calls Needed</h2>
<table class='dataTable'>
	<thead>
		<tr>
			<th>Study ID</th>
			<th>Event Name</th>
			<th>Approximate date PI call needed:</th>
		</tr>
	</thead>
	<tbody>";
$rows = [
	["4-3285-119", "3-months", "10/30/18"],
	["10-2284-23", "12-months", "10/30/18"]
];
foreach($rows as $row) {
	$content .= "
		<tr>
			<td>" . $row[0] . "</td>
			<td>" . $row[1] . "</td>
			<td>" . $row[2] . "</td>
		</tr>";
}
$content .= "
	</tbody>";

unset($rows);