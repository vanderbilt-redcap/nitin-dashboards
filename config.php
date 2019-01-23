<?php
if(!defined("ENVIRONMENT")) {
	if (is_file('/app001/victrcore/lib/Victr/Env.php')) include_once('/app001/victrcore/lib/Victr/Env.php');
	if (class_exists("Victr_Env")) {
		$envConf = Victr_Env::getEnvConf();

		if ($envConf[Victr_Env::ENV_CURRENT] === Victr_Env::ENV_PROD) {
			define("ENVIRONMENT", "PROD");
			// define("PID", 63383);	// real project on prod");
			define("PID", 73340);	// test project on prod");
		} elseif ($envConf[Victr_Env::ENV_CURRENT] === Victr_Env::ENV_DEV) {
			// define("ENVIRONMENT", "TEST");
			// define("PID", 1210);
		}
	} else {
		define("ENVIRONMENT", "DEV");
		// define("PID", 21);	// @able
		define("PID", 21);
	}
}