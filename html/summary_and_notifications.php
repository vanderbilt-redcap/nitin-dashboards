<?php

global $dash;
$params = [
	"project_id" => $dash->pid,
	"return_format" => 'array',
	"exportDataAccessGroups" => true
];
$records = \REDCap::getData($params);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$table = [
	"title" => "PI Calls Needed",
	"titleClass" => "redHeader",
	"headers" => ["Study ID", "DAG", "Event Name", "Approximate date PI call needed"],
	"content" => []
];
$piCallsNeeded = 0;
foreach ($records as $i => $record) {
	$edata = $record[$dash->enrollmentEID];
	foreach ($record as $eid => $data) {
		if ($data['qtk_pi_call'] == '1' and $data['qtk_pi_call_complete'] == '') {
			$piCallsNeeded++;
			$row = [];
			$row[0] = "<a href = \"" . $dash->recordHome . "$i\">" . $edata['enrollment_id'] . "</a>-" . $edata['study_id'];
			$row[1] = $edata['pati_6'];
			$row[2] = $dash->projEvents[$eid];
			$row[3] = $data["qtk_call_due_4"];
			
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