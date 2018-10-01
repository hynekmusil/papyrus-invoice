<?php
	ob_implicit_flush() ;
	$env = new Db4Env();
	$env->open();
	$mgr = new XmlManager($env);
	$con = $mgr->createContainer("jd_template.dbxml");
	
	$handle = @fopen("jd_template.txt", "r");
	
	if ($handle) {
		while (!feof($handle)) {
			$templateUrl = fgets($handle, 4096);
			$content = file_get_contents($templateUrl);
			$con->putDocument($templateUrl, $content);
		}
		fclose($handle);
	}
	$con->close();
	$env->close();


?>