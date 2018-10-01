<?php //encoding=utf-8
	header('Content-Type: text/xml; charset=utf-8');
	require_once 'XMLManager.php';
	require_once '../../invoice/server/schema_comodityList.php';

	/*$m_comodityList->addFollowing(5, 'sum');
	$m_comodityList->addFollowing(2,'summary');
	$m_comodityList->addFollowing(2,'comodity');
	$m_comodityList->addFollowing(10,'sum');
	$m_comodityList->addFollowing(7, 'comodity');*/
	
	
	
	$base = Array();
	$base[0] = Array('position' => 1,'name' => 'comodityList', 'parent' => 0,  'previous' => 0, 'following' => 0, 'children' => Array(0 => 2,1 => 7,2 => 12, 3 => 16));
	$base[1] = Array('position' => 2, 'name' => 'comodity', 'parent' => 1, 'previous' => 0, 'following' => 7,'children' => Array(0 => 3,1 => 4));
	$base[2] = Array('position' => 3, 'name' => 'name', 'parent' => 2, 'previous' => 0, 'following' => 4, 'children' => Array());
	$base[3] = Array('position' => 4, 'name' => 'price', 'parent' => 2, 'previous' => 3, 'following' => 0, 'children' => Array(0 => 5, 1 => 6));
	$base[4] = Array('position' => 5, 'name' => 'one', 'parent' => 4, 'previous' => 0, 'following' => 6, 'children' => Array ());
	$base[5] = Array('position' => 6, 'name' => 'sum', 'parent' => 4, 'previous' => 5, 'following' => 0, 'children' => Array());
	$base[6] = Array('position' => 7, 'name' => 'comodity','parent' => 1,'previous' => 2, 'following' => 12, 'children' => Array(0 => 8,1 => 9));
	$base[7] = Array('position' => 8, 'name' => 'name','parent' => 7, 'previous' => 0, 'following' => 9,'children' => Array());
	$base[8] = Array('position' => 9,'name' => 'price', 'parent' => 7, 'previous' => 8, 'following' => 0,'children' => Array(0 => 10, 1=>11));
	$base[9] = Array('position' => 10, 'name' => 'one', 'parent' => 9, 'previous' => 0, 'following' => 11,'children' => Array());
	$base[10] = Array('position' => 11, 'name' => 'sum', 'parent' => 9, 'previous' => 10, 'following' => 0,'children' => Array());
	$base[11] = Array('position' => 12, 'name' => 'comodity','parent' => 1,'previous' => 7, 'following' => 16, 'children' => Array(0 => 13,1 => 14));
	$base[12] = Array('position' => 13, 'name' => 'name','parent' => 12, 'previous' => 0, 'following' => 14,'children' => Array());
	$base[13] = Array('position' => 14,'name' => 'price', 'parent' => 12, 'previous' => 13, 'following' => 0,'children' => Array(0 => 15));
	$base[14] = Array('position' => 15, 'name' => 'one', 'parent' => 14, 'previous' => 0, 'following' => 0,'children' => Array());
	$base[15] = Array('position' => 16, 'name' => 'summary','parent' => 1,'previous' => 12, 'following' => 0,'children' => Array(0 => 17,1 => 18));
	$base[16] = Array('position' => 17, 'name' => 'one', 'parent' => 16,'previous' => 0, 'following' => 18, 'children' => Array());
	$base[17] = Array('position' => 18,'name' => 'sum','parent' => 16, 'previous' => 17, 'following' => 0, 'children' => Array());
	
	/*$m_comodityList->fill('comodityList.xml');*/
	$m_comodityList->getXML();
	echo "<!--\n";
	$test = $m_comodityList->getArray();
	if($base == $test) echo 'ok';
	else {
		echo "ko\n";
		echo "base oproti test:\n";
		print_r(array_diff($base, $test));
		echo "test oproti base:\n";
		print_r(array_diff($test, $base));
		echo "test:\n";
		print_r($test);
	}
	echo "\n-->";
?>