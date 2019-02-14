<?php
if(!defined("ENVIRONMENT")) {
	if (is_file('/app001/victrcore/lib/Victr/Env.php')) include_once('/app001/victrcore/lib/Victr/Env.php');
	if (class_exists("Victr_Env")) {
		$envConf = Victr_Env::getEnvConf();

		if ($envConf[Victr_Env::ENV_CURRENT] === Victr_Env::ENV_PROD) {
			define("ENVIRONMENT", "PROD");
			define("SCREENING_PID", 63382);	// real project on prod");
			define("SUBJECT_PID", 63383);	// real project on prod");
			define("IMAGING_PID", 63384);	// real project on prod");
			// define("SUBJECT_PID", 73340);	// test project on prod");
		} elseif ($envConf[Victr_Env::ENV_CURRENT] === Victr_Env::ENV_DEV) {
			// define("ENVIRONMENT", "TEST");
			// define("SUBJECT_PID", N/A);
		}
		
		define("SUBJECT_RECORD_URL", APP_PATH_WEBROOT_FULL . "DataEntry/record_home.php?pid=" . SUBJECT_PID . "&id=");
		define("SCREENING_RECORD_URL", APP_PATH_WEBROOT_FULL . "DataEntry/index.php?pid=" . SCREENING_PID . "&page=screening_log&id=");
		define("IMAGING_RECORD_URL", APP_PATH_WEBROOT_FULL . "DataEntry/record_home.php?pid=" . IMAGING_PID . "&id=");
	} else {
		define("ENVIRONMENT", "DEV");
		// define("SUBJECT_PID", 13);		// @able
		// define("IMAGING_PID", 14);		// @able
		// define("SCREENING_PID", 15);	// @able
		define("SUBJECT_PID", 21);
		define("SCREENING_PID", 22);
		define("IMAGING_PID", 23);
		
		define("SUBJECT_RECORD_URL", substr(APP_PATH_WEBROOT_FULL, 0, -8) . APP_PATH_WEBROOT . "DataEntry/record_home.php?pid=" . SUBJECT_PID . "&id=");
		define("SCREENING_RECORD_URL", substr(APP_PATH_WEBROOT_FULL, 0, -8) . APP_PATH_WEBROOT . "DataEntry/index.php?pid=" . SCREENING_PID . "&page=screening_log&id=");
		define("IMAGING_RECORD_URL", substr(APP_PATH_WEBROOT_FULL, 0, -8) . APP_PATH_WEBROOT . "DataEntry/record_home.php?pid=" . IMAGING_PID . "&id=");
	}
}
