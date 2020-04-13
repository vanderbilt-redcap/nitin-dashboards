<?php
define("SUBJECT_PID", 123);
define("SCREENING_PID", 122);
define("IMAGING_PID", 124);
define("SUBJECT_RECORD_URL", APP_PATH_WEBROOT_FULL . substr(APP_PATH_WEBROOT, 1) . "DataEntry/record_home.php?pid=" . SUBJECT_PID . "&id=");
define("SCREENING_RECORD_URL", APP_PATH_WEBROOT_FULL . substr(APP_PATH_WEBROOT, 1) . "DataEntry/index.php?pid=" . SCREENING_PID . "&page=screening_log&id=");
define("IMAGING_RECORD_URL", APP_PATH_WEBROOT_FULL . substr(APP_PATH_WEBROOT, 1) . "DataEntry/record_home.php?pid=" . IMAGING_PID . "&id=");
