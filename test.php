<?php //encoding=utf-8
	//header('Content-Type: text/xml; charset=utf-8');
	require_once '../system/server/componentComposer.php';
	
	$appDir = './';
	$component = new Component($appDir);
	
	$component->setCType('View');
	$component->getDataDoc();
	$componentComposer = new ComponentComposer($appDir, $component);
	$focus = new Focus('ComodityList',1);
	$componentComposer->compose($focus);
	$componentComposer->show();

?>