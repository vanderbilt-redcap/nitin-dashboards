<?php

require_once "../../redcap_connect.php";
require_once "config.php";

$data = \REDCap::getData(SUBJECT_PID, 'json');
print_r($data);

