<?php
global $dash,$Proj;

$Proj = new Project(123);
$report_id = 1063; // The report_id from the My Reports & Exports page
$export_format = 'csvlabels'; // 'csv' OR 'json' OR 'xml'
$report_data = DataExport::doReport($report_id, 'report', $export_format, true, true, true, false, false, false,
    false, false, false, false, false, array(), array(), false, false, false, true, true, "", "", "", true);
//$reportData = DataExport::doReport(1161,'report');
echo "Report Data:<br/>";
echo "<pre>";
print_r($report_data);
echo "</pre>";