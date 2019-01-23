<?php

global $dash;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// # prep PI Calls Needed table (so we can count # piCallsNeeded first)
$table = [
	"title" => "PI Calls Needed",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Approximate date PI call needed"],
	"content" => [
		// ["4-3285-119", "3-months", "10/30/18"],
		// ["10-2284-23", "12-months", "10/30/18"]
	]
];
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	// "filterLogic" => "[enrollment_arm_1][study_id] <> '' AND ([qtk_pi_call] = '1') AND ([qtk_pi_call_complete] = '')",
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);
$piCallsNeeded = 0;
foreach ($records as $i => $record) {
	foreach ($record as $eid => $data) {
		if ($data['qtk_pi_call'] == '1' and $data['qtk_pi_call_complete'] == '') {
			$piCallsNeeded++;
			$row = [];
			$row[0] = $record[$dash->enrollmentEID]['study_id'];
			$row[1] = $record[$dash->enrollmentEID]['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = $record[$dash->baselineEID]["qtk_call_due_4"];
			# print with label where possible, if not, print actual value
			
			$table['content'][] = $row;
		}
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# add Follow-Up Status Summary table
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
	["PI Calls Needed:", $piCallsNeeded],
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
$content .= "
	</tbody>
</table>";
unset($evenOdd, $i, $row);

# add PI Calls Needed table
$content .= $dash->makeDataTable($table);
unset($table);