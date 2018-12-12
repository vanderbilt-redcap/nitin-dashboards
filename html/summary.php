<?php

$content = "<table>
	<thead>
		<th>Follow-Up Status Summary</th>
	</thead>
	<tbody>";

# add table rows
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

foreach($rows as $row) {
	$content .= "
		<tr>
			<th>" . $row[0] . "</th>
			<td>" . $row[1] . "</td>
		</tr>";
}

$content .= "
	</tbody>
</table>
<h2>PI Calls Needed</h2>";