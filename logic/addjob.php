<?php

	$title = "Add New Job";

	if ( $_POST['state'] == "addjob" ) {
		include "inc/init.php";

		mysql_queryf("BEGIN;");

		mysql_queryf("INSERT INTO jobs (user_id,name,created) VALUES(%u,%s,NOW());",
			$user_id, $_POST['job_name']);

		$job_id = mysql_insert_id();

		foreach ( $_POST['suites'] as $suite_num => $suite_name ) {
			if ( $suite_name ) {
				#echo "$suite_num " . $_POST['suites'][$suite_num] . " " . $_POST['urls'][$suite_num] . "<br>";
				mysql_queryf("INSERT INTO runs (job_id,name,url,created) VALUES(%u,%s,%s,NOW());",
					$job_id, $suite_name, $_POST['urls'][$suite_num]);

				$run_id = mysql_insert_id();

				$ua_type = "1 = 1";

				if ( $_POST['browsers'] == "popular" ) {
					$ua_type = "popular = 1";
				} else if ( $_POST['browsers'] == "current" ) {
					$ua_type = "current = 1";
				} else if ( $_POST['browsers'] == "gbs" ) {
					$ua_type = "gbs = 1";
				} else if ( $_POST['browsers'] == "beta" ) {
					$ua_type = "beta = 1";
				} else if ( $_POST['browsers'] == "popularbeta" ) {
					$ua_type = "(popular = 1 OR beta = 1)";
				}

				$result = mysql_queryf("SELECT id FROM useragents WHERE active = 1 AND $ua_type;");

				while ( $row = mysql_fetch_array($result) ) {
					$browser_num = $row[0];
					mysql_queryf("INSERT INTO run_useragent (run_id,useragent_id,max,created) VALUES(%u,%u,%u,NOW());",
						$run_id, $browser_num, $_POST['max']);
				}
			}
		}

		mysql_queryf("COMMIT;");

		$url = "/?state=jobstatus&job_id=" . $job_id;

		if ( $_POST['output'] == "dump" ) {
			echo $url;
		} else {
			header("Location: $url");
		}

		exit();
	}

?>