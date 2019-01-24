<?php
if(!defined("ENVIRONMENT")) {
	if (is_file('/app001/victrcore/lib/Victr/Env.php')) include_once('/app001/victrcore/lib/Victr/Env.php');
	if (class_exists("Victr_Env")) {
		$envConf = Victr_Env::getEnvConf();

		if ($envConf[Victr_Env::ENV_CURRENT] === Victr_Env::ENV_PROD) {
			define("ENVIRONMENT", "PROD");
			define("SCREENING_PID", 63382);	// real project on prod");
			// define("SUBJECT_PID", 63383);	// real project on prod");
			define("IMAGING_PID", 63384);	// real project on prod");
			define("SUBJECT_PID", 73340);	// test project on prod");
		} elseif ($envConf[Victr_Env::ENV_CURRENT] === Victr_Env::ENV_DEV) {
			// define("ENVIRONMENT", "TEST");
			// define("SUBJECT_PID", N/A);
		}
	} else {
		define("ENVIRONMENT", "DEV");
		define("SUBJECT_PID", 13);		// @able
		define("IMAGING_PID", 14);		// @able
		define("SCREENING_PID", 15);	// @able
		// define("PID", 21);
	}
}