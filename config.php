<?php
use Vanderbilt\Victrlib\Env;
error_reporting(E_ALL & ~E_WARNING);

if(!defined("ENVIRONMENT")) {
    if (is_file('/app001/www/redcap/plugins/victrlib/src/Env.php'))
        include_once('/app001/www/redcap/plugins/victrlib/src/Env.php');
	if (class_exists("\\Vanderbilt\\Victrlib\\Env")) {
		//$envConf = Victr_Env::getEnvConf();

		if (Env::isProd()) {
			define("ENVIRONMENT", "PROD");
			define("SCREENING_PID", 63382);	// real project on prod");
			define("SUBJECT_PID", 63383);	// real project on prod");
			define("IMAGING_PID", 63384);	// real project on prod");
			// define("SUBJECT_PID", 73340);	// test project on prod");
		} elseif (Env::isStaging()) {
			define("ENVIRONMENT", "TEST");
			define("SCREENING_PID", 63382);	// real project on prod");
			define("SUBJECT_PID", 63383);	// real project on prod");
			define("IMAGING_PID", 63384);	// real project on prod");
		}
		
		define("SUBJECT_RECORD_URL", APP_PATH_WEBROOT_FULL . substr(APP_PATH_WEBROOT, 1) . "DataEntry/record_home.php?pid=" . SUBJECT_PID . "&id=");
		define("SCREENING_RECORD_URL", APP_PATH_WEBROOT_FULL . substr(APP_PATH_WEBROOT, 1) . "DataEntry/index.php?pid=" . SCREENING_PID . "&page=screening_log&id=");
		define("IMAGING_RECORD_URL", APP_PATH_WEBROOT_FULL . substr(APP_PATH_WEBROOT, 1) . "DataEntry/record_home.php?pid=" . IMAGING_PID . "&id=");
		
		function llog($text) {}
	} else {
		define("ENVIRONMENT", "DEV");
		define("SUBJECT_PID", 13);		// @able
		define("IMAGING_PID", 14);		// @able
		define("SCREENING_PID", 15);	// @able
		// define("SUBJECT_PID", 123);
		// define("SCREENING_PID", 122);
		// define("IMAGING_PID", 124);
		
		define("SUBJECT_RECORD_URL", substr(APP_PATH_WEBROOT_FULL, 0, -8) . APP_PATH_WEBROOT . "DataEntry/record_home.php?pid=" . SUBJECT_PID . "&id=");
		define("SCREENING_RECORD_URL", substr(APP_PATH_WEBROOT_FULL, 0, -8) . APP_PATH_WEBROOT . "DataEntry/index.php?pid=" . SCREENING_PID . "&page=screening_log&id=");
		define("IMAGING_RECORD_URL", substr(APP_PATH_WEBROOT_FULL, 0, -8) . APP_PATH_WEBROOT . "DataEntry/record_home.php?pid=" . IMAGING_PID . "&id=");
		
		file_put_contents("C:/vumc/log.txt", "nitin dev logging:\n");
		function llog($text) {
			file_put_contents("C:/vumc/log.txt", "$text\n", FILE_APPEND);
		}
	}
}
